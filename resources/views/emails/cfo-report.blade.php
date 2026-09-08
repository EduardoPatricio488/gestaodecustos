<!DOCTYPE html>
<html lang="pt">
<body style="margin: 0; padding: 0; background-color: #f4f7f6; color: #18212f; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f7f6;">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 640px; background-color: #ffffff; border: 1px solid #dce5e2;">
                    <tr>
                        <td style="padding: 28px 32px; background-color: #18181b; color: #ffffff;">
                            <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #34d399;">{{ config('app.name') }} · CFO Inteligente</p>
                            <h1 style="margin: 0; font-size: 26px; line-height: 34px; font-weight: 700;">O teu Relatório Financeiro</h1>
                            <p style="margin: 10px 0 0; font-size: 14px; line-height: 20px; color: #a1a1aa;">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px 32px 12px;">
                            <h2 style="margin: 0 0 8px; font-size: 22px; line-height: 30px; font-weight: 700;">Olá, {{ $user->name }}.</h2>
                            <p style="margin: 0; color: #52616b; font-size: 15px; line-height: 24px;">O teu consultor financeiro processou os teus dados. Aqui está o diagnóstico deste mês.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 24px 8px;">
                            <table role="presentation" width="100%" cellspacing="8" cellpadding="0" border="0">
                                <tr>
                                    <td width="33.33%" style="padding: 16px; background-color: #ecfdf5; border: 1px solid #a7f3d0; vertical-align: top;">
                                        <p style="margin: 0 0 8px; color: #047857; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Receitas</p>
                                        <p style="margin: 0; color: #065f46; font-size: 20px; line-height: 26px; font-weight: 700;">{{ number_format($stats['earned'], 2, ',', '.') }} &euro;</p>
                                    </td>
                                    <td width="33.33%" style="padding: 16px; background-color: #fff7ed; border: 1px solid #fed7aa; vertical-align: top;">
                                        <p style="margin: 0 0 8px; color: #c2410c; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Despesas</p>
                                        <p style="margin: 0; color: #9a3412; font-size: 20px; line-height: 26px; font-weight: 700;">{{ number_format($stats['spent'], 2, ',', '.') }} &euro;</p>
                                    </td>
                                    <td width="33.33%" style="padding: 16px; background-color: {{ $stats['healthScore'] >= 50 ? '#eff6ff' : '#fef2f2' }}; border: 1px solid {{ $stats['healthScore'] >= 50 ? '#bfdbfe' : '#fecaca' }}; vertical-align: top;">
                                        <p style="margin: 0 0 8px; color: {{ $stats['healthScore'] >= 50 ? '#1d4ed8' : '#b91c1c' }}; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Saúde Financeira</p>
                                        <p style="margin: 0; color: {{ $stats['healthScore'] >= 50 ? '#1e3a8a' : '#991b1b' }}; font-size: 20px; line-height: 26px; font-weight: 700;">{{ $stats['healthScore'] }}%</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 32px 32px; color: #374151; font-size: 14px; line-height: 24px;">
                            {!! Str::markdown($analysis) !!}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 32px; background-color: #f4f7f6; border-top: 1px solid #dce5e2; text-align: center;">
                            <a href="{{ route('ai') }}" style="display: inline-block; padding: 12px 28px; background-color: #18181b; color: #ffffff; font-size: 12px; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;">Ver no {{ config('app.name') }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
