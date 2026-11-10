export interface DeviceClientDevice {
    id: string
    name: string
    location: string
    status: 'online' | 'offline'
}

export interface DeviceClientContent {
    id: string
    title: string
    type: 'image' | 'video' | 'html' | 'url'
    payload: Record<string, unknown> | null
    url: string | null
    created_at: string
}

export interface DeviceClientData {
    device: DeviceClientDevice
    contents: DeviceClientContent[]
}