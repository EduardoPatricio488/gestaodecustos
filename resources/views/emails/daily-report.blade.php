<!DOCTYPE html>
<html lang="pt">
<body style="margin: 0; padding: 0; background-color: #f4f7f6; color: #18212f; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f7f6;">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 640px; background-color: #ffffff; border: 1px solid #dce5e2;">
                    <tr>
                        <td style="padding: 28px 32px; background-color: #0f766e; color: #ffffff;">
                            <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase;">{{ config('app.name') }}</p>
                            <h1 style="margin: 0; font-size: 26px; line-height: 34px; font-weight: 700;">Resumo financeiro diário</h1>
                            <p style="margin: 10px 0 0; font-size: 14px; line-height: 20px; color: #ccfbf1;">{{ $data['date']->translatedFormat('l, d \d\e F \d\e Y') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px 32px 12px;">
                            <h2 style="margin: 0 0 8px; font-size: 22px; line-height: 30px; font-weight: 700;">Olá, {{ $user->name }}.</h2>
                            <p style="margin: 0; color: #52616b; font-size: 15px; line-height: 24px;">Aqui está o movimento da tua conta no dia anterior.</p>
                        </td>
                    </tr>

                    @if (array_intersect(['earned', 'spent', 'balance'], $data['sections']))
                        <tr>
                            <td style="padding: 20px 24px 8px;">
                                <table role="presentation" width="100%" cellspacing="8" cellpadding="0" border="0">
                                    <tr>
                                        @if (in_array('earned', $data['sections']))
                                            <td width="33.33%" style="padding: 16px; background-color: #ecfdf5; border: 1px solid #a7f3d0; vertical-align: top;">
                                                <p style="margin: 0 0 8px; color: #047857; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Receitas</p>
                                                <p style="margin: 0; color: #065f46; font-size: 20px; line-height: 26px; font-weight: 700;">{{ number_format($data['earned'], 2, ',', '.') }} &euro;</p>
                                            </td>
                                        @endif
                                        @if (in_array('spent', $data['sections']))
                                            <td width="33.33%" style="padding: 16px; background-color: #fff7ed; border: 1px solid #fed7aa; vertical-align: top;">
                                                <p style="margin: 0 0 8px; color: #c2410c; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Despesas</p>
                                                <p style="margin: 0; color: #9a3412; font-size: 20px; line-height: 26px; font-weight: 700;">{{ number_format($data['spent'], 2, ',', '.') }} &euro;</p>
                                            </td>
                                        @endif
                                        @if (in_array('balance', $data['sections']))
                                            <td width="33.33%" style="padding: 16px; background-color: {{ $data['balance'] >= 0 ? '#eff6ff' : '#fef2f2' }}; border: 1px solid {{ $data['balance'] >= 0 ? '#bfdbfe' : '#fecaca' }}; vertical-align: top;">
                                                <p style="margin: 0 0 8px; color: {{ $data['balance'] >= 0 ? '#1d4ed8' : '#b91c1c' }}; font-size: 11px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;">Saldo</p>
                                                <p style="margin: 0; color: {{ $data['balance'] >= 0 ? '#1e3a8a' : '#991b1b' }}; font-size: 20px; line-height: 26px; font-weight: 700;">{{ number_format($data['balance'], 2, ',', '.') }} &euro;</p>
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if (in_array('categories', $data['sections']))
                        <tr>
                            <td style="padding: 24px 32px 8px;">
                                <h3 style="margin: 0 0 14px; color: #18212f; font-size: 17px; line-height: 24px;">Despesas por categoria</h3>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #dce5e2;">
                                    @forelse ($data['categoryStats'] as $item)
                                        <tr>
                                            <td style="padding: 13px 16px; border-bottom: 1px solid #dce5e2; color: #374151; font-size: 14px; line-height: 20px;">{{ $item->name }}</td>
                                            <td align="right" style="padding: 13px 16px; border-bottom: 1px solid #dce5e2; color: #18212f; font-size: 14px; font-weight: 700; line-height: 20px;">{{ number_format($item->total, 2, ',', '.') }} &euro;</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td style="padding: 18px 16px; color: #6b7280; font-size: 14px; line-height: 20px;">Sem despesas registadas neste dia.</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td align="center" style="padding: 28px 32px 32px;">
                            <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 13px 22px; background-color: #0f766e; color: #ffffff; font-size: 14px; font-weight: 700; line-height: 20px; text-decoration: none;">Abrir dashboard</a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px 32px; border-top: 1px solid #dce5e2; color: #6b7280; font-size: 12px; line-height: 18px;">
                            Recebeste este email porque ativaste os relatórios diários nas definições da tua conta.<br>
                            {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
