export default defineNuxtRouteMiddleware(async () => {
    const authStore = useAuthStore()
    const { getToken } = useApi()

    if (!getToken()) {
        authStore.clearAuth()
        return navigateTo('/auth/login')
    }

    if (!authStore.user && import.meta.client) {
        authStore.loadUser()
    }

    if (!authStore.user) {
        try {
            await authStore.fetchUser()
        } catch {
            authStore.clearAuth()
            return navigateTo('/auth/login')
        }
    }
})