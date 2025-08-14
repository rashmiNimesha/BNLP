@extends('layouts.app')

@section('content')
    <title>BNLP</title>

    <!-- Main Section -->
    <div class="head-section">
        <div class="first-content">
            <h1 id="bnpl-title">Buy Now Pay Later</h1>
            <p id="bnpl-welcome">Welcome to the BNPL platform. Here you can manage your loans and payments easily.</p>
            <div class="btn-group">
                <a href="{{ route('generate') }}" class="btn btn-primary">Go to Generate Loans</a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
        <div class="second-content">
            <img src="{{ asset('images/buynowpaylater.png') }}" alt="BNPL Image" class="bnpl-img">
        </div>
    </div>

    <div class="features-wrapper">
        <h2 id="explore-features">Explore Our Features</h2>
        <div class="feature-list">
            <div class="feature-item">
                <h3>Buy Now, Pay Later</h3>
                <p>Flexible payment options to suit your needs.</p>
            </div>
            <div class="feature-item">
                <h3>Installments</h3>
                <p>Pay in easy installments over time.</p>
            </div>
            <div class="feature-item">
                <h3>Automatically updations</h3>
                <p>Stay updated with automatic notifications and reminders.</p>
            </div>
        </div>
    </div>
@endsection

