<script setup lang="ts">
defineProps<{
    total: number
    images: number
    videos: number
    others: number
}>()
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <UCard>
            <p class="text-sm text-neutral-500">Total Content</p>
            <p class="mt-2 text-3xl font-bold">{{ total }}</p>
        </UCard>

        <UCard>
            <p class="text-sm text-neutral-500">Images</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ images }}</p>
        </UCard>

        <UCard>
            <p class="text-sm text-neutral-500">Videos</p>
            <p class="mt-2 text-3xl font-bold text-purple-600">{{ videos }}</p>
        </UCard>

        <UCard>
            <p class="text-sm text-neutral-500">Other Content</p>
            <p class="mt-2 text-3xl font-bold text-orange-600">{{ others }}</p>
        </UCard>
    </div>
</template>
