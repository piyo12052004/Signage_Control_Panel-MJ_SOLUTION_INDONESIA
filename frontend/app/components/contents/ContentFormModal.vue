<script setup lang="ts">
import type {
    Content,
    ContentForm,
    ContentType,
} from '~/types/type-content'

const props = withDefaults(defineProps<{
    open: boolean
    content?: Content | null
    loading?: boolean
}>(), {
    content: null,
    loading: false,
})

const emit = defineEmits<{
    'update:open': [value: boolean]
    save: [form: ContentForm]
}>()

const isEdit = computed(() => Boolean(props.content))

const form = reactive({
    title: '',
    type: 'image' as ContentType,
    url: '',
    payloadText: '{\n  "html": ""\n}',
    file: null as File | null,
})

const formError = ref<string | null>(null)

const fileInput = ref<HTMLInputElement | null>(null)

const usesFile = computed(() =>
    ['image', 'video'].includes(form.type),
)

const usesUrl = computed(() =>
    form.type === 'url',
)

const fileAccept = computed(() => {
    if (form.type === 'image') {
        return 'image/jpeg,image/png,image/webp,image/gif'
    }

    return 'video/mp4,video/webm,video/quicktime'
})

const currentFileName = computed(() => {
    if (form.file) {
        return form.file.name
    }

    if (props.content?.url && props.content.type === form.type) {
        return props.content.url.split('/').pop()
    }

    return null
})

watch(
    () => [props.open, props.content] as const,
    ([open, content]) => {
        if (!open) return

        formError.value = null

        form.title = content?.title ?? ''
        form.type = content?.type ?? 'image'
        form.url = content?.type === 'url'
            ? content.url ?? ''
            : ''

        form.file = null

        form.payloadText = content?.payload
            ? JSON.stringify(content.payload, null, 2)
            : '{\n  "html": ""\n}'

        if (fileInput.value) {
            fileInput.value.value = ''
        }
    },
    { immediate: true },
)

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement

    const selectedFile = target.files?.[0] ?? null

    form.file = selectedFile

    if (!selectedFile) return

    const maxSize = 100 * 1024 * 1024

    if (selectedFile.size > maxSize) {
        formError.value = 'Ukuran file maksimal 100 MB.'
        form.file = null
        target.value = ''
        return
    }

    if (
        form.type === 'image' &&
        !selectedFile.type.startsWith('image/')
    ) {
        formError.value = 'Silakan pilih file gambar.'
        form.file = null
        target.value = ''
        return
    }

    if (
        form.type === 'video' &&
        !selectedFile.type.startsWith('video/')
    ) {
        formError.value = 'Silakan pilih file video.'
        form.file = null
        target.value = ''
        return
    }

    formError.value = null
}

const submit = () => {
    if (props.loading) return

    formError.value = null

    if (!form.title.trim()) {
        formError.value = 'Judul content wajib diisi.'
        return
    }

    let payload: Record<string, unknown> | null = null

    if (form.type === 'html') {
        try {
            const parsed: unknown = JSON.parse(form.payloadText)

            if (
                parsed === null ||
                typeof parsed !== 'object' ||
                Array.isArray(parsed)
            ) {
                throw new Error('Payload harus berupa JSON object.')
            }

            payload = parsed as Record<string, unknown>
        } catch (error: any) {
            formError.value =
                error?.message || 'Format JSON payload tidak valid.'

            return
        }
    }

    if (usesFile.value) {
        const canKeepOldFile =
            isEdit.value &&
            props.content?.type === form.type &&
            Boolean(props.content.url)

        if (!form.file && !canKeepOldFile) {
            formError.value = 'Silakan pilih file terlebih dahulu.'
            return
        }
    }

    if (usesUrl.value && !form.url.trim()) {
        formError.value = 'URL wajib diisi.'
        return
    }

    emit('save', {
        title: form.title.trim(),
        type: form.type,
        url: usesUrl.value ? form.url.trim() : null,
        payload,
        file: form.file,
    })
}

const close = () => {
    if (props.loading) return

    emit('update:open', false)
}
</script>

<template>
    <UModal :open="open" :title="isEdit ? 'Edit Content' : 'Tambah Content'" :description="isEdit
        ? 'Perbarui informasi content.'
        : 'Tambahkan content baru.'" @update:open="emit('update:open', $event)">
        <template #body>
            <form class="space-y-4" @submit.prevent="submit">

                <!-- Judul -->
                <UFormField label="Judul" required>
                    <UInput v-model="form.title" placeholder="Masukkan judul content" class="w-full"
                        :disabled="loading" />
                </UFormField>

                <!-- Tipe -->
                <UFormField label="Tipe Content" required>
                    <USelect v-model="form.type" :items="[
                        { label: 'Image', value: 'image' },
                        { label: 'HTML', value: 'html' },
                    ]" value-key="value" class="w-full" :disabled="loading" />
                </UFormField>

                <!-- Upload Image / Video -->
                <UFormField v-if="usesFile" :label="form.type === 'image'
                    ? 'Upload Gambar'
                    : 'Upload Video'" required :description="form.type === 'image'
                ? 'JPG, PNG, WEBP. Maksimal 100 MB.'
                : '-'">
                    <div class="space-y-3">
                        <input ref="fileInput" type="file" :accept="fileAccept"
                            class="block w-full rounded-md border border-default p-2 text-sm" :disabled="loading"
                            @change="handleFileChange" />

                        <div v-if="currentFileName" class="rounded-lg border border-default p-3">
                            <div class="flex items-center gap-2">
                                <UIcon :name="form.type === 'image'
                                    ? 'i-lucide-image'
                                    : 'i-lucide-video'" class="size-5 text-primary" />

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">
                                        {{ form.file?.name || currentFileName }}
                                    </p>

                                    <p class="text-xs text-muted">
                                        {{ form.file
                                            ? `${(form.file.size / 1024 / 1024).toFixed(2)} MB`
                                        : 'File saat ini' }}
                                    </p>
                                </div>

                                <UBadge v-if="!form.file" color="success" variant="soft">
                                    Tersimpan
                                </UBadge>
                            </div>
                        </div>

                        <p v-if="isEdit && !form.file" class="text-xs text-muted">
                            Kosongkan pilihan file jika ingin tetap menggunakan file lama.
                        </p>
                    </div>
                </UFormField>

                <!-- URL -->
                <UFormField v-else-if="usesUrl" label="URL Content" required>
                    <UInput v-model="form.url" type="url" placeholder="https://example.com" class="w-full"
                        :disabled="loading" />
                </UFormField>

                <!-- HTML -->
                <UFormField v-else label="Payload JSON" required
                    description="Contoh: { &quot;html&quot;: &quot;&lt;h1&gt;Hello&lt;/h1&gt;&quot; }">
                    <UTextarea v-model="form.payloadText" :rows="7" class="w-full font-mono" :disabled="loading" />
                </UFormField>

                <!-- Error -->
                <UAlert v-if="formError" color="error" variant="soft" :description="formError" />

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-2">
                    <UButton color="neutral" variant="outline" :disabled="loading" @click="close">
                        Batal
                    </UButton>

                    <UButton type="submit" :loading="loading" :disabled="!form.title.trim()">
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Content' }}
                    </UButton>
                </div>

            </form>
        </template>
    </UModal>
</template>