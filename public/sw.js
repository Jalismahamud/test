const CACHE_NAME = 'smart-pos-v1'
const STATIC_ASSETS = [
    '/dashboard',
    '/pos',
    '/offline.html',
]

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(() => {})
        })
    )
    self.skipWaiting()
})

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            )
        )
    )
    self.clients.claim()
})

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url)

    if (url.pathname.startsWith('/api/')) return

    if (event.request.method !== 'GET') return

    event.respondWith(
        fetch(event.request)
            .then(response => {
                const clone = response.clone()
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone))
                return response
            })
            .catch(() => caches.match(event.request))
    )
})

self.addEventListener('sync', event => {
    if (event.tag === 'sync-sales') {
        event.waitUntil(
            self.clients.matchAll().then(clients => {
                clients.forEach(client => client.postMessage({ type: 'TRIGGER_SYNC' }))
            })
        )
    }
})

self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting()
})
