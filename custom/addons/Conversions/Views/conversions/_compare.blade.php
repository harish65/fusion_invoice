<table class="table table-striped">
    <thead>
    <tr>
        <th>Quote</th>
        <th>Total Before</th>
        <th>Total After</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($comparisons['quotes'] as $quote)
        <tr>
            <td>{{ $quote['number'] }}</td>
            <td>{{ $quote['prev_total'] }}</td>
            <td>{{ $quote['total'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Invoice</th>
        <th>Total Before</th>
        <th>Total After</th>
        <th>Paid Before</th>
        <th>Paid After</th>
        <th>Balance Before</th>
        <th>Balance After</th>
        <th>Difference</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($comparisons['invoices'] as $invoice)
        <tr>
            <td>{{ $invoice['number'] }}</td>
            <td>{{ $invoice['prev_total'] }}</td>
            <td>{{ $invoice['total'] }}</td>
            <td>{{ $invoice['prev_paid'] }}</td>
            <td>{{ $invoice['paid'] }}</td>
            <td>{{ $invoice['prev_balance'] }}</td>
            <td>{{ $invoice['balance'] }}</td>
            <td>{{ $invoice['difference'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>