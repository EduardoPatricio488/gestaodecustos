const categoryLabels = {
    alimentacao: '🍔 Alimentação',
    transporte: '🚗 Transporte',
    casa: '🏠 Casa',
    compras: '🛒 Compras',
    entretenimento: '🎮 Entretenimento',
    saude: '💊 Saúde',
    seguros: '🛡️ Seguros',
    tecnologia: '💻 Tecnologia',
    educacao: '📚 Educação',
    outro: '📦 Outro',
};

const money = new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' });

function esc(value) {
    const node = document.createElement('span');
    node.textContent = String(value ?? '');
    return node.innerHTML;
}

function formatDate(value) {
    try {
        return new Intl.DateTimeFormat('pt-PT', {
            day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit',
        }).format(new Date(value));
    } catch {
        return 'Agora';
    }
}

function formatDuration(start) {
    if (!start) return 'agora';
    const minutes = Math.max(0, Math.floor((Date.now() - Number(start)) / 60000));
    if (minutes < 60) return `${minutes} min`;
    const hours = Math.floor(minutes / 60);
    const remaining = minutes % 60;
    return remaining ? `${hours}h ${remaining}min` : `${hours}h`;
}

function beginOfflineSession() {
    if (!localStorage.getItem('finance-pro-offline-started-at')) {
        localStorage.setItem('finance-pro-offline-started-at', String(Date.now()));
    }
}

