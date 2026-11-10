<script setup lang="ts">
import type { Device } from '~/types/type-device'

defineProps<{
    devices: Device[]
}>()

defineEmits<{
    edit: [device: Device]
    delete: [device: Device]
}>()

const formatLastSeen = (value: string | null) => {
    if (!value) return 'Belum pernah aktif'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return 'Waktu tidak valid'
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date)
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-neutral-200 text-xs text-neutral-500 dark:border-neutral-800">
                    <!-- <th class="px-4 py-3 font-medium">ID</th> -->
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Lokasi</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Last Seen</th>
                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="device in devices" :key="device.id"
                    class="border-b border-neutral-100 last:border-0 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-900/50">
                    <!-- <td class="max-w-32 truncate px-4 py-4 font-mono text-xs text-neutral-500" :title="device.id">
                        {{ device.id }}
                    </td> -->

                    <td class="px-4 py-4 font-semibold">
                        {{ device.name }}
                    </td>

                    <td class="px-4 py-4 text-neutral-600 dark:text-neutral-400">
                        {{ device.location }}
                    </td>

                    <td class="px-4 py-4">
                        <UBadge :color="device.status === 'online' ? 'success' : 'error'" variant="subtle">
                            {{ device.status === 'online' ? 'Online' : 'Offline' }}
                        </UBadge>
                    </td>

                    <td class="whitespace-nowrap px-4 py-4 text-neutral-500">
                        {{ formatLastSeen(device.last_seen) }}
                    </td>

                    <td class="px-4 py-4">
                        <div class="flex justify-end gap-1">
                            <UButton color="neutral" variant="ghost" icon="i-lucide-pencil" size="sm"
                                aria-label="Edit device" @click="$emit('edit', device)" />

                            <UButton color="error" variant="ghost" icon="i-lucide-trash-2" size="sm"
                                aria-label="Delete device" @click="$emit('delete', device)" />
                        </div>
                    </td>
                </tr>

                <tr v-if="devices.length === 0">
                    <td colspan="6" class="px-4 py-12 text-center text-neutral-500">
                        Tidak ada perangkat ditemukan.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
