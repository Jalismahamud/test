import axios from 'axios'

const api = axios.create({
    baseURL: '/api/v1',
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
})

api.interceptors.request.use(config => {
    const token = localStorage.getItem('pos_token')
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
})

api.interceptors.response.use(
    res => res,
    err => {
        if (err.response?.status === 401) {
            localStorage.removeItem('pos_token')
            window.location.href = '/login'
        }
        return Promise.reject(err)
    }
)

export const searchProducts = (q, categoryId = null) =>
    api.get('/products/search', { params: { q, category_id: categoryId } })

export const processSale = (data) => api.post('/sales', data)

export const getCustomers = (search = '') => api.get('/customers', { params: { search } })

export const syncPush = (items) => api.post('/sync/push', { items })

export const syncPull = (since = null) => api.get('/sync/pull', { params: { since } })

export const getDashboardStats = () => api.get('/reports/dashboard')

export default api
