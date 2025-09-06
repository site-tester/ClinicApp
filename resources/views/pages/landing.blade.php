@extends('layouts.app')

@section('content')
    <div style="background: linear-gradient(to right, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('{{ asset('images/homepage.png') }}') no-repeat center center; background-size: cover; ">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-center text-center py-5" style="min-height: 75vh;">
                <div class="col-12 col-md-6 text-md-end text-lg-start px-3 px-md-5">
                    <h3 class="display-5 fw-bold text-dark text-center">Welcome to Frydt Lying-in Clinic</h3>
                    <p class="lead text-dark mt-5">
                        We are your trusted partner in maternal and child healthcare. Your health and well-being are our top priority. Explore our services and let us guide you every step of the way. We’re here to assist you throughout your journey to motherhood. Let us take care of your healthcare needs with compassion and expertise.
                    </p>
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button class="btn bg-text-theme btn-lg mt-3 rounded">Get Started</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
