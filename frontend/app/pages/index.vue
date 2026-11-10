<script setup lang="ts">
import type { DropdownMenuItem } from '@nuxt/ui'

definePageMeta({
    middleware: 'auth',
})

interface DeviceStatusEvent {
    device_id: string
    name: string
    status: 'online' | 'offline'
    last_seen: string | null
}

// Dashboard
const {
    loading,
    error,
    totalDevices,
    onlineDevices,
    offlineDevices,
    totalContents,
    recentDevices,
    deviceContents,
    contentLoading,
    saving,
    removingContentId,
    loadDashboard,
    getDeviceContents,
    updateDeviceStatus,
    attachContent,
    detachContent,
} = useDashboard()

// Manage Content Modal
const isContentModalOpen = ref(false)
const selectedDeviceId = ref<string | null>(null)

// WebSocket
const {
    connected: websocketConnected,
    listenDeviceStatus,
} = useWebSocket()

let stopListening: (() => void) | undefined

// Menerima perubahan status device dari WebSocket.
const handleDeviceStatus = (event: DeviceStatusEvent) => {
    updateDeviceStatus(
        event.device_id,
        event.status,
        event.last_seen,
    )
}

// Open Device Client & Manage Content
const items = (deviceId: string): DropdownMenuItem[][] => [
    [
        {
            label: 'Open Device Client',
            icon: 'i-lucide-monitor-play',
            onSelect: () => {
                window.open(
                    `/device/${deviceId}`,
                    '_blank',
                    'noopener,noreferrer',
                )
            },
        },
    ],
    [
        {
            label: 'Manage Content',
            icon: 'i-lucide-images',
            onSelect: async () => {
                selectedDeviceId.value = deviceId
                isContentModalOpen.value = true

                try {
                    await getDeviceContents(deviceId)
                } catch {
                    // Error notification ditangani composable.
                }
            },
        },
    ],
]

// Add Content
const handleAttachContent = async (contentId: string) => {
    if (!selectedDeviceId.value) return

    try {
        await attachContent(
            selectedDeviceId.value,
            contentId,
        )
    } catch {
        // Error notification ditangani composable.
    }
}

// Remove Content
const handleDetachContent = async (contentId: string) => {
    if (!selectedDeviceId.value) return

    try {
        await detachContent(
            selectedDeviceId.value,
            contentId,
        )
    } catch {
        // Error notification ditangani composable.
    }
}

// Initialize Dashboard and WebSocket
onMounted(async () => {
    try {
        await loadDashboard()
    } catch (err) {
        console.error(
            '[Dashboard] Gagal memuat dashboard:',
            err,
        )
    }

    // Subscribe WebSocket setelah dashboard dimuat.
    console.log('[Dashboard] Memulai WebSocket listener...')

    stopListening = listenDeviceStatus(handleDeviceStatus)
})

// Cleanup WebSocket
onUnmounted(() => {
    stopListening?.()
    stopListening = undefined
})
</script>


