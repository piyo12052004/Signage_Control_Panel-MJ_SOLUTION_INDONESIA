<script setup lang="ts">
defineProps<{
    total: number
    online: number
    offline: number
}>()
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <UCard>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-neutral-500">Total Perangkat</p>
                    <p class="mt-2 text-3xl font-bold">{{ total }}</p>
                </div>
                <div class="rounded-xl bg-primary-100 p-3 text-primary-600 dark:bg-primary-950">
                    <UIcon name="i-lucide-monitor" class="size-6" />
                </div>
            </div>
        </UCard>

        <UCard>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-neutral-500">Online</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ online }}</p>
                </div>
                <div class="rounded-xl bg-green-100 p-3 text-green-600 dark:bg-green-950">
                    <UIcon name="i-lucide-wifi" class="size-6" />
                </div>
            </div>
        </UCard>

        <UCard>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-neutral-500">Offline</p>
                    <p class="mt-2 text-3xl font-bold text-red-500">{{ offline }}</p>
                </div>
                <div class="rounded-xl bg-red-100 p-3 text-red-500 dark:bg-red-950">
                    <UIcon name="i-lucide-wifi-off" class="size-6" />
                </div>
            </div>
        </UCard>
    </div>
</template>
