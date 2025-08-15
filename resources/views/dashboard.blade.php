@extends('layouts.app')

@section('content')
    <h1 id="sub-headings">Loan Dashboard</h1>
    <table class="table table-striped" id="loans-table">
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Total Amount</th>
                <th>Installments Paid / Total</th>
                <th>Total Amount Paid</th>
                <th>Status</th>
                <th>Next Payment Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr data-loan-id="{{ $loan->id }}">
                    <td>{{ $loan->id }}</td>
                    <td>{{ $loan->amount }}</td>
                    <td>{{ $loan->installmentsPaidCount() }} / {{ $loan->installments->count() }}</td>
                    <td>{{ $loan->totalPaid() }}</td>
                    <td>{{ $loan->status }}</td>
                    <td>
                        {{
                            $loan->nextDueDate()
                                ? $loan->nextDueDate()->format('Y-m-d H:i')
                                : 'N/A'
                        }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    <script>
        Echo.channel('loans')
            .listen('LoanGenerated', (e) => {
                const loan = e.loan;
                const row = document.createElement('tr');
                row.setAttribute('data-loan-id', loan.id);
                row.innerHTML = `
                    <td>${loan.id}</td>
                    <td>${loan.amount}</td>
                    <td>0 / ${loan.installments ? loan.installments.length : 0}</td>
                    <td>0</td>
                    <td>${loan.status}</td>
                    <td>${loan.installments && loan.installments.length > 0 ? loan.installments[0].due_date : 'N/A'}</td>
                `;
                document.querySelector('#loans-table tbody').appendChild(row);
            })
            .listen('InstallmentPaid', (e) => {
                const loanId = e.installment.loan_id;
                const row = document.querySelector(`tr[data-loan-id="${loanId}"]`);
                if (row) {
                    let paidCount = parseInt(row.querySelector('td:nth-child(3)').textContent.split(' / ')[0]) + 1;
                    let totalCount = row.querySelector('td:nth-child(3)').textContent.split(' / ')[1];
                    let totalPaid = parseFloat(row.querySelector('td:nth-child(4)').textContent) + parseFloat(e.installment.amount);
                    row.querySelector('td:nth-child(3)').textContent = `${paidCount} / ${totalCount}`;
                    row.querySelector('td:nth-child(4)').textContent = totalPaid;
                    
                }
            })
            .listen('LoanCompleted', (e) => {
                const loanId = e.loan.id;
                const row = document.querySelector(`tr[data-loan-id="${loanId}"]`);
                if (row) {
                    row.querySelector('td:nth-child(5)').textContent = 'completed';
                }
            });
    </script>
@endsection