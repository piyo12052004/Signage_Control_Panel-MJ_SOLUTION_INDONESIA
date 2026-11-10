import { computed, ref, watch } from 'vue'
import type {
    Device,
    DeviceForm,
    DeviceListData,
    DevicePagination,
} from '~/types/type-device'
import type { ApiResponse } from '~/types/type-api'

export const useDevices = () => {
    const { api } = useApi()
    const notify = useNotify()

    const devices = ref<Device[]>([])

    const search = ref('')
    const status = ref('all')

    const modalOpen = ref(false)
    const selectedDevice = ref<Device | null>(null)

    const loading = ref(false)
    const saving = ref(false)
    const deleting = ref(false)

    const error = ref<string | null>(null)

    const pagination = ref<DevicePagination>({
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 1,
    })

    // Helper untuk mengambil pesan error dari API
    const getErrorMessage = (err: any, fallback: string) => {
        return (
            err?.data?.error?.message ||
            err?.data?.message ||
            err?.message ||
            fallback
        )
    }

    // Fetch devices
    const fetchDevices = async () => {
        loading.value = true
        error.value = null

        try {
            const response = await api<
                ApiResponse<DeviceListData> & {
                    meta?: DevicePagination
                }
            >('/setting/devices', {
                method: 'GET',
                query: {
                    page: pagination.value.current_page,
                    per_page: pagination.value.per_page,
                    search: search.value || undefined,
                    status: status.value === 'all'
                        ? undefined
                        : status.value,
                    sort_by: 'created_at',
                    sort_direction: 'desc',
                },
            })

            devices.value = response.data?.items ?? []

            pagination.value = {
                current_page: response.meta?.current_page ?? 1,
                per_page: response.meta?.per_page ?? 10,
                total: response.meta?.total ?? 0,
                last_page: response.meta?.last_page ?? 1,
            }

            return response.data
        } catch (err: any) {
            error.value = getErrorMessage(
                err,
                'Gagal mengambil data device.',
            )

            notify.error(error.value)

            throw err
        } finally {
            loading.value = false
        }
    }

    // Statistics
    const totalDevices = computed(() => pagination.value.total)

    const onlineCount = computed(() =>
        devices.value.filter(device => device.status === 'online').length,
    )

    const offlineCount = computed(() =>
        devices.value.filter(device => device.status === 'offline').length,
    )

    // Modal
    const openCreate = () => {
        selectedDevice.value = null
        modalOpen.value = true
    }

    const openEdit = (device: Device) => {
        selectedDevice.value = { ...device }
        modalOpen.value = true
    }

    const closeModal = () => {
        modalOpen.value = false
        selectedDevice.value = null
    }

    // Create / Update
    const saveDevice = async (form: DeviceForm) => {
        saving.value = true

        const isEditing = Boolean(selectedDevice.value)

        try {
            if (isEditing && selectedDevice.value) {
                await api(`/setting/devices/${selectedDevice.value.id} `, {
                    method: 'PUT',
                    body: form,
                })
            } else {
                await api('/setting/devices', {
                    method: 'POST',
                    body: form,
                })
            }

            await fetchDevices()

            notify.success(
                isEditing
                    ? 'Device berhasil diperbarui.'
                    : 'Device berhasil ditambahkan.',
            )

            return isEditing ? 'updated' : 'created'
        } catch (err: any) {
            const message = getErrorMessage(
                err,
                isEditing
                    ? 'Gagal memperbarui device.'
                    : 'Gagal menambahkan device.',
            )

            notify.error(message)

            throw err
        } finally {
            saving.value = false
        }
    }

    // Delete
    const deleteDevice = async (device: Device) => {
        deleting.value = true

        try {
            await api(`/setting/devices/${device.id} `, {
                method: 'DELETE',
            })

            // Jika data terakhir pada halaman dihapus,
            // kembali ke halaman sebelumnya.
            if (
                devices.value.length === 1 &&
                pagination.value.current_page > 1
            ) {
                pagination.value.current_page--
            }

            await fetchDevices()

            notify.success('Device berhasil dihapus.')
        } catch (err: any) {
            const message = getErrorMessage(
                err,
                'Gagal menghapus device.',
            )

            notify.error(message)

            throw err
        } finally {
            deleting.value = false
        }
    }

    // Pagination
    const changePage = async (newPage: number) => {
        if (
            newPage < 1 ||
            newPage > pagination.value.last_page
        ) {
            return
        }

        pagination.value.current_page = newPage

        await fetchDevices()
    }

    // Search dan filter
    watch([search, status], () => {
        pagination.value.current_page = 1
        fetchDevices().catch(() => { })
    })

    return {
        devices,
        search,
        status,
        modalOpen,
        selectedDevice,
        pagination,
        loading,
        saving,
        deleting,
        error,
        totalDevices,
        onlineCount,
        offlineCount,
        fetchDevices,
        openCreate,
        openEdit,
        closeModal,
        saveDevice,
        deleteDevice,
        changePage,
    }
}
