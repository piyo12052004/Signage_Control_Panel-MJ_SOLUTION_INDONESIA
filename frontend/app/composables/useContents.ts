import { computed, ref, watch } from 'vue'

import type {
    Content,
    ContentForm,
    ContentListData,
    ContentPagination,
} from '~/types/type-content'

import type { ApiResponse } from '~/types/type-api'

export const useContents = () => {
    const { api } = useApi()
    const notify = useNotify()

    const contents = ref<Content[]>([])

    const search = ref('')
    const type = ref('all')

    const modalOpen = ref(false)
    const selectedContent = ref<Content | null>(null)

    const loading = ref(false)
    const saving = ref(false)
    const deleting = ref(false)

    const error = ref<string | null>(null)

    const pagination = ref<ContentPagination>({
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 1,
    })

    const getErrorMessage = (
        err: any,
        fallback: string,
    ) => {
        return (
            err?.data?.error?.message ||
            err?.data?.message ||
            err?.message ||
            fallback
        )
    }

    // Fetch content
    const fetchContents = async () => {
        loading.value = true
        error.value = null

        try {
            const response = await api<
                ApiResponse<ContentListData> & {
                    meta?: ContentPagination
                }
            >('/setting/contents', {
                method: 'GET',
                query: {
                    page: pagination.value.current_page,
                    per_page: pagination.value.per_page,
                    search: search.value || undefined,
                    type: type.value === 'all'
                        ? undefined
                        : type.value,
                    sort_by: 'created_at',
                    sort_direction: 'desc',
                },
            })

            contents.value = response.data?.items ?? []

            pagination.value = {
                current_page: response.meta?.current_page ?? 1,
                per_page: response.meta?.per_page ?? 10,
                total: response.meta?.total ?? 0,
                last_page: response.meta?.last_page ?? 1,
            }

            return response.data
        } catch (err: any) {
            error.value = getErrorMessage(
                err,
                'Gagal mengambil data content.',
            )

            notify.error(error.value)

            throw err
        } finally {
            loading.value = false
        }
    }

    // Open create modal
    const openCreate = () => {
        selectedContent.value = null
        modalOpen.value = true
    }

    // Open edit modal
    const openEdit = (content: Content) => {
        selectedContent.value = { ...content }
        modalOpen.value = true
    }

    // Close modal
    const closeModal = () => {
        modalOpen.value = false
        selectedContent.value = null
    }

    // Create / Update
    const saveContent = async (form: ContentForm) => {
        saving.value = true

        const isEditing = Boolean(selectedContent.value)

        try {
            const formData = new FormData()

            formData.append('title', form.title)
            formData.append('type', form.type)

            if (form.file) {
                formData.append('file', form.file)
            }

            if (form.url) {
                formData.append('url', form.url)
            }

            if (form.payload) {
                formData.append(
                    'payload',
                    JSON.stringify(form.payload),
                )
            }

            if (isEditing && selectedContent.value) {
                formData.append('_method', 'PUT')

                await api(
                    `/setting/contents/${selectedContent.value.id}`,
                    {
                        method: 'POST',
                        body: formData,
                    },
                )
            } else {
                await api('/setting/contents', {
                    method: 'POST',
                    body: formData,
                })
            }

            notify.success(
                isEditing
                    ? 'Content berhasil diperbarui.'
                    : 'Content berhasil ditambahkan.',
            )

            // Refresh daftar secara terpisah.
            await fetchContents()

            return isEditing ? 'updated' : 'created'
        } catch (err: any) {
            notify.error(
                getErrorMessage(
                    err,
                    isEditing
                        ? 'Gagal memperbarui content.'
                        : 'Gagal menambahkan content.',
                ),
            )

            throw err
        } finally {
            saving.value = false
        }
    }

    // Delete
    const deleteContent = async (content: Content) => {
        deleting.value = true

        try {
            await api(
                `/setting/contents/${content.id}`,
                {
                    method: 'DELETE',
                },
            )

            if (
                contents.value.length === 1 &&
                pagination.value.current_page > 1
            ) {
                pagination.value.current_page--
            }

            notify.success('Content berhasil dihapus.')

            await fetchContents()
        } catch (err: any) {
            notify.error(
                getErrorMessage(
                    err,
                    'Gagal menghapus content.',
                ),
            )

            throw err
        } finally {
            deleting.value = false
        }
    }

    // Pagination
    const changePage = async (newPage: number) => {
        if (
            newPage < 1 ||
            newPage > pagination.value.last_page
        ) {
            return
        }

        pagination.value.current_page = newPage

        await fetchContents()
    }

    const totalContents = computed(
        () => pagination.value.total,
    )

    // Search dan filter
    let searchTimeout: ReturnType<typeof setTimeout> | undefined

    watch([search, type], () => {
        pagination.value.current_page = 1

        if (searchTimeout) {
            clearTimeout(searchTimeout)
        }

        searchTimeout = setTimeout(() => {
            fetchContents().catch(() => { })
        }, 400)
    })

    onScopeDispose(() => {
        if (searchTimeout) {
            clearTimeout(searchTimeout)
        }
    })

    return {
        contents,
        search,
        type,

        modalOpen,
        selectedContent,

        pagination,

        loading,
        saving,
        deleting,

        error,
        totalContents,

        fetchContents,

        openCreate,
        openEdit,
        closeModal,

        saveContent,
        deleteContent,

        changePage,
    }
}