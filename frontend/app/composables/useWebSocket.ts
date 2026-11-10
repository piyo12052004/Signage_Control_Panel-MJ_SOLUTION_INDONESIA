interface DeviceStatusEvent {
    device_id: string
    name: string
    status: 'online' | 'offline'
    last_seen: string | null
}

export const useWebSocket = () => {
    const { $echo } = useNuxtApp()

    const connected = useState<boolean>(
        'websocket-connected',
        () => false,
    )

    const listenDeviceStatus = (
        callback: (event: DeviceStatusEvent) => void,
    ) => {
        const channel = $echo.channel('devices')
        const connection = $echo.connector.pusher.connection

        const updateConnection = () => {
            connected.value = connection.state === 'connected'
        }

        channel.listen(
            '.device.status.updated',
            (event: DeviceStatusEvent) => {
                console.log('[WebSocket]')
                callback(event)
            },
        )

        connection.bind('connected', updateConnection)
        connection.bind('disconnected', updateConnection)
        connection.bind('unavailable', updateConnection)

        updateConnection()

        return () => {
            channel.stopListening('.device.status.updated')
            $echo.leave('devices')

            connection.unbind('connected', updateConnection)
            connection.unbind('disconnected', updateConnection)
            connection.unbind('unavailable', updateConnection)

            connected.value = false
        }
    }

    return {
        connected,
        listenDeviceStatus,
    }
}
