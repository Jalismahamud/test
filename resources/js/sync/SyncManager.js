import {
    getPendingSales,
    markSaleSynced,
    markSaleFailed,
    bulkSaveProducts,
    bulkSaveCategories,
} from '../db/database'

class SyncManager {
    constructor() {
        this.running = false
        this.maxRetries = 3
    }

    async sync() {
        if (this.running || !navigator.onLine) return
        this.running = true
        try {
            await this.push()
            await this.pull()
        } catch (err) {
            console.error('Sync error:', err)
        } finally {
            this.running = false
        }
    }

    async push() {
        const pending = await getPendingSales()
        const eligible = pending.filter(s => (s.retry_count || 0) < this.maxRetries)
        if (!eligible.length) return

        const token = localStorage.getItem('pos_token')

        try {
            const res = await fetch('/api/v1/sync/push', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Accept':        'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    items: eligible.map(s => ({
                        uuid:    s.uuid,
                        type:    'sale',
                        payload: s.data,
                    })),
                }),
            })

            const json = await res.json()

            if (json.results) {
                for (const result of json.results) {
                    if (result.status === 'synced') {
                        await markSaleSynced(result.uuid, result.server_id)
                    } else {
                        await markSaleFailed(result.uuid, result.error || 'Unknown error')
                    }
                }
            }
        } catch (err) {
            console.error('Push failed:', err)
        }
    }

    async pull() {
        const since = localStorage.getItem('last_sync_at')
        const token = localStorage.getItem('pos_token')

        try {
            const url = '/api/v1/sync/pull' + (since ? `?since=${encodeURIComponent(since)}` : '')
            const res = await fetch(url, {
                headers: {
                    'Accept':        'application/json',
                    'Authorization': `Bearer ${token}`,
                },
            })

            const json = await res.json()

            if (json.products?.length) await bulkSaveProducts(json.products)
            if (json.categories?.length) await bulkSaveCategories(json.categories)
            if (json.pulled_at) localStorage.setItem('last_sync_at', json.pulled_at)
        } catch (err) {
            console.error('Pull failed:', err)
        }
    }

    start() {
        window.addEventListener('online', () => {
            console.log('Back online — syncing...')
            this.sync()
        })

        setInterval(() => this.sync(), 5 * 60 * 1000)

        if (navigator.onLine) {
            setTimeout(() => this.sync(), 2000)
        }
    }
}

export default new SyncManager()
