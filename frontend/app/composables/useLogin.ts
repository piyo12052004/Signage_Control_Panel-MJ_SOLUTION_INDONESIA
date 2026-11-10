import type { LoginPayload } from '~/types/type-auth'

export const useLogin = () => {
    const authStore = useAuthStore()
    const notify = useNotify()

    const state = reactive<LoginPayload>({
        email: '',
        password: ''
    })

    const errors = reactive<{
        email?: string
        password?: string
        general?: string
    }>({})

    const isLoading = computed(() => authStore.loading)

    const clearErrors = () => {
        errors.email = undefined
        errors.password = undefined
        errors.general = undefined
    }

    const validate = (): boolean => {
        clearErrors()

        let isValid = true

        if (!state.email.trim()) {
            errors.email = 'Email wajib diisi.'
            isValid = false
        }

        if (!state.password) {
            errors.password = 'Kata sandi wajib diisi.'
            isValid = false
        }

        if (!isValid) {
            notify.warning(
                'Silakan lengkapi email dan kata sandi.'
            )
        }

        return isValid
    }

    const handleLogin = async () => {
        if (!validate()) {
            return
        }

        try {
            await authStore.login({
                email: state.email.trim(),
                password: state.password
            })

            notify.success('Login berhasil.')

            await navigateTo('/')
        } catch (error: any) {
            const response = error?.data

            const status =
                error?.response?.status ??
                error?.statusCode ??
                error?.status

            if (status === 422) {
                const validationErrors = response?.errors

                errors.email = validationErrors?.email?.[0]
                errors.password = validationErrors?.password?.[0]
            }

            const message =
                response?.error?.message ||
                response?.message ||
                authStore.error ||
                'Email atau kata sandi yang kamu masukkan salah.'

            errors.general = message

            notify.error(message)
        }
    }

    return {
        state,
        errors,
        isLoading,
        handleLogin
    }
}