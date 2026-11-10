<script setup lang="ts">
const search = defineModel<string>('search', { default: '' })
const type = defineModel<string>('type', { default: 'all' })

defineEmits<{
    create: []
}>()
</script>

<template>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row">
            <UInput v-model="search" icon="i-lucide-search" placeholder="Cari judul content..."
                class="w-full sm:w-72" />

            <USelect v-model="type" :items="[
                { label: 'Semua tipe', value: 'all' },
                { label: 'Image', value: 'image' },
                { label: 'HTML', value: 'html' },
            ]" value-key="value" class="w-full sm:w-40" />
        </div>

        <!-- <UButton icon="i-lucide-plus" @click="$emit('create')">
            Tambah Content
        </UButton> -->
    </div>
</template>
