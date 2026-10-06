<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas que vencem amanhã</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f5f2; font-family:Arial, Helvetica, sans-serif; color:#1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f7f5f2;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 18px rgba(84,33,110,.12);">
                    <tr>
                        <td style="background:#54216e; padding:28px 32px; text-align:center;">
                            <div style="font-size:26px; line-height:32px; font-weight:700; color:#ffffff;">Gerenciador Financeiro</div>
                            <div style="margin-top:6px; font-size:14px; color:#dec4e8;">Amar Assist</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 14px; font-size:18px;">Olá, <strong>{{ $user->name }}</strong>!</p>
                            <p style="margin:0 0 24px; font-size:16px; line-height:24px; color:#475569;">
                                Este é um lembrete de que você possui despesas com vencimento amanhã,
                                <strong>{{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; border:1px solid #eee1f3; border-radius:10px; overflow:hidden;">
                                <tr style="background:#f7f1fa;">
                                    <th align="left" style="padding:12px; color:#54216e; font-size:13px;">Descrição</th>
                                    <th align="left" style="padding:12px; color:#54216e; font-size:13px;">Categoria</th>
                                    <th align="right" style="padding:12px; color:#54216e; font-size:13px;">Valor</th>
                                </tr>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td style="padding:13px 12px; border-top:1px solid #eee1f3; font-size:14px;">{{ $expense->description }}</td>
                                        <td style="padding:13px 12px; border-top:1px solid #eee1f3; font-size:14px; color:#64748b;">{{ $expense->category->description }}</td>
                                        <td align="right" style="padding:13px 12px; border-top:1px solid #eee1f3; font-size:14px; white-space:nowrap;">R$ {{ number_format((float) $expense->amount, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background:#fff6ed;">
                                    <td colspan="2" style="padding:14px 12px; border-top:2px solid #e7611c; font-weight:700; color:#54216e;">Total</td>
                                    <td align="right" style="padding:14px 12px; border-top:2px solid #e7611c; font-weight:700; color:#cd4c12; white-space:nowrap;">R$ {{ number_format((float) $expenses->sum('amount'), 2, ',', '.') }}</td>
                                </tr>
                            </table>

                            <div style="text-align:center; margin-top:28px;">
                                <a href="{{ config('app.frontend_url') }}/expenses" style="display:inline-block; background:#54216e; color:#ffffff; text-decoration:none; font-weight:700; padding:13px 24px; border-radius:9px;">Ver minhas despesas</a>
                            </div>

                            <p style="margin:28px 0 0; font-size:13px; line-height:20px; color:#64748b; text-align:center;">
                                Esta é uma mensagem automática. Você está recebendo este aviso porque possui uma despesa cadastrada para amanhã.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#eee1f3; padding:18px 24px; text-align:center; font-size:12px; color:#54216e;">
                            Teste Prático - Desenvolvedor PHP by Rafael Rodrigo Doimo &reg; {{ date('Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
