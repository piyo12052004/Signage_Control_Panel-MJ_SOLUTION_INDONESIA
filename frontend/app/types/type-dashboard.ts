
import type { Device } from '~/types/type-device'
import type { Content } from '~/types/type-content'

export interface DashboardDevice extends Device {
    contents_count: number
}

export interface DashboardData {
    total_devices: number
    online_devices: number
    offline_devices: number
    total_contents: number
    recent_devices: DashboardDevice[]
}

export interface DashboardDeviceContents {
    device: {
        id: string
        name: string
        location: string
        status: 'online' | 'offline'
    }
    assigned_contents: Content[]
    available_contents: Content[]
}

export interface DashboardDevice {
    id: string
    name: string
    location: string
    status: 'online' | 'offline'
    last_seen: string | null
    contents_count: number
    created_at: string
}

export interface DashboardData {
    total_devices: number
    online_devices: number
    offline_devices: number
    total_contents: number
    recent_devices: DashboardDevice[]
}
