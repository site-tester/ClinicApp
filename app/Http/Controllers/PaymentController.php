<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Service;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Display payment form
     */
    public function create(Request $request)
    {
        $appointmentId = $request->get('appointment_id');
        $serviceIds = $request->get('services', []);

        $appointment = null;
        $services = collect();
        $total = 0;

        // If payment is for an appointment
        if ($appointmentId) {
            $appointment = Appointment::with('service')->findOrFail($appointmentId);
            $services->push($appointment->service);
            $total = $appointment->service->price;
        }

        // If payment is for individual services
        if (!empty($serviceIds)) {
            $selectedServices = Service::whereIn('id', $serviceIds)->get();
            $services = $services->merge($selectedServices);
            $total += $selectedServices->sum('price');
        }

        if ($services->isEmpty()) {
            return redirect()->back()->with('error', 'No services selected for payment.');
        }

        return view('patient.payments.create', compact('appointment', 'services', 'total'));
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'payment_method' => 'required|in:paypal,gcash',
        ]);

        try {
            DB::beginTransaction();

            $services = Service::whereIn('id', $request->services)->get();
            $total = $services->sum('price');

            // Create payment record
            $payment = Payment::create([
                'patient_id' => Auth::id(),
                'appointment_id' => $request->appointment_id,
                'amount' => $total,
                'currency' => 'PHP',
                'payment_method' => $request->payment_method,
                'description' => 'Payment for FRYDT Clinic Services',
                'status' => 'pending'
            ]);

            // Create payment items
            foreach ($services as $service) {
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $service->price,
                    'total_price' => $service->price
                ]);
            }

            DB::commit();

            // Handle different payment methods
            if ($request->payment_method === 'paypal') {
                return $this->processPayPalPayment($payment);
            } elseif ($request->payment_method === 'gcash') {
                return $this->processGcashPayment($payment);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment(Payment $payment)
    {
        try {
            $order = $this->paypalService->createOrder($payment);

            // Find the approval URL
            $approvalUrl = null;
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if ($approvalUrl) {
                return redirect($approvalUrl);
            } else {
                throw new \Exception('PayPal approval URL not found');
            }

        } catch (\Exception $e) {
            Log::error('PayPal payment error: ' . $e->getMessage());
            $payment->markAsFailed();

            return redirect()->route('patient.payments.show', $payment)
                ->with('error', 'PayPal payment failed. Please try again or contact support.');
        }
    }

    /**
     * Process GCash payment
     */
    private function processGcashPayment(Payment $payment)
    {
        try {
            // Generate GCash reference
            $gcashReference = 'GCASH-' . $payment->payment_reference;

            // Store reference in payment
            $payment->update([
                'gcash_reference' => $gcashReference,
            ]);

            return redirect()->route('patient.payments.show', $payment)
                ->with('success', 'GCash payment initiated. Please scan the QR code to complete payment.');

        } catch (\Exception $e) {
            Log::error('GCash payment error: ' . $e->getMessage());
            $payment->markAsFailed();

            return redirect()->route('patient.payments.show', $payment)
                ->with('error', 'GCash payment failed. Please try again or contact support.');
        }
    }


    /**
     * Handle successful PayPal payment
     */
    public function success(Request $request)
    {
        $orderId = $request->get('token');
        $payerId = $request->get('PayerID');

        if (!$orderId || !$payerId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Invalid payment response from PayPal.');
        }

        try {
            // Find payment by PayPal order ID
            $payment = Payment::where('paypal_order_id', $orderId)->firstOrFail();

            // Capture the payment
            $captureResponse = $this->paypalService->captureOrder($orderId);

            if ($captureResponse['status'] === 'COMPLETED') {
                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'paypal_payer_id' => $payerId,
                    'paypal_payment_id' => $captureResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                    'paypal_response' => $captureResponse,
                    'paid_at' => now()
                ]);

                // Update appointment status if applicable
                if ($payment->appointment) {
                    $payment->appointment->update(['status' => 'confirmed']);
                }

                return redirect()->route('patient.payments.show', $payment)
                    ->with('success', 'Payment completed successfully!');
            } else {
                $payment->markAsFailed();
                return redirect()->route('patient.payments.show', $payment)
                    ->with('error', 'Payment could not be completed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Payment success handling error: ' . $e->getMessage());
            return redirect()->route('patient.dashboard')
                ->with('error', 'Error processing payment. Please contact support.');
        }
    }

    /**
     * Handle cancelled PayPal payment
     */
    public function cancel(Request $request)
    {
        $orderId = $request->get('token');

        if ($orderId) {
            $payment = Payment::where('paypal_order_id', $orderId)->first();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);

                return redirect()->route('patient.payments.show', $payment)
                    ->with('warning', 'Payment was cancelled.');
            }
        }

        return redirect()->route('patient.dashboard')
            ->with('warning', 'Payment was cancelled.');
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        // $this->authorize('view', $payment);

        $payment->load(['patient', 'appointment.service', 'items.service']);

        return view('patient.payments.show', compact('payment'));
    }

    /**
     * List user's payments
     */
    public function index()
    {
        $payments = Payment::with(['appointment.service', 'items.service'])
            ->where('patient_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('patient.payments.index', compact('payments'));
    }
}