<template>
    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan aktivitas perangkat dan konten.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <!-- WebSocket Status -->
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full" :class="websocketConnected ? 'bg-green-500' : 'bg-red-500'" />

                    <span class="text-xs text-gray-500">
                        {{ websocketConnected ? 'Live Connected' : 'Disconnected' }}
                    </span>
                </div>

                <UButton icon="i-lucide-refresh-cw" color="neutral" variant="outline" :loading="loading"
                    @click="loadDashboard">
                    Refresh
                </UButton>
            </div>
        </div>

        <!-- Error -->
        <UAlert v-if="error" color="error" variant="soft" icon="i-lucide-alert-circle" :title="error" />

        <!-- Statistics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <UCard>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Devices</p>
                        <p class="mt-2 text-3xl font-bold">
                            {{ totalDevices }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-blue-100 p-3 text-blue-600">
                        <UIcon name="i-lucide-monitor" class="size-6" />
                    </div>
                </div>
            </UCard>

            <UCard>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Devices Online</p>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ onlineDevices }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-green-100 p-3 text-green-600">
                        <UIcon name="i-lucide-monitor-check" class="size-6" />
                    </div>
                </div>
            </UCard>

            <UCard>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Devices Offline</p>
                        <p class="mt-2 text-3xl font-bold text-red-600">
                            {{ offlineDevices }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-red-100 p-3 text-red-600">
                        <UIcon name="i-lucide-monitor-x" class="size-6" />
                    </div>
                </div>
            </UCard>

            <UCard>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Contents</p>
                        <p class="mt-2 text-3xl font-bold">
                            {{ totalContents }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-purple-100 p-3 text-purple-600">
                        <UIcon name="i-lucide-images" class="size-6" />
                    </div>
                </div>
            </UCard>
        </div>

        <!-- Recent Devices -->
        <UCard>
            <template #header>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">
                        Recent Devices
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Perangkat yang terakhir ditambahkan.
                    </p>
                </div>
            </template>

            <!-- Loading -->
            <div v-if="loading && recentDevices.length === 0"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <USkeleton v-for="i in 3" :key="i" class="h-32 w-full" />
            </div>

            <!-- Empty -->
            <div v-else-if="recentDevices.length === 0" class="py-10 text-center">
                <UIcon name="i-lucide-monitor-x" class="mx-auto size-10 text-gray-400" />

                <p class="mt-3 font-medium">
                    Belum ada device
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    Device yang ditambahkan akan muncul di sini.
                </p>
            </div>

            <!-- Device Cards -->
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div v-for="device in recentDevices" :key="device.id"
                    class="rounded-xl border border-gray-200 p-4 transition hover:border-primary-400 hover:shadow-sm dark:border-gray-800">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="rounded-lg bg-gray-100 p-3 dark:bg-gray-800">
                                <UIcon name="i-lucide-monitor" class="size-5 text-gray-600 dark:text-gray-300" />
                            </div>

                            <div class="min-w-0">
                                <h3 class="truncate font-semibold text-gray-900 dark:text-white">
                                    {{ device.name }}
                                </h3>

                                <p class="truncate text-sm text-gray-500">
                                    {{ device.location || 'Lokasi belum diatur' }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ device.contents_count }} Content
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <UBadge :color="device.status === 'online' ? 'success' : 'error'" variant="subtle">
                                <span class="mr-1.5 inline-block size-1.5 rounded-full bg-current" />
                                {{ device.status }}
                            </UBadge>

                            <UDropdownMenu :items="items(device.id)" :content="{
                                align: 'end',
                                side: 'bottom',
                                sideOffset: 8,
                            }" :ui="{
                                content: 'w-52',
                            }">
                                <UButton icon="i-lucide-ellipsis-vertical" color="neutral" variant="ghost" size="sm" />
                            </UDropdownMenu>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-100 pt-3 dark:border-gray-800">
                        <div class="flex items-center justify-between gap-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <UIcon name="i-lucide-clock" class="size-3.5" />
                                Last seen
                            </span>

                            <span>
                                {{
                                    device.last_seen
                                        ? new Date(device.last_seen).toLocaleString('id-ID')
                                        : 'Belum pernah terhubung'
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </UCard>

        <!-- Manage Content Modal -->
        <UModal v-model:open="isContentModalOpen" :ui="{ content: 'sm:max-w-3xl' }">
            <template #content>
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold">
                                Manage Content
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                {{
                                    deviceContents?.device.name
                                    || 'Mengambil data device...'
                                }}
                            </p>
                        </div>

                        <UButton icon="i-lucide-x" color="neutral" variant="ghost"
                            @click="isContentModalOpen = false" />
                    </div>

                    <!-- Loading -->
                    <div v-if="contentLoading" class="mt-6 space-y-3">
                        <USkeleton class="h-16 w-full" />
                        <USkeleton class="h-16 w-full" />
                        <USkeleton class="h-16 w-full" />
                    </div>

                    <template v-else-if="deviceContents">
                        <!-- Assigned Contents -->
                        <div class="mt-6">
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="font-semibold">
                                    Assigned Contents
                                </h3>

                                <UBadge color="success" variant="subtle">
                                    {{ deviceContents.assigned_contents.length }}
                                </UBadge>
                            </div>

                            <div v-if="deviceContents.assigned_contents.length === 0"
                                class="rounded-lg border border-dashed p-6 text-center">
                                <UIcon name="i-lucide-images" class="mx-auto size-8 text-gray-400" />

                                <p class="mt-2 text-sm text-gray-500">
                                    Belum ada content untuk device ini.
                                </p>
                            </div>

                            <div v-else class="space-y-2">
                                <div v-for="content in deviceContents.assigned_contents" :key="content.id"
                                    class="flex items-center justify-between gap-3 rounded-lg border p-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <UIcon name="i-lucide-file" class="size-5 shrink-0 text-gray-500" />

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium">
                                                {{ content.title }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ content.type }}
                                            </p>
                                        </div>
                                    </div>

                                    <UButton icon="i-lucide-trash-2" color="error" variant="ghost" size="sm"
                                        :loading="removingContentId === content.id"
                                        :disabled="saving || !!removingContentId"
                                        @click="handleDetachContent(content.id)" />
                                </div>
                            </div>
                        </div>

                        <UDivider class="my-6" />

                        <!-- Available Contents -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <h3 class="font-semibold">
                                    Available Contents
                                </h3>

                                <UBadge color="info" variant="subtle">
                                    {{ deviceContents.available_contents.length }}
                                </UBadge>
                            </div>

                            <div v-if="deviceContents.available_contents.length === 0"
                                class="rounded-lg border border-dashed p-6 text-center">
                                <p class="text-sm text-gray-500">
                                    Tidak ada content lain yang tersedia.
                                </p>
                            </div>

                            <div v-else class="space-y-2">
                                <div v-for="content in deviceContents.available_contents" :key="content.id"
                                    class="flex items-center justify-between gap-3 rounded-lg border p-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <UIcon name="i-lucide-file" class="size-5 shrink-0 text-gray-500" />

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium">
                                                {{ content.title }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ content.type }}
                                            </p>
                                        </div>
                                    </div>

                                    <UButton icon="i-lucide-plus" color="primary" variant="soft" size="sm"
                                        :loading="saving" :disabled="!!removingContentId"
                                        @click="handleAttachContent(content.id)">
                                        Add
                                    </UButton>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </UModal>
    </div>
</template>
