<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de acesso bancário</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
    <div style="max-width:620px;margin:40px auto;padding:0 20px;">
        <div style="background:#18181b;border-radius:22px 22px 0 0;padding:28px 32px;color:#fff;">
            <div style="font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;opacity:.75;">Finance Pro IA</div>
            <h1 style="margin:12px 0 0;font-size:24px;line-height:1.15;">Credenciais de acesso bancário</h1>
        </div>
        <div style="background:#fff;border:1px solid #e4e4e7;border-top:0;border-radius:0 0 22px 22px;padding:32px;">
            <p style="font-size:15px;line-height:1.7;margin-top:0;">A empresa autorizou o pedido de acesso bancário e enviou os dados necessários para autenticação.</p>

            <div style="background:#f4f4f5;border-radius:16px;padding:20px;margin:24px 0;">
                <p style="margin:0 0 8px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#71717a;">Empresa</p>
                <p style="margin:0;font-size:18px;font-weight:800;">{{ $workspace->legal_name ?: $workspace->name }}</p>
            </div>

            <div style="border:1px solid #e4e4e7;border-radius:16px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 6px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#71717a;">NIF da Empresa</p>
                <p style="margin:0;font-size:22px;font-weight:800;letter-spacing:2px;">{{ implode(' ', str_split(preg_replace('/\D/', '', (string) $workspace->tax_number), 3)) }}</p>
            </div>

            <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:16px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 6px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#047857;">Token de Auditoria</p>
                <p style="margin:0;font-size:28px;font-weight:900;letter-spacing:5px;color:#065f46;font-family:monospace;">{{ $token }}</p>
            </div>

            <p style="font-size:13px;line-height:1.7;color:#71717a;">O token é uma credencial de acesso. Deve ser tratado de forma confidencial e utilizado apenas pela entidade bancária que efetuou o pedido.</p>
            <p style="font-size:14px;line-height:1.7;margin-bottom:0;">Portal bancário: <a href="{{ url('/portal/banco') }}" style="color:#2563eb;font-weight:700;">{{ url('/portal/banco') }}</a></p>

            <div style="margin-top:28px;padding-top:20px;border-top:1px solid #e4e4e7;font-size:11px;color:#a1a1aa;">
                Mensagem automática enviada pelo Finance Pro IA.
            </div>
        </div>
    </div>
</body>
</html>
