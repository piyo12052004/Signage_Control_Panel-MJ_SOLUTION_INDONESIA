import type { ApiResponse } from '~/types/type-api'
import type {
    User,
    LoginPayload,
    LoginData,
    AuthToken
} from '~/types/type-auth'

export const useAuthStore = defineStore('authStore', () => {
    const { api, getToken, setToken } = useApi()

    const user = ref<User | null>(null)
    const loading = ref(false)
    const error = ref<string | null>(null)

    // Simpan data user ke localStorage.
    const saveUser = (userData: User) => {
        user.value = userData

        if (import.meta.client) {
            localStorage.setItem(
                'signage_user',
                JSON.stringify(userData)
            )
        }
    }

    // Ambil data user dari localStorage.
    const loadUser = () => {
        if (!import.meta.client) return null

        const storedUser = localStorage.getItem('signage_user')

        if (!storedUser) return null

        try {
            const parsedUser = JSON.parse(storedUser) as User
            user.value = parsedUser

            return parsedUser
        } catch {
            localStorage.removeItem('signage_user')
            user.value = null

            return null
        }
    }

    // Login.
    const login = async (
        credentials: LoginPayload
    ): Promise<LoginData> => {
        loading.value = true
        error.value = null

        try {
            const response = await api<ApiResponse<LoginData>>(
                '/auth/login',
                {
                    method: 'POST',
                    body: credentials
                }
            )

            const newToken = response.data.access_token

            if (!newToken) {
                throw new Error('Access token tidak ditemukan.')
            }

            // Simpan token ke cookie.
            setToken(newToken)

            // Simpan user ke Pinia dan localStorage.
            saveUser(response.data.user)

            return response.data
        } catch (err: any) {
            error.value =
                err?.data?.error?.message ||
                err?.data?.message ||
                err?.message ||
                'Login gagal.'

            throw err
        } finally {
            loading.value = false
        }
    }

    // Get user dari Laravel.
    const fetchUser = async (): Promise<User> => {
        const response = await api<ApiResponse<User>>('/auth/me')

        saveUser(response.data)

        return response.data
    }

    // Refresh token.
    const refresh = async (): Promise<string | null> => {
        const { refreshToken } = useApi()

        return await refreshToken()
    }

    // Hapus autentikasi.
    const clearAuth = () => {
        setToken(null)
        user.value = null
        error.value = null

        if (import.meta.client) {
            localStorage.removeItem('signage_user')
        }
    }

    // Logout.
    const logout = async () => {
        try {
            if (getToken()) {
                await api('/auth/logout', {
                    method: 'POST'
                })
            }
        } finally {
            clearAuth()
            await navigateTo('/auth/login')
        }
    }

    return {
        user,
        loading,
        error,
        saveUser,
        loadUser,
        isAuthenticated: computed(() => !!getToken()),
        login,
        fetchUser,
        refresh,
        clearAuth,
        logout
    }
})