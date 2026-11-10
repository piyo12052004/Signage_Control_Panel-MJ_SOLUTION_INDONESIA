<script setup lang="ts">
import type { Content } from '~/types/type-content'

defineProps<{
    contents: Content[]
}>()

const emit = defineEmits<{
    edit: [content: Content]
    delete: [content: Content]
}>()

const formatDate = (value: string) => {
    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date)
}

const getTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        image: 'success',
        video: 'info',
        html: 'warning',
        url: 'secondary',
    }

    return colors[type] ?? 'neutral'
}

const getPayloadPreview = (content: Content) => {
    if (content.url) return content.url

    if (content.payload) {
        return JSON.stringify(content.payload)
    }

    return '-'
}
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-default">
        <table class="w-full text-sm">
            <thead class="bg-elevated">
                <tr class="text-left">
                    <!-- <th class="px-4 py-3">ID</th> -->
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Payload / URL</th>
                    <th class="px-4 py-3">Dibuat</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="content in contents" :key="content.id" class="border-t border-default">
                    <!-- <td class="max-w-32 truncate px-4 py-3 font-mono text-xs" :title="content.id">
                        {{ content.id }}
                    </td> -->

                    <td class="px-4 py-3 font-medium">
                        {{ content.title }}
                    </td>

                    <td class="px-4 py-3">
                        <UBadge :color="getTypeColor(content.type)">
                            {{ content.type }}
                        </UBadge>
                    </td>

                    <td class="max-w-64 truncate px-4 py-3 text-muted" :title="getPayloadPreview(content)">
                        {{ getPayloadPreview(content) }}
                    </td>

                    <td class="whitespace-nowrap px-4 py-3">
                        {{ formatDate(content.created_at) }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <UButton icon="i-lucide-pencil" color="neutral" variant="ghost" size="sm"
                                @click="emit('edit', content)" />

                            <UButton icon="i-lucide-trash-2" color="error" variant="ghost" size="sm"
                                @click="emit('delete', content)" />
                        </div>
                    </td>
                </tr>

                <tr v-if="contents.length === 0">
                    <td colspan="6" class="px-4 py-10 text-center text-muted">
                        Belum ada data content.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
