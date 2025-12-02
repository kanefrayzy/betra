<x-layouts.app>

 <x-UI.transaction-tab/>

    <div class="table-container">
        <h2>{{ __('Транзакции') }}</h2>
        <table class="styled-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Hash</th>
                <th>User ID</th>
                <th>Amount</th>
                <th>Currency ID</th>
                <th>Type</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Context</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->hash }}</td>
                    <td>{{ $transaction->user_id }}</td>
                    <td>{{ moneyFormat($transaction->amount) }}</td>
                    <td>{{ $transaction->currency->symbol }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->status }}</td>
                    <td>{{ $transaction->created_at->format('d.m.Y  H:s:i') }}</td>
                    <td>{{ $transaction->context }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <style>
        .table-container {
            width: 90%;
            margin: 50px auto;
            padding: 20px;
            background: rgba(41, 41, 66, 0.29);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px 10px 0 0;
        }

        .table-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
            overflow: hidden;
        }

        .styled-table thead tr {

            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

    </style>
</x-layouts.app>
