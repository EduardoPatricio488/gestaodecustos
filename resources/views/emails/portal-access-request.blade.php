<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de acesso ao portal</title>
</head>
<body style="margin:0;background:#f4f4f5;font-family:Arial,sans-serif;color:#18181b;padding:32px 16px;">
    @php($isSupplier = $request->portal_type === 'supplier')
    @php($portalLabel = $isSupplier ? 'Fornecedor' : 'Cliente')
    <div style="max-width:620px;margin:0 auto;background:#fff;border:1px solid #e4e4e7;border-radius:24px;overflow:hidden;">
        <div style="padding:28px;background:#18181b;color:#fff;">
            <div style="font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;opacity:.7;">Finance Pro IA</div>
            <h1 style="font-size:24px;margin:10px 0 0;">Pedido de acesso ao Portal</h1>
        </div>
        <div style="padding:28px;">
            <p style="font-size:15px;line-height:1.6;margin-top:0;">Foi recebido um pedido para acesso ao Portal do {{ $portalLabel }} da <strong>{{ $workspace->legal_name ?: $workspace->name }}</strong>.</p>
            <div style="background:#f4f4f5;border-radius:16px;padding:18px;margin:22px 0;">
                <p style="margin:0 0 9px;"><strong>Nome:</strong> {{ $request->requester_name }}</p>
                <p style="margin:0 0 9px;"><strong>Email:</strong> {{ $request->requester_email }}</p>
                <p style="margin:0;"><strong>NIF:</strong> {{ $request->tax_number ?: 'Não indicado' }}</p>
            </div>
            <p style="font-size:13px;line-height:1.6;color:#52525b;">O pedido foi registado como pendente. Verifica os dados e, se reconheceres o pedido, cria/entrega as credenciais do portal através da área empresarial.</p>
        </div>
    </div>
</body>
</html>
