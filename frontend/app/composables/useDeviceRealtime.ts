interface DeviceStatusEvent {
    device_id: string
    name: string
    status: 'online' | 'offline'
    last_seen: string | null
}

interface DeviceStatusItem {
    id: string
    name: string
    status: 'online' | 'offline'
    last_seen: string | null
}

export const useDeviceRealtime = (
    devices: Ref<DeviceStatusItem[]>,
) => {
    const { $echo } = useNuxtApp()

    const connected = ref(false)

    let channel: ReturnType<typeof $echo.channel> | null = null

    const handleStatusUpdated = (
        event: DeviceStatusEvent,
    ) => {
        const device = devices.value.find(
            item => item.id === event.device_id,
        )

        if (!device) return

        device.status = event.status
        device.last_seen = event.last_seen
    }

    onMounted(() => {
        channel = $echo.channel('devices')

        channel
            .listen(
                '.device.status.updated',
                handleStatusUpdated,
            )

        // Status koneksi WebSocket.
        const connector = $echo.connector

        connector.pusher.connection.bind(
            'connected',
            () => {
                connected.value = true
            },
        )

        connector.pusher.connection.bind(
            'disconnected',
            () => {
                connected.value = false
            },
        )

        connector.pusher.connection.bind(
            'unavailable',
            () => {
                connected.value = false
            },
        )

        connected.value =
            connector.pusher.connection.state === 'connected'
    })

    onBeforeUnmount(() => {
        $echo.leave('devices')
        connected.value = false
    })

    return {
        connected,
    }
}