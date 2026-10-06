<h1>Despesas que vencem amanhã</h1>
<p>As despesas abaixo vencem em {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}:</p>
<ul>
    @foreach ($expenses as $expense)
        <li>{{ $expense->description }} — R$ {{ number_format((float) $expense->amount, 2, ',', '.') }}</li>
    @endforeach
</ul>
