@extends('layouts.app')

@section('content')
    <h1 id= "sub-headings">Generate Loans</h1>
    <form id="generate-form">
        <div class="mb-3">
            <label for="loan_amount" class="form-label">Loan Amount</label>
            <input type="number" class="form-control" id="loan_amount" name="loan_amount" required min="1">
        </div>
        <div class="mb-3">
            <label for="number_of_loans" class="form-label">Number of Loans</label>
            <input type="number" class="form-control" id="number_of_loans" name="number_of_loans" required min="1">
        </div>
        <div class="mb-3">
            <label for="installments_per_loan" class="form-label">Installments per Loan (default 4)</label>
            <input type="number" class="form-control" id="installments_per_loan" name="installments_per_loan" min="1">
        </div>
        <div class="mb-3">
            <label for="installment_period_minutes" class="form-label">Installment Period (minutes)</label>
            <input type="number" class="form-control" id="installment_period_minutes" name="installment_period_minutes" required min="1">
        </div>
        <button type="submit" class="btn btn-primary">Generate</button>
    </form>
    <div id="response" class="mt-3"></div>
@endsection

@section('scripts')
    <script>
        document.getElementById('generate-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('/api/loans/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('response').innerHTML = '<div class="alert alert-success">Loans generated! Check dashboard.</div>';
                document.getElementById('generate-form').reset();
            })
            .catch(error => {
                document.getElementById('response').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            });
        });
    </script>
@endsection