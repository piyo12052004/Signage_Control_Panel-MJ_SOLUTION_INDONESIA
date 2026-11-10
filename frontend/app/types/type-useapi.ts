export interface ApiOptions {
    method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
    body?: Record<string, unknown> | FormData
    query?: Record<string, unknown>
    headers?: HeadersInit
}