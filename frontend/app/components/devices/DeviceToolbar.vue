<script setup lang="ts">
const search = defineModel<string>('search', { required: true })
const status = defineModel<string>('status', { required: true })

defineEmits<{
    create: []
}>()
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-semibold text-neutral-900 dark:text-white">
                Daftar Perangkat
            </h2>
            <p class="mt-1 text-xs text-neutral-500">
                Kelola seluruh perangkat digital signage.
            </p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <UInput v-model="search" icon="i-lucide-search" placeholder="Cari perangkat..." class="sm:w-64" />

            <USelect v-model="status" :items="[
                { label: 'Semua Status', value: 'all' },
                { label: 'Online', value: 'online' },
                { label: 'Offline', value: 'offline' }
            ]" class="sm:w-40" />

            <!-- <UButton icon="i-lucide-plus" color="primary" @click="$emit('create')">
                Tambah
            </UButton> -->
        </div>
    </div>
</template>