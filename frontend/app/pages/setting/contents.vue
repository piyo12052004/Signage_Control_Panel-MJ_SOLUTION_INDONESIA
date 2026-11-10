<script setup lang="ts">
import type { Content, ContentForm } from '~/types/type-content'

definePageMeta({
    middleware: 'auth',
})

const {
    contents,
    search,
    type,
    modalOpen,
    selectedContent,
    pagination,
    loading,
    saving,
    error,
    totalContents,
    fetchContents,
    openCreate,
    openEdit,
    closeModal,
    saveContent,
    deleteContent,
    changePage,
} = useContents()

const pageError = ref<string | null>(null)

const deleteModalOpen = ref(false)
const selectedContentToDelete = ref<Content | null>(null)
const deleting = ref(false)
const deleteError = ref<string | null>(null)

await useAsyncData(
    'setting-contents',
    async () => {
        await fetchContents()
        return true
    },
    {
        server: false,
    },
)

const handleRefresh = async () => {
    pageError.value = null

    try {
        await fetchContents()
    } catch {
        pageError.value = error.value
    }
}

const handleSave = async (form: ContentForm) => {
    pageError.value = null

    try {
        await saveContent(form)
        closeModal()
    } catch (err: any) {
        pageError.value =
            err?.data?.error?.message ||
            err?.data?.message ||
            err?.message ||
            'Gagal menyimpan content.'
    }
}

const openDeleteModal = (content: Content) => {
    selectedContentToDelete.value = content
    deleteError.value = null
    deleteModalOpen.value = true
}

const closeDeleteModal = () => {
    if (deleting.value) return

    deleteModalOpen.value = false
    selectedContentToDelete.value = null
    deleteError.value = null
}

const handleDelete = async () => {
    if (!selectedContentToDelete.value || deleting.value) return

    deleting.value = true
    deleteError.value = null

    try {
        await deleteContent(selectedContentToDelete.value)

        deleteModalOpen.value = false
        selectedContentToDelete.value = null
    } catch (err: any) {
        deleteError.value =
            err?.data?.error?.message ||
            err?.data?.message ||
            err?.message ||
            'Gagal menghapus content.'
    } finally {
        deleting.value = false
    }
}

const handlePageChange = async (newPage: number) => {
    if (
        newPage < 1 ||
        newPage > pagination.value.last_page ||
        loading.value
    ) {
        return
    }

    try {
        await changePage(newPage)
    } catch {
        // Error sudah ditangani oleh useContents.
    }
}

const firstItem = computed(() => {
    if (totalContents.value === 0) return 0

    return (
        (pagination.value.current_page - 1) *
        pagination.value.per_page + 1
    )
})

const lastItem = computed(() => {
    return Math.min(
        pagination.value.current_page * pagination.value.per_page,
        totalContents.value,
    )
})
</script>

<template>
    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">
                    Contents
                </h1>

                <p class="text-sm text-muted">
                    Kelola konten yang akan ditampilkan pada perangkat signage.
                </p>
            </div>

            <div class="flex gap-2">
                <UButton icon="i-lucide-refresh-cw" color="neutral" variant="outline" :loading="loading"
                    :disabled="loading" @click="handleRefresh">
                    Refresh
                </UButton>

                <UButton icon="i-lucide-plus" @click="openCreate">
                    Tambah Content
                </UButton>
            </div>
        </div>

        <!-- Error -->
        <UAlert v-if="pageError || error" color="error" variant="soft" :description="pageError || error || ''" />

        <!-- Toolbar -->
        <ContentsContentToolbar v-model:search="search" v-model:type="type" @create="openCreate" />

        <!-- Table -->
        <div class="space-y-4">
            <div v-if="loading" class="flex justify-center py-12">
                <UIcon name="i-lucide-loader-circle" class="size-8 animate-spin text-primary" />
            </div>

            <ContentsContentTable v-else :contents="contents" @edit="openEdit" @delete="openDeleteModal" />

            <!-- Pagination -->
            <div v-if="totalContents > 0" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-muted">
                    Menampilkan {{ firstItem }}–{{ lastItem }}
                    dari {{ totalContents }} content
                </p>

                <div class="flex items-center gap-2">
                    <UButton icon="i-lucide-chevron-left" color="neutral" variant="outline" size="sm"
                        :disabled="pagination.current_page <= 1 || loading"
                        @click="handlePageChange(pagination.current_page - 1)" />

                    <span class="text-sm">
                        Halaman {{ pagination.current_page }}
                        dari {{ pagination.last_page }}
                    </span>

                    <UButton icon="i-lucide-chevron-right" color="neutral" variant="outline" size="sm"
                        :disabled="pagination.current_page >= pagination.last_page || loading"
                        @click="handlePageChange(pagination.current_page + 1)" />
                </div>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <ContentsContentFormModal v-model:open="modalOpen" :content="selectedContent" :loading="saving"
            @save="handleSave" />

        <!-- Delete Confirmation -->
        <UModal :open="deleteModalOpen" title="Hapus Content"
            description="Apakah kamu yakin ingin menghapus content ini?" @update:open="!$event && closeDeleteModal()">
            <template #body>
                <div class="space-y-4">
                    <div class="rounded-lg border border-default p-4">
                        <p class="font-medium">
                            {{ selectedContentToDelete?.title }}
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            Tipe:
                            {{ selectedContentToDelete?.type }}
                        </p>
                    </div>

                    <UAlert color="warning" variant="soft" title="Perhatian"
                        description="Content yang dihapus tidak dapat dikembalikan." />

                    <UAlert v-if="deleteError" color="error" variant="soft" :description="deleteError" />

                    <div class="flex justify-end gap-2">
                        <UButton color="neutral" variant="outline" :disabled="deleting" @click="closeDeleteModal">
                            Batal
                        </UButton>

                        <UButton color="error" :loading="deleting" @click="handleDelete">
                            Hapus Content
                        </UButton>
                    </div>
                </div>
            </template>
        </UModal>
    </div>
</template>
