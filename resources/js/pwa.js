import syncManager from './sync/SyncManager'

export function registerPWA() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').then(reg => {
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data?.type === 'TRIGGER_SYNC') syncManager.sync()
            })
        }).catch(err => console.error('SW registration failed:', err))
    }
    syncManager.start()
}