function mountBunkerOverlay() {
    const bunker = document.querySelector('[x-show="!isOnline"]');
    if (!bunker || bunker.dataset.bunkerEnhanced === '1') return;
    bunker.dataset.bunkerEnhanced = '1';

    bunker.innerHTML = `
        <style>
            .fp-bunker { min-height:100%; background:linear-gradient(180deg,#09090b 0%,#111113 100%); color:#fafafa; }
            .fp-bunker * { box-sizing:border-box; }
            .fp-bunker button,.fp-bunker input,.fp-bunker select { font:inherit; }
            .fp-bunker .wrap { width:min(1180px,100%); margin:auto; padding:18px; }
            .fp-bunker .top { position:sticky;top:0;z-index:20;background:rgba(9,9,11,.9);backdrop-filter:blur(18px);border-bottom:1px solid #27272a; }
            .fp-bunker .topbar { display:flex;align-items:center;justify-content:space-between;gap:14px;padding:15px 18px; }
            .fp-bunker .brand { display:flex;align-items:center;gap:11px; }
            .fp-bunker .logo { width:42px;height:42px;border-radius:14px;display:grid;place-items:center;background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 12px 30px rgba(16,185,129,.2); }
            .fp-bunker .eyebrow { font-size:9px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#71717a; }
            .fp-bunker h1 { margin:2px 0 0;font-size:18px;font-weight:950;letter-spacing:-.04em; }
            .fp-bunker .status { display:flex;align-items:center;gap:8px;border:1px solid #3f3f46;background:#18181b;border-radius:999px;padding:8px 11px;font-size:10px;font-weight:900; }
            .fp-bunker .dot { width:8px;height:8px;border-radius:999px;background:#ef4444;box-shadow:0 0 0 4px rgba(239,68,68,.12); }
            .fp-bunker .hero { padding:24px 0 18px; }
            .fp-bunker .hero h2 { margin:5px 0 0;font-size:clamp(30px,5vw,52px);line-height:.98;letter-spacing:-.07em;font-weight:950; }
            .fp-bunker .hero p { max-width:720px;margin:12px 0 0;color:#a1a1aa;font-size:13px;line-height:1.6; }
            .fp-bunker .grid { display:grid;grid-template-columns:minmax(0,1.45fr) minmax(290px,.75fr);gap:14px; }
            .fp-bunker .card { border:1px solid #27272a;background:rgba(24,24,27,.9);border-radius:22px;padding:18px;box-shadow:0 18px 55px rgba(0,0,0,.18); }
            .fp-bunker .card h3 { margin:0;font-size:13px;font-weight:950; }
            .fp-bunker .muted { color:#a1a1aa;font-size:11px;line-height:1.55; }
            .fp-bunker .fields { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px; }
            .fp-bunker .field.full { grid-column:1/-1; }
            .fp-bunker label { display:block;margin-bottom:6px;color:#d4d4d8;font-size:10px;font-weight:850; }
            .fp-bunker input,.fp-bunker select { width:100%;min-height:44px;padding:10px 12px;border:1px solid #3f3f46;border-radius:13px;background:#09090b;color:#fff;outline:none; }
            .fp-bunker input:focus,.fp-bunker select:focus { border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.12); }
            .fp-bunker .amount { min-height:66px;font-size:32px;font-weight:950;letter-spacing:-.05em; }
            .fp-bunker .actions { display:flex;gap:8px;flex-wrap:wrap;margin-top:12px; }
            .fp-bunker .btn { border:1px solid #3f3f46;background:#18181b;color:#fafafa;border-radius:12px;padding:10px 13px;font-size:10px;font-weight:900; }
            .fp-bunker .btn.primary { border-color:#10b981;background:#10b981;color:#03251a; }
            .fp-bunker .btn.danger { border-color:#7f1d1d;color:#fca5a5; }
            .fp-bunker .chips { display:flex;gap:7px;flex-wrap:wrap;margin-top:11px; }
            .fp-bunker .chip { border:1px solid #3f3f46;background:#18181b;color:#d4d4d8;border-radius:999px;padding:7px 9px;font-size:10px;font-weight:850; }
            .fp-bunker .stats { display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px; }
            .fp-bunker .stat { border:1px solid #27272a;background:#09090b;border-radius:14px;padding:11px; }
            .fp-bunker .stat span { display:block;color:#71717a;font-size:8px;font-weight:850;text-transform:uppercase;letter-spacing:.08em; }
            .fp-bunker .stat strong { display:block;margin-top:4px;font-size:17px;font-weight:950;letter-spacing:-.04em; }
            .fp-bunker .list { display:grid;gap:7px;margin-top:11px; }
            .fp-bunker .row { display:flex;align-items:center;justify-content:space-between;gap:10px;border:1px solid #27272a;background:#09090b;border-radius:14px;padding:10px; }
            .fp-bunker .row-main { min-width:0; }
            .fp-bunker .row-title { overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:10px;font-weight:900; }
            .fp-bunker .row-meta { margin-top:3px;color:#71717a;font-size:9px; }
            .fp-bunker .row-value { white-space:nowrap;font-size:11px;font-weight:950; }
            .fp-bunker .badge { display:inline-flex;margin-top:4px;border-radius:999px;padding:3px 6px;background:#27272a;color:#d4d4d8;font-size:8px;font-weight:900; }
            .fp-bunker .empty { padding:22px 10px;text-align:center;color:#71717a;font-size:10px; }
            .fp-bunker .notice { position:fixed;right:18px;bottom:18px;z-index:40;max-width:360px;padding:12px 14px;border:1px solid #3f3f46;border-radius:14px;background:#18181b;box-shadow:0 18px 50px rgba(0,0,0,.4);font-size:10px;font-weight:900; }
            .fp-bunker .danger-zone { border-color:#3f2020; }
            .fp-bunker .storage { height:7px;margin-top:8px;background:#27272a;border-radius:999px;overflow:hidden; }
            .fp-bunker .storage > div { height:100%;width:0;background:#10b981;border-radius:999px;transition:width .25s ease; }
            @media(max-width:820px){ .fp-bunker .grid{grid-template-columns:1fr}.fp-bunker .fields{grid-template-columns:1fr}.fp-bunker .wrap{padding:12px}.fp-bunker .topbar{padding:12px}.fp-bunker .status{font-size:9px}.fp-bunker .hero{padding-top:18px} }
        </style>
        <div class="fp-bunker">
            <div class="top"><div class="topbar wrap">
                <div class="brand"><div class="logo">🔐</div><div><div class="eyebrow">Finance Pro IA · Modo Local</div><h1>Bunker Offline</h1></div></div>
                <div class="status"><span class="dot"></span><span>Sem ligação · Offline há <strong data-offline-duration>agora</strong></span></div>
            </div></div>
            <div class="wrap">
                <section class="hero"><div class="eyebrow">Central Offline Financeira</div><h2>A tua gestão financeira<br>não fica offline.</h2><p>Regista despesas, consulta o histórico e prepara a sincronização. Tudo o que é criado aqui permanece neste dispositivo até a ligação ser restaurada.</p></section>
                <div class="grid">
                    <section class="card"><h3>Registo rápido</h3><div class="muted">O mínimo de passos para registar uma despesa.</div>
                        <form data-bunker-form><div class="fields">
                            <div class="field full"><label for="bunker-amount">Valor</label><input id="bunker-amount" class="amount" type="number" min="0.01" step="0.01" inputmode="decimal" placeholder="0,00 €" required></div>
                            <div class="field full"><label for="bunker-title">Descrição / Local</label><input id="bunker-title" maxlength="255" placeholder="Continente, Uber, Café..." required></div>
                            <div class="field"><label for="bunker-category">Categoria</label><select id="bunker-category">${Object.entries(categoryLabels).map(([value,label])=>`<option value="${value}">${label}</option>`).join('')}</select></div>
                            <div class="field"><label for="bunker-payment">Método de pagamento</label><select id="bunker-payment"><option value="cartao">Cartão</option><option value="mbway">MB WAY</option><option value="dinheiro">Dinheiro</option><option value="transferencia">Transferência</option><option value="outro">Outro</option></select></div>
                            <div class="field"><label for="bunker-date">Data</label><input id="bunker-date" type="datetime-local"></div>
                            <div class="field"><label for="bunker-notes">Notas</label><input id="bunker-notes" maxlength="500" placeholder="Opcional"></div>
                        </div><div class="actions"><button class="btn primary" type="submit">Guardar no Bunker</button><button class="btn" type="button" data-clear-form>Limpar</button></div></form>
                        <div class="chips">${Object.entries(categoryLabels).slice(0,6).map(([value,label])=>`<button type="button" class="chip" data-category="${value}">${label}</button>`).join('')}</div>
                    </section>
                    <aside class="card"><h3>🔐 Cofre Offline</h3><div class="muted">Estado real do armazenamento local.</div><div class="stats"><div class="stat"><span>Registos</span><strong data-count>0</strong></div><div class="stat"><span>Total</span><strong data-total>€0,00</strong></div><div class="stat"><span>Média</span><strong data-average>€0,00</strong></div><div class="stat"><span>Maior</span><strong data-largest>€0,00</strong></div></div><div class="muted" style="margin-top:13px">Armazenamento local</div><div class="storage"><div data-storage-bar></div></div><div class="muted" data-storage-text style="margin-top:6px">A calcular...</div><div class="actions"><button class="btn" type="button" data-export-json>Exportar JSON</button><button class="btn" type="button" data-export-csv>Exportar CSV</button></div></aside>
                </div>
                <section class="card" style="margin-top:14px"><div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap"><div><h3>📦 Fila de sincronização</h3><div class="muted" data-queue-summary>0 registos pendentes.</div></div><button class="btn primary" type="button" data-sync>Sincronizar agora</button></div><div class="list" data-queue-list></div></section>
                <section class="card" style="margin-top:14px"><h3>🧮 Soma de tickets rápida</h3><div class="muted">Soma vários tickets e transforma o total numa despesa.</div><div class="fields"><div class="field"><label for="bunker-ticket">Valor do ticket</label><input id="bunker-ticket" type="number" min="0.01" step="0.01" inputmode="decimal" placeholder="0,00 €"></div><div class="field"><label>Total</label><input data-ticket-total value="€0,00" readonly></div></div><div class="actions"><button class="btn" type="button" data-add-ticket>Adicionar ticket</button><button class="btn" type="button" data-clear-tickets>Limpar</button><button class="btn primary" type="button" data-save-tickets>Guardar como despesa</button></div><div class="list" data-ticket-list></div></section>
                <section class="card" style="margin-top:14px"><h3>🕘 Histórico do Bunker</h3><div class="muted">Registos guardados neste dispositivo.</div><div style="margin-top:10px"><input data-history-search placeholder="Pesquisar descrição ou categoria..."></div><div class="list" data-history-list></div></section>
                <section class="card danger-zone" style="margin-top:14px"><h3>Gestão do Bunker</h3><div class="muted">Limpar tudo remove apenas os dados armazenados localmente neste dispositivo.</div><div class="actions"><button class="btn danger" type="button" data-clear-all>Limpar tudo</button></div></section>
            </div>
            <div class="notice" data-bunker-notice hidden></div>
        </div>
    `;

    const q = () => window.financeProBunker?.getOfflineQueue?.() || [];
    const notice = message => {
        const el = bunker.querySelector('[data-bunker-notice]');
        el.textContent = message;
        el.hidden = false;
        clearTimeout(el._timer);
        el._timer = setTimeout(() => { el.hidden = true; }, 3500);
    };
    const render = () => {
        const queue = q();
        const total = queue.reduce((sum, item) => sum + Number(item.amount || 0), 0);
        const average = queue.length ? total / queue.length : 0;
        const largest = queue.reduce((max, item) => Math.max(max, Number(item.amount || 0)), 0);
        bunker.querySelector('[data-count]').textContent = queue.length;
        bunker.querySelector('[data-total]').textContent = money.format(total);
        bunker.querySelector('[data-average]').textContent = money.format(average);
        bunker.querySelector('[data-largest]').textContent = money.format(largest);
        bunker.querySelector('[data-queue-summary]').textContent = queue.length ? `${queue.length} registo(s) pendente(s) — serão enviados quando a ligação estiver disponível.` : 'O Bunker está vazio. Está pronto para guardar a próxima despesa.';
        bunker.querySelector('[data-queue-list]').innerHTML = queue.length ? queue.slice().reverse().map(item => `<div class="row"><div class="row-main"><div class="row-title">${esc(item.title)}</div><div class="row-meta">${categoryLabels[item.category_slug] || item.category_slug || 'Sem categoria'} · ${formatDate(item.spent_at)}</div><span class="badge">${item.local_status === 'error' ? '🔴 Erro' : item.local_status === 'syncing' ? '🔵 A sincronizar' : '🟡 Pendente'}</span></div><div class="row-value">${money.format(item.amount)}</div></div>`).join('') : '<div class="empty">🔐<br><br><strong>O teu Bunker está pronto.</strong><br>Regista a primeira despesa acima.</div>';
        const search = bunker.querySelector('[data-history-search]').value.trim().toLowerCase();
        const filtered = queue.slice().reverse().filter(item => !search || `${item.title} ${item.description || ''} ${item.category_slug || ''}`.toLowerCase().includes(search));
        bunker.querySelector('[data-history-list]').innerHTML = filtered.length ? filtered.map(item => `<div class="row"><div class="row-main"><div class="row-title">${esc(item.title)}</div><div class="row-meta">${categoryLabels[item.category_slug] || item.category_slug || 'Sem categoria'} · ${formatDate(item.spent_at)}</div></div><div class="row-value">${money.format(item.amount)}</div></div>`).join('') : '<div class="empty">Nenhum registo encontrado.</div>';
    };

    bunker.querySelector('[data-bunker-form]').addEventListener('submit', event => {
        event.preventDefault();
        const amount = Number(bunker.querySelector('#bunker-amount').value);
        const title = bunker.querySelector('#bunker-title').value.trim();
        if (!Number.isFinite(amount) || amount <= 0) return notice('Indica um valor válido.');
        if (!title) return notice('Indica onde ou em que foi feita a despesa.');
        window.financeProBunker.saveOfflineExpense({
            amount, title, description: title,
            category_slug: bunker.querySelector('#bunker-category').value,
            payment_method: bunker.querySelector('#bunker-payment').value,
            notes: bunker.querySelector('#bunker-notes').value.trim(),
            spent_at: bunker.querySelector('#bunker-date').value ? new Date(bunker.querySelector('#bunker-date').value).toISOString() : new Date().toISOString(),
        });
        event.target.reset();
        notice('✓ Despesa guardada localmente.');
        render();
        bunker.querySelector('#bunker-amount').focus();
    });

    bunker.querySelector('[data-clear-form]').addEventListener('click', () => bunker.querySelector('[data-bunker-form]').reset());
    bunker.querySelectorAll('[data-category]').forEach(button => button.addEventListener('click', () => { bunker.querySelector('#bunker-category').value = button.dataset.category; bunker.querySelector('#bunker-amount').focus(); }));
    bunker.querySelector('[data-history-search]').addEventListener('input', render);
    bunker.querySelector('[data-sync]').addEventListener('click', async () => {
        if (!navigator.onLine) return notice('Ainda estás sem Internet. Os registos continuam seguros na fila local.');
        try { const result = await window.financeProBunker.syncOfflineExpenses(); notice(`✓ Sincronização concluída: ${result.count || 0} registo(s).`); render(); } catch { notice('A sincronização não foi concluída. Os registos continuam no Bunker.'); render(); }
    });

    let tickets = [];
    const renderTickets = () => {
        const total = tickets.reduce((sum, value) => sum + value, 0);
        bunker.querySelector('[data-ticket-total]').value = money.format(total);
        bunker.querySelector('[data-ticket-list]').innerHTML = tickets.length ? tickets.map((value, index) => `<div class="row"><div class="row-title">Ticket ${index + 1}</div><div class="row-value">${money.format(value)}</div></div>`).join('') : '';
    };
    bunker.querySelector('[data-add-ticket]').addEventListener('click', () => { const input = bunker.querySelector('#bunker-ticket'); const value = Number(input.value); if (!Number.isFinite(value) || value <= 0) return notice('Indica um valor de ticket válido.'); tickets.push(value); input.value = ''; renderTickets(); });
    bunker.querySelector('[data-clear-tickets]').addEventListener('click', () => { tickets = []; renderTickets(); });
    bunker.querySelector('[data-save-tickets]').addEventListener('click', () => { const total = tickets.reduce((sum, value) => sum + value, 0); if (!total) return notice('Adiciona pelo menos um ticket.'); bunker.querySelector('#bunker-amount').value = total.toFixed(2); bunker.querySelector('#bunker-title').value = 'Soma de tickets'; bunker.querySelector('#bunker-category').value = 'compras'; tickets = []; renderTickets(); notice('Total colocado no registo rápido.'); });

    bunker.querySelector('[data-export-json]').addEventListener('click', () => { window.financeProBunker.exportOfflineData('json'); notice('Dados exportados em JSON.'); });
    bunker.querySelector('[data-export-csv]').addEventListener('click', () => { window.financeProBunker.exportOfflineData('csv'); notice('Dados exportados em CSV.'); });
    bunker.querySelector('[data-clear-all]').addEventListener('click', () => {
        const queue = q();
        if (!queue.length) return notice('Não existem registos locais para apagar.');
        if (confirm(`Esta ação irá remover ${queue.length} registo(s) armazenado(s) neste dispositivo.\n\nEsta ação não pode ser desfeita.\n\nContinuar?`)) {
            window.financeProBunker.clearOfflineQueue();
            render();
            notice('Registos locais removidos.');
        }
    });

    const storage = async () => {
        if (!navigator.storage?.estimate) return;
        try {
            const result = await navigator.storage.estimate();
            if (!result.quota) return;
            const ratio = Math.min(1, Number(result.usage || 0) / Number(result.quota));
            bunker.querySelector('[data-storage-bar]').style.width = `${ratio * 100}%`;
            const bytes = value => { if (!value) return '0 B'; const units=['B','KB','MB','GB']; const i=Math.min(Math.floor(Math.log(value)/Math.log(1024)), units.length-1); return `${(value/Math.pow(1024,i)).toFixed(i?1:0)} ${units[i]}`; };
            bunker.querySelector('[data-storage-text]').textContent = `${bytes(result.usage || 0)} utilizados de aproximadamente ${bytes(result.quota)}.`;
        } catch {
            bunker.querySelector('[data-storage-text]').textContent = 'O navegador não disponibiliza uma estimativa fiável.';
        }
    };

    beginOfflineSession();
    const durationTimer = setInterval(() => {
        const node = bunker.querySelector('[data-offline-duration]');
        if (node) node.textContent = formatDuration(localStorage.getItem('finance-pro-offline-started-at'));
    }, 30000);
    bunker.addEventListener('DOMNodeRemoved', () => clearInterval(durationTimer), { once: true });
    window.addEventListener('offline-queue-updated', render);
    window.addEventListener('offline-bunker-synced', render);
    render();
    renderTickets();
    storage();
}

function start() {
    if (!navigator.onLine) beginOfflineSession();
    mountBunkerOverlay();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
} else {
    start();
}
