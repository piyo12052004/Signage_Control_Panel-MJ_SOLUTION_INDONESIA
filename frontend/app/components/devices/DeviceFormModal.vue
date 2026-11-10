<script setup lang="ts">
import type { Device, DeviceForm } from '~/types/type-device'

const props = defineProps<{
    open: boolean
    device?: Device | null
    loading?: boolean
}>()

const emit = defineEmits<{
    'update:open': [value: boolean]
    save: [form: DeviceForm]
}>()

const form = reactive<DeviceForm>({
    name: '',
    location: '',
})

const isEdit = computed(() => Boolean(props.device))

watch(
    () => [props.open, props.device] as const,
    ([open, device]) => {
        if (!open) return

        form.name = device?.name ?? ''
        form.location = device?.location ?? ''
    },
    { immediate: true },
)

const submit = () => {
    if (props.loading) return

    if (!form.name.trim() || !form.location.trim()) return

    emit('save', {
        name: form.name.trim(),
        location: form.location.trim(),
    })
}

const close = () => {
    if (props.loading) return

    emit('update:open', false)
}
</script>

<template>
    <UModal :open="open" :title="isEdit ? 'Edit Perangkat' : 'Tambah Perangkat'"
        description="Masukkan informasi perangkat digital signage." @update:open="close">
        <template #body>
            <form class="space-y-4" @submit.prevent="submit">
                <UFormField label="Nama Perangkat" required>
                    <UInput v-model="form.name" placeholder="Lobby Display 01" class="w-full" :disabled="loading" />
                </UFormField>

                <UFormField label="Lokasi" required>
                    <UInput v-model="form.location" placeholder="Main Lobby" class="w-full" :disabled="loading" />
                </UFormField>

                <div class="flex justify-end gap-2 pt-3">
                    <UButton color="neutral" variant="outline" type="button" :disabled="loading" @click="close">
                        Batal
                    </UButton>

                    <UButton type="submit" :loading="loading" :disabled="!form.name.trim() || !form.location.trim()">
                        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Perangkat' }}
                    </UButton>
                </div>
            </form>
        </template>
    </UModal>
</template>
