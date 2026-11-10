import type {
    LoginCredentials,
    LoginData,
    User,
    AuthToken
} from '~/types/type-auth'

import type { ApiResponse } from '~/types/type-api'

export const useAuth = () => {
    const api = useApi()

    const accessToken = useCookie<string | null>(
        'signage_access_token'
    )

    const user = useState<User | null>(
        'auth:user',
        () => null
    )

    const isAuthenticated = computed(() => {
        return Boolean(accessToken.value)
    })

    const login = async (credentials: LoginCredentials) => {
        const response = await api<ApiResponse<LoginData>>('/auth/login',{
                method: 'POST',
                body: credentials
            }
        )

        accessToken.value = response.data.access_token
        user.value = response.data.user

        return response.data
    }

    const getMe = async () => {
        const response = await api<ApiResponse<User>>('/auth/me')

        user.value = response.data

        return response.data
    }

    const refresh = async () => {
        const response = await api<ApiResponse<AuthToken>>(
            '/auth/refresh',
            {
                method: 'POST'
            }
        )

        accessToken.value = response.data.access_token

        return response.data
    }

    const logout = async () => {
        try {
            if (accessToken.value) {
                await api('/auth/logout', {
                    method: 'POST'
                })
            }
        } finally {
            accessToken.value = null
            user.value = null

            await navigateTo('/auth/login')
        }
    }

    const clearAuth = () => {
        accessToken.value = null
        user.value = null
    }

    return {
        user,
        accessToken,
        isAuthenticated,
        login,
        getMe,
        refresh,
        logout,
        clearAuth
    }
}