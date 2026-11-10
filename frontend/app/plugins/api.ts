import type { ApiResponse } from '~/types/type-api'
import type { AuthToken } from '~/types/type-auth'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const accessToken = useCookie<string | null>('signage_access_token', {
    default: () => null,
    sameSite: 'lax',
    secure: import.meta.env.PROD,
    path: '/',
    maxAge: 60 * 60 * 24 * 14
  })

  const api = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      Accept: 'application/json'
    }
  })

  let refreshPromise: Promise<string | null> | null = null

  const refreshAccessToken = async (): Promise<string | null> => {
    if (refreshPromise) {
      return refreshPromise
    }

    refreshPromise = (async () => {
      const oldToken = accessToken.value

      if (!oldToken) {
        return null
      }

      try {
        const response = await $fetch<ApiResponse<AuthToken>>(
          '/auth/refresh',
          {
            baseURL: config.public.apiBase,
            method: 'POST',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${oldToken}`
            }
          }
        )

        const newToken = response.data.access_token

        accessToken.value = newToken

        return newToken
      } catch {
        accessToken.value = null
        return null
      } finally {
        refreshPromise = null
      }
    })()

    return refreshPromise
  }

  const apiRequest = async <T>(
    url: string,
    options: Record<string, any> = {}
  ): Promise<T> => {
    const token = accessToken.value

    const headers = new Headers(options.headers || {})

    headers.set('Accept', 'application/json')

    if (token) {
      headers.set('Authorization', `Bearer ${token}`)
    }

    try {
      return await api<T>(url, {
        ...options,
        headers
      })
    } catch (error: any) {
      const status = error?.response?.status
      const isAuthEndpoint =
        url.includes('/auth/login') ||
        url.includes('/auth/refresh')

      if (status !== 401 || isAuthEndpoint || !accessToken.value) {
        throw error
      }

      const newToken = await refreshAccessToken()

      if (!newToken) {
        accessToken.value = null
        throw error
      }

      const retryHeaders = new Headers(options.headers || {})

      retryHeaders.set('Accept', 'application/json')
      retryHeaders.set('Authorization', `Bearer ${newToken}`)

      // Request hanya diulang satu kali.
      return await api<T>(url, {
        ...options,
        headers: retryHeaders
      })
    }
  }

  return {
    provide: {
      api: apiRequest
    }
  }
})