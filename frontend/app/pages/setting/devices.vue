<script setup lang="ts">
import type { Device, DeviceForm } from '~/types/type-device'

definePageMeta({
    middleware: 'auth',
})

const {
    devices,
    search,
    status,
    modalOpen,
    selectedDevice,
    pagination,
    totalDevices,
    onlineCount,
    offlineCount,
    loading,
    saving,
    error,
    fetchDevices,
    openCreate,
    openEdit,
    closeModal,
    saveDevice,
    deleteDevice,
    changePage,
} = useDevices()

// Initial loading
const { pending } = await useAsyncData(
    'setting-devices',
    async () => {
        await fetchDevices()
        return true
    },
    {
        server: false,
    },
)

// Error halaman
const pageError = ref<string | null>(null)

// Delete modal state
const deleteModalOpen = ref(false)
const selectedDeviceToDelete = ref<Device | null>(null)
const deleting = ref(false)
const deleteError = ref<string | null>(null)

// Error handler
const getErrorMessage = (err: any, fallback: string) => {
    return (
        err?.data?.error?.message ||
        err?.data?.message ||
        err?.message ||
        fallback
    )
}

// Refresh data
const handleRefresh = async () => {
    pageError.value = null

    try {
        await fetchDevices()
    } catch (err: any) {
        pageError.value = getErrorMessage(
            err,
            'Gagal memperbarui data device.',
        )
    }
}

// Create / Update
const handleSave = async (form: DeviceForm) => {
    pageError.value = null

    try {
        await saveDevice(form)
        closeModal()
    } catch (err: any) {
        pageError.value = getErrorMessage(
            err,
            'Gagal menyimpan data device.',
        )
    }
}

// Open delete modal
const openDeleteModal = (device: Device) => {
    selectedDeviceToDelete.value = device
    deleteError.value = null
    deleteModalOpen.value = true
}

// Close delete modal
const closeDeleteModal = () => {
    if (deleting.value) return

    deleteModalOpen.value = false
    selectedDeviceToDelete.value = null
    deleteError.value = null
}

// Delete device
const handleDelete = async () => {
    if (!selectedDeviceToDelete.value || deleting.value) return

    deleting.value = true
    deleteError.value = null

    try {
        await deleteDevice(selectedDeviceToDelete.value)

        deleteModalOpen.value = false
        selectedDeviceToDelete.value = null
    } catch (err: any) {
        deleteError.value = getErrorMessage(
            err,
            'Gagal menghapus data device.',
        )
    } finally {
        deleting.value = false
    }
}

// Pagination
const handlePageChange = async (newPage: number) => {
    if (
        newPage < 1 ||
        newPage > pagination.value.last_page ||
        loading.value
    ) {
        return
    }

    pageError.value = null

    try {
        await changePage(newPage)
    } catch (err: any) {
        pageError.value = getErrorMessage(
            err,
            'Gagal mengambil data halaman.',
        )
    }
}
</script>

<template>
    <div class="space-y-6 p-6">

        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Devices
                </h1>

                <p class="text-sm text-gray-500">
                    Kelola perangkat digital signage.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <!-- Refresh -->
                <UButton color="neutral" variant="outline" icon="i-lucide-refresh-cw" :loading="loading"
                    :disabled="loading || pending" @click="handleRefresh">
                    Refresh
                </UButton>

                <!-- Create -->
                <UButton icon="i-lucide-plus" :disabled="saving" @click="openCreate">
                    Tambah Device
                </UButton>
            </div>
        </div>

        <!-- Error -->
        <UAlert v-if="pageError || error" color="error" variant="soft" title="Terjadi kesalahan"
            :description="pageError || error || 'Gagal memuat data device.'" />

        <!-- Statistics -->
        <DevicesDeviceStats :total="totalDevices" :online="onlineCount" :offline="offlineCount" />

        <!-- Toolbar -->
        <DevicesDeviceToolbar v-model:search="search" v-model:status="status" @create="openCreate" />

        <!-- Table -->
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800">
            <div v-if="pending || loading" class="flex justify-center py-12">
                <UIcon name="i-lucide-loader-circle" class="size-8 animate-spin text-primary" />
            </div>

            <DevicesDeviceTable v-else :devices="devices" @edit="openEdit" @delete="openDeleteModal" />
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500">
                Menampilkan
                {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                –
                {{ Math.min(
                    pagination.current_page * pagination.per_page,
                    pagination.total
                ) }}
                dari {{ pagination.total }} device
            </p>

            <div class="flex items-center gap-2">
                <UButton color="neutral" variant="outline" icon="i-lucide-chevron-left"
                    :disabled="pagination.current_page <= 1 || loading"
                    @click="handlePageChange(pagination.current_page - 1)">
                    Sebelumnya
                </UButton>

                <span class="whitespace-nowrap text-sm">
                    {{ pagination.current_page }} / {{ pagination.last_page }}
                </span>

                <UButton color="neutral" variant="outline" trailing-icon="i-lucide-chevron-right" :disabled="pagination.current_page >= pagination.last_page ||
                    loading
                    " @click="handlePageChange(pagination.current_page + 1)">
                    Berikutnya
                </UButton>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!pending && !loading && devices.length === 0" class="py-10 text-center">
            <UIcon name="i-lucide-monitor" class="mx-auto mb-3 size-10 text-gray-400" />

            <p class="font-medium">
                Belum ada device
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Tambahkan device untuk mulai mengelola perangkat signage.
            </p>
        </div>

        <!-- Create / Edit Modal -->
        <DevicesDeviceFormModal v-model:open="modalOpen" :device="selectedDevice" :loading="saving"
            @save="handleSave" />

        <!-- Delete Confirmation Modal -->
        <UModal :open="deleteModalOpen" title="Hapus Perangkat"
            description="Apakah kamu yakin ingin menghapus perangkat ini?" @update:open="(value) => {
                if (!value) closeDeleteModal()
            }">
            <template #body>
                <div class="space-y-4">

                    <!-- Device Information -->
                    <div class="flex items-start gap-3">
                        <div class="rounded-full bg-red-100 p-3 text-red-600 dark:bg-red-950">
                            <UIcon name="i-lucide-trash-2" class="size-6" />
                        </div>

                        <div class="min-w-0">
                            <p class="font-medium">
                                {{ selectedDeviceToDelete?.name }}
                            </p>

                            <p class="mt-1 text-sm text-neutral-500">
                                Lokasi:
                                {{ selectedDeviceToDelete?.location }}
                            </p>
                        </div>
                    </div>

                    <!-- Warning -->
                    <UAlert color="warning" variant="soft" title="Perhatian"
                        description="Data perangkat yang dihapus tidak dapat dikembalikan." />

                    <!-- Delete Error -->
                    <UAlert v-if="deleteError" color="error" variant="soft" title="Gagal menghapus perangkat"
                        :description="deleteError" />

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-2">
                        <UButton color="neutral" variant="outline" :disabled="deleting" @click="closeDeleteModal">
                            Batal
                        </UButton>

                        <UButton color="error" icon="i-lucide-trash-2" :loading="deleting"
                            :disabled="!selectedDeviceToDelete" @click="handleDelete">
                            Hapus Perangkat
                        </UButton>
                    </div>

                </div>
            </template>
        </UModal>

    </div>
</template>
