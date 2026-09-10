<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de acesso bancário</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
    <div style="max-width:620px;margin:40px auto;padding:0 20px;">
        <div style="background:#18181b;border-radius:22px 22px 0 0;padding:28px 32px;color:#fff;">
            <div style="font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;opacity:.75;">Finance Pro IA</div>
            <h1 style="margin:12px 0 0;font-size:24px;line-height:1.15;">Pedido de acesso bancário</h1>
        </div>
        <div style="background:#fff;border:1px solid #e4e4e7;border-top:0;border-radius:0 0 22px 22px;padding:32px;">
            <p style="font-size:15px;line-height:1.7;margin-top:0;">Foi recebido um pedido de acesso à área bancária da sua empresa.</p>
            <div style="background:#f4f4f5;border-radius:16px;padding:20px;margin:24px 0;">
                <p style="margin:0 0 8px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#71717a;">Empresa</p>
                <p style="margin:0;font-size:18px;font-weight:800;">{{ $workspace->legal_name ?: $workspace->name }}</p>
                <p style="margin:8px 0 0;font-size:13px;color:#71717a;">NIF: {{ implode(' ', str_split(preg_replace('/\D/', '', (string) $workspace->tax_number), 3)) }}</p>
            </div>
            <div style="border:1px solid #e4e4e7;border-radius:16px;padding:20px;margin:20px 0;">
                <p style="margin:0 0 8px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#71717a;">Entidade solicitante</p>
                <p style="margin:0;font-size:17px;font-weight:800;">{{ $request->bank_name }}</p>
                <p style="margin:6px 0 0;font-size:14px;color:#52525b;">{{ $request->bank_email }}</p>
            </div>
            <p style="font-size:14px;line-height:1.7;">Entra na área <strong>Banco</strong> da Finance Pro IA para verificar o pedido. Só deves enviar as credenciais depois de confirmares que reconheces a entidade solicitante.</p>
            <p style="font-size:13px;line-height:1.7;color:#71717a;">Por segurança, não respondas a este email com outras credenciais bancárias. O Finance Pro IA só utiliza o pedido para partilhar o NIF e o Token de Auditoria da aplicação.</p>
            <div style="margin-top:28px;padding-top:20px;border-top:1px solid #e4e4e7;font-size:11px;color:#a1a1aa;">
                Mensagem automática enviada pelo Finance Pro IA.
            </div>
        </div>
    </div>
</body>
</html>
