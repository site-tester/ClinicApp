@extends('patient.layouts.app')

@section('title', 'Payment Details')

@section('content')
<style>
@media (max-width: 768px) {
    .payment-show-card {
        margin-bottom: 20px;
    }

    .table-responsive {
        font-size: 0.9rem;
    }

    .table td, .table th {
        padding: 0.5rem;
    }

    .alert {
        font-size: 0.9rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .card-header h4 {
        font-size: 1.2rem;
    }

    .stat-card .stat-number {
        font-size: 1.2rem;
    }

    .gcash-qr-section {
        padding: 15px;
    }

    .gcash-qr-section img {
        max-width: 200px !important;
    }
}
</style>

    <div class="container-fluid px-3 px-md-4 mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Payment Receipt
                        </h4>
                        <span
                            class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }} fs-6">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <h6 class="border-bottom pb-2">Payment Information</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Reference:</strong></p>
                                        <p class="text-muted small">{{ $payment->payment_reference }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Date:</strong></p>
                                        <p class="text-muted small">{{ $payment->created_at->format('M d, Y') }}</p>
                                        <p class="text-muted small">{{ $payment->created_at->format('g:i A') }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Method:</strong></p>
                                        <p class="mb-1">
                                            @if ($payment->payment_method === 'paypal')
                                                <i class="fab fa-paypal text-primary me-1"></i> PayPal
                                            @elseif($payment->payment_method === 'gcash')
                                                <i class="fas fa-qrcode text-success me-1"></i> GCash
                                            @else
                                                <i class="fas fa-credit-card text-info me-1"></i> Other
                                            @endif
                                        </p>
                                    </div>
                                    @if ($payment->paid_at)
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Paid At:</strong></p>
                                        <p class="text-muted small">{{ $payment->paid_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                    @endif
                                    @if ($payment->paypal_payment_id)
                                    <div class="col-12">
                                        <p class="mb-1"><strong>PayPal ID:</strong></p>
                                        <p class="text-muted small">{{ $payment->paypal_payment_id }}</p>
                                    </div>
                                    @endif
                                    @if ($payment->gcash_reference)
                                    <div class="col-12">
                                        <p class="mb-1"><strong>GCash Reference:</strong></p>
                                        <p class="text-muted small">{{ $payment->gcash_reference }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h6 class="border-bottom pb-2">Patient Information</h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <p class="mb-1"><strong>Name:</strong></p>
                                        <p class="text-muted">{{ $payment->patient->name }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="mb-1"><strong>Email:</strong></p>
                                        <p class="text-muted small">{{ $payment->patient->email }}</p>
                                    </div>
                                    @if ($payment->appointment)
                                    <div class="col-12">
                                        <p class="mb-1"><strong>Appointment:</strong></p>
                                        <p class="text-muted small">{{ $payment->appointment->appointment_datetime->format('M d, Y') }}</p>
                                        <p class="text-muted small">{{ $payment->appointment->appointment_datetime->format('g:i A') }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2">Services Paid</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->service_name }}</strong>
                                                @if ($item->service && $item->service->description)
                                                    <br><small class="text-muted">{{ $item->service->description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->formatted_unit_price }}</td>
                                            <td>{{ $item->formatted_total_price }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3">Total Amount</th>
                                        <th>{{ $payment->formatted_amount }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if ($payment->payment_method === 'gcash')
                            <div class="mt-4">
                                <h6 class="border-bottom pb-2">
                                    <i class="fas fa-qrcode me-2"></i>GCash Payment QR Code
                                </h6>
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-8 col-lg-6 gcash-qr-section">
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <img src="{{ asset('images/gcash-qr.png') }}"
                                                     alt="GCash QR Code"
                                                     class="img-fluid border rounded shadow-sm"
                                                     style="max-width: 250px; width: 100%; height: auto;"
                                                     onerror="this.src='{{ asset('images/gcash-placeholder.png') }}'">
                                            </div>
                                            <div class="alert alert-success">
                                                <h6 class="alert-heading mb-2">
                                                    <i class="fas fa-info-circle me-2"></i>Payment Instructions
                                                </h6>
                                                <ol class="text-start mb-2 small">
                                                    <li>Open your GCash app</li>
                                                    <li>Tap the QR scanner</li>
                                                    <li>Scan this QR code</li>
                                                    <li>Enter the exact amount: <strong>{{ $payment->formatted_amount }}</strong></li>
                                                    <li>Use reference: <strong>{{ $payment->gcash_reference }}</strong></li>
                                                </ol>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="p-2 bg-light rounded">
                                                        <small class="text-muted d-block">Amount</small>
                                                        <strong class="text-success">{{ $payment->formatted_amount }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-light rounded">
                                                        <small class="text-muted d-block">Reference</small>
                                                        <strong class="text-primary">{{ $payment->gcash_reference }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($payment->notes)
                            <div class="mt-3">
                                <h6 class="border-bottom pb-2">Notes</h6>
                                <p class="text-muted">{{ $payment->notes }}</p>
                            </div>
                        @endif

                        <div class="row mt-4 g-2">
                            <div class="col-12 col-md-6">
                                <a href="{{ route('patient.payments.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-list me-2"></i>View All Payments
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex gap-2 flex-column flex-md-row">
                                    @if ($payment->status === 'completed')
                                        <button class="btn btn-success flex-fill" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>Print Receipt
                                        </button>
                                    @endif
                                    @if ($payment->status === 'pending' && in_array($payment->payment_method, ['paypal', 'gcash']))
                                        <a href="{{ route('patient.payments.create', ['services' => $payment->items->pluck('service_id')]) }}"
                                            class="btn btn-primary flex-fill">
                                            <i class="fas fa-redo me-2"></i>Retry Payment
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
