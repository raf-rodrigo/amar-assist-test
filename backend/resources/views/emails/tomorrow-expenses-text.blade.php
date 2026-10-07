Olá, {{ $user->name }}!

Você possui despesas com vencimento amanhã, {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}:

@foreach ($expenses as $expense)
- {{ $expense->description }} ({{ $expense->category->description }}): R$ {{ number_format((float) $expense->amount, 2, ',', '.') }}
@endforeach

Total: R$ {{ number_format((float) $expenses->sum('amount'), 2, ',', '.') }}

Acesse suas despesas: {{ config('app.frontend_url') }}/expenses

Esta é uma mensagem automática do Gerenciador Financeiro Amar Assist.
