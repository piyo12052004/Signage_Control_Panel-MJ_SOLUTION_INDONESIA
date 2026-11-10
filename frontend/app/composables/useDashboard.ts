
import { computed, ref } from 'vue'
import type { ApiResponse } from '~/types/type-api'
import type {
    DashboardData,
    DashboardDeviceContents,
} from '~/types/type-dashboard'

export const useDashboard = () => {
    const { api } = useApi()
    const notify = useNotify()

    const dashboard = ref<DashboardData>({
        total_devices: 0,
        online_devices: 0,
        offline_devices: 0,
        total_contents: 0,
        recent_devices: [],
    })

    const deviceContents = ref<DashboardDeviceContents | null>(null)

    const loading = ref(false)
    const contentLoading = ref(false)
    const saving = ref(false)
    const removingContentId = ref<string | null>(null)
    const error = ref<string | null>(null)

    const totalDevices = computed(() => dashboard.value.total_devices)
    const onlineDevices = computed(() => dashboard.value.online_devices)
    const offlineDevices = computed(() => dashboard.value.offline_devices)
    const totalContents = computed(() => dashboard.value.total_contents)
    const recentDevices = computed(() => dashboard.value.recent_devices)

    // Memuat data dashboard dari API.
    const loadDashboard = async () => {
        loading.value = true
        error.value = null

        try {
            const response = await api<ApiResponse<DashboardData>>(
                '/dashboard',
                { method: 'GET' },
            )

            dashboard.value = response.data
        } catch (err) {
            error.value = 'Gagal memuat data dashboard.'
            notify.error(error.value)
            throw err
        } finally {
            loading.value = false
        }
    }

    // Memperbarui status device dari event WebSocket.
    const updateDeviceStatus = (
        deviceId: string,
        status: 'online' | 'offline',
        lastSeen: string | null,
    ) => {
        const device = dashboard.value.recent_devices.find(
            item => item.id === deviceId,
        )

        if (!device) {
            console.warn(
                '[Dashboard] Device tidak ditemukan:',
                deviceId,
            )
            return
        }

        const previousStatus = device.status

        // Jika status tidak berubah, hanya perbarui last_seen.
        if (previousStatus === status) {
            device.last_seen = lastSeen
            return
        }

        // Perbarui data device.
        device.status = status
        device.last_seen = lastSeen

        // Perbarui statistik dashboard.
        if (previousStatus === 'online' && status === 'offline') {
            dashboard.value.online_devices = Math.max(
                0,
                dashboard.value.online_devices - 1,
            )

            dashboard.value.offline_devices += 1
        }

        if (previousStatus === 'offline' && status === 'online') {
            dashboard.value.offline_devices = Math.max(
                0,
                dashboard.value.offline_devices - 1,
            )

            dashboard.value.online_devices += 1
        }
    }

    // Mengambil content yang terhubung ke device.
    const getDeviceContents = async (deviceId: string) => {
        contentLoading.value = true
        deviceContents.value = null

        try {
            const response = await api<ApiResponse<DashboardDeviceContents>>(
                `/dashboard/devices/${deviceId}/contents`,
                { method: 'GET' },
            )

            deviceContents.value = response.data
        } catch (err) {
            notify.error('Gagal mengambil content device.')
            throw err
        } finally {
            contentLoading.value = false
        }
    }

    // Menambahkan content ke device.
    const attachContent = async (
        deviceId: string,
        contentId: string,
    ) => {
        saving.value = true

        try {
            await api<ApiResponse<null>>(
                `/dashboard/devices/${deviceId}/contents`,
                {
                    method: 'POST',
                    body: { content_id: contentId },
                },
            )

            await getDeviceContents(deviceId)
            await loadDashboard()

            notify.success('Content berhasil ditambahkan.')
        } catch (err) {
            notify.error('Gagal menambahkan content.')
            throw err
        } finally {
            saving.value = false
        }
    }

    // Melepas content dari device.
    const detachContent = async (
        deviceId: string,
        contentId: string,
    ) => {
        removingContentId.value = contentId

        try {
            await api<ApiResponse<null>>(
                `/dashboard/devices/${deviceId}/contents/${contentId}`,
                { method: 'DELETE' },
            )

            await getDeviceContents(deviceId)
            await loadDashboard()

            notify.success('Content berhasil dilepas dari device.')
        } catch (err) {
            notify.error('Gagal melepas content.')
            throw err
        } finally {
            removingContentId.value = null
        }
    }

    return {
        dashboard,
        deviceContents,
        loading,
        contentLoading,
        saving,
        removingContentId,
        error,

        totalDevices,
        onlineDevices,
        offlineDevices,
        totalContents,
        recentDevices,

        loadDashboard,
        updateDeviceStatus,
        getDeviceContents,
        attachContent,
        detachContent,
    }
}
