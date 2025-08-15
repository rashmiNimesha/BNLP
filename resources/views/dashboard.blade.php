@extends('layouts.app')

@section('content')
    <h1 id="sub-headings">Loan Dashboard</h1>
    <div class="mb-3" style="text-align:right;">
        <input type="text" id="status-search" placeholder="Search by status (e.g. pending)" class="form-control" style="width:200px;display:inline-block;">
        <button id="search-btn" class="btn btn-primary btn-sm" style="margin-left:8px;">Search</button>
        <button id="reset-btn" class="btn btn-secondary btn-sm" style="margin-left:4px;">Reset</button>
    </div>
    <table class="table table-striped" id="loans-table">
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Total Amount</th>
                <th>Installments Paid / Total</th>
                <th>Total Amount Paid</th>
                <th>Status</th>
                <th>Next Payment Due</th>
                <th>Actions</th>
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
                    <td>
                        <button class="btn btn-sm btn-primary view-graph-btn" data-loan-id="{{ $loan->id }}">View Graph</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="chart-container" style="display:none; margin-top:2rem; text-align:center;">
        <canvas id="installment-chart" width="250" height="250" style="max-width:400px; max-height:400px;"></canvas>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const loanInstallmentData = {
            @foreach($loans as $loan)
                "{{ $loan->id }}": {
                    paid: {{ $loan->installmentsPaidCount() }},
                    pending: {{ $loan->installments->count() - $loan->installmentsPaidCount() }},
                    total: {{ $loan->installments->count() }},
                    amountPaid: {{ $loan->totalPaid() }},
                    amountPending: {{ $loan->amount - $loan->totalPaid() }}
                },
            @endforeach
        };

        let chartInstance = null;

        document.querySelectorAll('.view-graph-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const loanId = this.getAttribute('data-loan-id');
                const data = loanInstallmentData[loanId];
                if (!data) return;

                document.getElementById('chart-container').style.display = 'block';

                const ctx = document.getElementById('installment-chart').getContext('2d');
                if (chartInstance) {
                    chartInstance.destroy();
                }
                chartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [
                            `Paid (${data.paid} installments, LKR ${data.amountPaid})`,
                            `Pending (${data.pending} installments, LKR ${data.amountPending})`
                        ],
                        datasets: [{
                            data: [data.paid, data.pending],
                            backgroundColor: ['#89c2d9', '#dee2e6'],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            title: {
                                display: true,
                                text: `Loan #${loanId} Installment Status`
                            }
                        }
                    }
                });
            });
        });

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
                    <td>
                        <button class="btn btn-sm btn-primary view-graph-btn" data-loan-id="${loan.id}">View Graph</button>
                    </td>
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

        // Loan status search/filter logic
        document.getElementById('search-btn').addEventListener('click', function() {
            const query = document.getElementById('status-search').value.trim().toLowerCase();
            document.querySelectorAll('#loans-table tbody tr').forEach(row => {
                const status = row.querySelector('td:nth-child(5)').textContent.trim().toLowerCase();
                row.style.display = query && status !== query ? 'none' : '';
            });
        });
        document.getElementById('reset-btn').addEventListener('click', function() {
            document.getElementById('status-search').value = '';
            document.querySelectorAll('#loans-table tbody tr').forEach(row => {
                row.style.display = '';
            });
        });
    </script>
@endsection