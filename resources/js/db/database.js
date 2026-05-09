import Dexie from 'dexie'

const db = new Dexie('SmartPOS_DB')

db.version(1).stores({
    products:      '++id, &uuid, sku, barcode, category_id, is_active',
    categories:    '++id, &uuid, name',
    settings:      '&key, value',
    pending_sales: '++id, &uuid, sync_status, created_at, retry_count',
    held_sales:    '++id, &uuid, created_at',
})

export async function savePendingSale(data) {
    return db.pending_sales.add({
        ...data,
        uuid:        data.uuid || crypto.randomUUID(),
        sync_status: 'pending',
        retry_count: 0,
        created_at:  new Date().toISOString(),
    })
}

export async function getPendingSales() {
    return db.pending_sales.where('sync_status').equals('pending').toArray()
}

export async function markSaleSynced(uuid, serverId) {
    return db.pending_sales.where({ uuid }).modify({ sync_status: 'synced', server_id: serverId })
}

export async function markSaleFailed(uuid, errorMsg) {
    const sale = await db.pending_sales.where({ uuid }).first()
    return db.pending_sales.where({ uuid }).modify({
        sync_status: 'failed',
        last_error:  errorMsg,
        retry_count: (sale?.retry_count || 0) + 1,
    })
}

export async function saveHeldSale(cartData) {
    return db.held_sales.add({
        ...cartData,
        uuid:       crypto.randomUUID(),
        created_at: new Date().toISOString(),
    })
}

export async function getHeldSales() {
    return db.held_sales.orderBy('created_at').reverse().toArray()
}

export async function deleteHeldSale(id) {
    return db.held_sales.delete(id)
}

export async function bulkSaveProducts(products) {
    return db.products.bulkPut(products)
}

export async function bulkSaveCategories(categories) {
    return db.categories.bulkPut(categories)
}

export async function getLocalProducts(query = '', categoryId = null) {
    let collection = db.products.where('is_active').equals(1)
    const results = await collection.toArray()
    return results.filter(p => {
        const matchQuery = !query || p.name?.toLowerCase().includes(query.toLowerCase()) || p.sku?.includes(query)
        const matchCat = !categoryId || p.category_id === categoryId
        return matchQuery && matchCat
    }).slice(0, 15)
}

export async function getLocalCategories() {
    return db.categories.toArray()
}

export default db
