import './offline-bunker';
import './offline-bunker-ui';

window.beginOfflineSession = function beginOfflineSession() {
    if (!localStorage.getItem('finance-pro-offline-started-at')) {
        localStorage.setItem('finance-pro-offline-started-at', String(Date.now()));
    }
};
