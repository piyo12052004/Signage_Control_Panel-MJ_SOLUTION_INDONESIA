export default defineNuxtRouteMiddleware(async () => {
    const { getToken, refreshToken } = useApi()

    if (!getToken()) {
        return
    }

    try {
        await useAuthStore().fetchUser()

        return navigateTo('/')
    } catch {
        if (getToken()) {
            return navigateTo('/')
        }
    }
})