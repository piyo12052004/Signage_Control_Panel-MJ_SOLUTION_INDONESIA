import { ref, computed } from 'vue'
import type { ApiResponse } from '~/types/type-api'
import type { DeviceClientData } from '~/types/type-device-client'

export const useDeviceClient = () => {
    const { api } = useApi()

    const deviceClient = ref<DeviceClientData | null>(null)

    const loading = ref(false)
    const error = ref<string | null>(null)

    const currentIndex = ref(0)

    const contents = computed(
        () => deviceClient.value?.contents ?? []
    )

    const currentContent = computed(
        () => contents.value[currentIndex.value] ?? null
    )

    const loadDeviceClient = async (deviceId: string) => {
        loading.value = true
        error.value = null

        try {
            const response = await api<ApiResponse<DeviceClientData>>(
                `/device-client/${deviceId}`
            )

            deviceClient.value = response.data

            if (currentIndex.value >= contents.value.length) {
                currentIndex.value = 0
            }
        } catch (err: any) {
            error.value =
                err?.data?.message ||
                'Gagal mengambil data device client.'

        } finally {
            loading.value = false
        }
    }

    const nextContent = () => {
        if (contents.value.length <= 1) return

        currentIndex.value =
            (currentIndex.value + 1) % contents.value.length
    }

    return {
        deviceClient,
        contents,
        currentContent,
        currentIndex,
        loading,
        error,
        loadDeviceClient,
        nextContent,
    }
}