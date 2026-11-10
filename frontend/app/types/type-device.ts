export type DeviceStatus = 'online' | 'offline'

export interface Device {
    id: string
    name: string
    location: string
    status: DeviceStatus
    last_seen: string | null
    created_at: string
    updated_at: string
}

export interface DeviceForm {
    name: string
    location: string
}

export interface DevicePagination {
    current_page: number
    per_page: number
    total: number
    last_page: number
}

export interface DeviceListData {
    items: Device[]
}