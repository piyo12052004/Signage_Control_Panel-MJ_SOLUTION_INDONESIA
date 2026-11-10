import type { ApiResponse } from '~/types/type-api'
import type { AuthToken } from '~/types/type-auth'

interface ApiOptions {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  body?: Record<string, unknown> | FormData
  query?: Record<string, unknown>
  headers?: HeadersInit
}

// Mencegah refresh berulang secara bersamaan.
let refreshPromise: Promise<string | null> | null = null

export const useApi = () => {
  const config = useRuntimeConfig()

  const baseURL = config.public.apiBase as string

  const tokenCookie = useCookie<string | null>(
    'signage_access_token',
    {
      default: () => null,
      sameSite: 'lax',
      secure: import.meta.env.PROD,
      path: '/',
      maxAge: 60 * 60 * 24 * 14
    }
  )

  // Mengambil token langsung dari cookie.
  const getToken = () => tokenCookie.value

  // Menyimpan token ke cookie.
  const setToken = (token: string | null) => {
    tokenCookie.value = token
  }

  // Refresh token.
  const refreshToken = async (): Promise<string | null> => {
    if (refreshPromise) {
      return refreshPromise
    }

    refreshPromise = (async () => {
      const currentToken = getToken()

      if (!currentToken) {
        return null
      }

      try {
        const response = await $fetch<ApiResponse<AuthToken>>(
          '/auth/refresh',
          {
            baseURL,
            method: 'POST',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${currentToken}`
            }
          }
        )

        const newToken = response.data.access_token

        if (!newToken) {
          throw new Error('Access token baru tidak ditemukan.')
        }

        setToken(newToken)

        return newToken
      } catch {
        setToken(null)
        return null
      } finally {
        refreshPromise = null
      }
    })()

    return refreshPromise
  }

  // Request API.
  const api = async <T>(
    path: string,
    options: ApiOptions = {},
    isRetry = false
  ): Promise<T> => {
    const token = getToken()

    const headers = new Headers(options.headers)

    headers.set('Accept', 'application/json')

    if (token) {
      headers.set('Authorization', `Bearer ${token}`)
    }

    try {
      return await $fetch<T>(path, {
        baseURL,
        method: options.method ?? 'GET',
        body: options.body,
        query: options.query,
        headers
      })
    } catch (error: any) {
      const status =
        error?.response?.status ??
        error?.statusCode ??
        error?.status

      const isAuthEndpoint =
        path.includes('/auth/login') ||
        path.includes('/auth/refresh')

      if (
        status === 401 &&
        !isRetry &&
        !isAuthEndpoint &&
        getToken()
      ) {
        const newToken = await refreshToken()

        if (newToken) {
          return api<T>(path, options, true)
        }
      }

      throw error
    }
  }

  return {
    api,
    getToken,
    setToken,
    refreshToken
  }
}