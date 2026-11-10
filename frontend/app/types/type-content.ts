export type ContentType = 'image' | 'video' | 'html'

export interface Content {
    id: string
    title: string
    type: ContentType
    payload: Record<string, unknown> | null
    url: string | null
    created_at: string
    updated_at: string
}

export interface ContentForm {
    title: string
    type: ContentType
    url: string | null
    payload: Record<string, unknown> | null
    file?: File | null
}

export interface ContentListData {
    items: Content[]
}

export interface ContentPagination {
    current_page: number
    per_page: number
    total: number
    last_page: number
}