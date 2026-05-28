function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

window.enableNotifications = async function() {
    try {
        const registration = await navigator.serviceWorker.ready;

        // 1. Pedir permissão ao iOS
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Precisas de autorizar as notificações nas definições do iPhone.');
            return;
        }

        // 2. Criar a Chave Única do teu telemóvel
        const subscribeOptions = {
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(import.meta.env.VITE_VAPID_PUBLIC_KEY)
        };

        const subscription = await registration.pushManager.subscribe(subscribeOptions);

        // 3. Obter o Token de Segurança do HTML
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            alert('Erro: Chave CSRF não encontrada. Adicione a meta tag ao head.');
            return;
        }

        // 4. Enviar para o Servidor Laravel
        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(subscription)
        });

        if (response.ok) {
            alert('SISTEMA ATIVO: O teu iPhone já está registado na BD!');
        } else {
            const errorData = await response.json();
            alert('Erro no Servidor: ' + (errorData.message || response.statusText));
        }

    } catch (error) {
        alert('ERRO TÉCNICO: ' + error.message);
    }
};
