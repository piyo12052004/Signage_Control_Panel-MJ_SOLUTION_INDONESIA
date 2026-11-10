
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig()

    const echo = new Echo({
        broadcaster: 'reverb',
        key: config.public.reverbAppKey as string,

        wsHost: config.public.reverbHost as string,
        wsPort: Number(config.public.reverbPort),
        wssPort: Number(config.public.reverbPort),

        forceTLS: config.public.reverbScheme === 'https',

        enabledTransports: ['ws'],
    })

    return {
        provide: {
            echo,
        },
    }
})
