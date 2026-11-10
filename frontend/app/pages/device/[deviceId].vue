<script setup lang="ts">
import { onMounted, onUnmounted, computed, ref } from 'vue'

definePageMeta({
    layout: false,
})

const route = useRoute()
const config = useRuntimeConfig()

const deviceId = String(route.params.deviceId)

const {
    deviceClient,
    contents,
    currentContent,
    loading,
    error,
    loadDeviceClient,
    nextContent,
} = useDeviceClient()

// Fullscreen
const isFullscreen = ref(false)

const toggleFullscreen = async () => {
    try {
        if (!document.fullscreenElement) {
            await document.documentElement.requestFullscreen()
        } else {
            await document.exitFullscreen()
        }
    } catch (err) {
        console.error('Gagal mengubah mode fullscreen:', err)
    }
}

const handleFullscreenChange = () => {
    isFullscreen.value = !!document.fullscreenElement
}

// Content URL
const contentUrl = computed(() => {
    const content = currentContent.value

    if (!content) return ''

    const payload = content.payload as Record<string, unknown> | null

    if (typeof content.url === 'string') {
        return content.url
    }

    if (typeof payload?.url === 'string') {
        return payload.url
    }

    return ''
})

// HTML Content
const htmlContent = computed(() => {
    const content = currentContent.value

    if (!content) return ''

    const payload = content.payload as Record<string, unknown> | null

    return typeof payload?.html === 'string'
        ? payload.html
        : ''
})

// Video selesai
const handleVideoEnded = () => {
    nextContent()
}

// Intervals
let heartbeatInterval: ReturnType<typeof setInterval> | null = null
let contentInterval: ReturnType<typeof setInterval> | null = null
let slideInterval: ReturnType<typeof setInterval> | null = null

// Heartbeat
const sendHeartbeat = async () => {
    try {
        await $fetch(
            `${config.public.apiBase}/device-client/${deviceId}/heartbeat`,
            {
                method: 'POST',
            },
        )
    } catch (err) {
        console.error('Heartbeat gagal:', err)
    }
}

onMounted(async () => {
    document.addEventListener('fullscreenchange', handleFullscreenChange)

    // Load data pertama kali
    await loadDeviceClient(deviceId)

    // Kirim heartbeat pertama kali
    sendHeartbeat()

    // Heartbeat setiap 5 detik
    heartbeatInterval = setInterval(() => {
        sendHeartbeat()
    }, 5000)

    // Refresh konten setiap 30 detik
    contentInterval = setInterval(() => {
        loadDeviceClient(deviceId)
    }, 30000)

    // Ganti slide setiap 15 detik
    slideInterval = setInterval(() => {
        if (currentContent.value?.type !== 'video') {
            if (contents.value.length > 1) {
                nextContent()
            }
        }
    }, 15000)
})

onUnmounted(() => {
    document.removeEventListener('fullscreenchange', handleFullscreenChange)

    if (heartbeatInterval) {
        clearInterval(heartbeatInterval)
    }

    if (contentInterval) {
        clearInterval(contentInterval)
    }

    if (slideInterval) {
        clearInterval(slideInterval)
    }
})
</script>

<template>
    <main class="device-screen">
        <!-- Tombol Fullscreen: selalu terlihat -->
        <button class="fullscreen-button" type="button" @click="toggleFullscreen">
            <span v-if="isFullscreen">
                Exit Fullscreen
            </span>

            <span v-else>
                Fullscreen
            </span>
        </button>

        <!-- Loading -->
        <div v-if="loading" class="message">
            <p>Loading content...</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="message">
            <p>{{ error }}</p>
        </div>

        <!-- Tidak ada konten -->
        <div v-else-if="!currentContent" class="message">
            <p>No content available</p>
        </div>

        <!-- Konten -->
        <template v-else>
            <!-- Image -->
            <img v-if="currentContent.type === 'image'" :src="contentUrl" class="media-content" alt="Device Content" />

            <!-- Video -->
            <video v-else-if="currentContent.type === 'video'" :src="contentUrl" class="media-content" autoplay muted
                playsinline @ended="handleVideoEnded" />

            <!-- HTML -->
            <iframe v-else-if="currentContent.type === 'html'" :srcdoc="htmlContent" class="iframe-content"
                title="HTML Content" />

            <!-- URL -->
            <iframe v-else-if="currentContent.type === 'url'" :src="contentUrl" class="iframe-content"
                title="Web Content" />

            <!-- Format tidak dikenal -->
            <div v-else class="message">
                <p>Unsupported content type</p>
            </div>
        </template>
    </main>
</template>

<style>
html,
body,
#__nuxt {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

.device-screen {
    position: relative;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
    background: #000;
}

/* Tombol Fullscreen */
.fullscreen-button {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999999;

    padding: 10px 18px;

    color: #fff;
    background: rgba(30, 30, 30, 0.85);

    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 8px;

    font-size: 14px;
    font-weight: 600;

    cursor: pointer;

    opacity: 1;
    visibility: visible;
    display: block;

    transition: background 0.2s ease;
}

.fullscreen-button:hover {
    background: rgba(70, 70, 70, 0.95);
}

.fullscreen-button:active {
    transform: scale(0.97);
}

/* Image dan Video */
.media-content {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000;
}

/* Iframe */
.iframe-content {
    display: block;
    width: 100%;
    height: 100%;
    border: none;
    background: #fff;
}

/* Loading dan Error */
.message {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 100%;
    height: 100%;

    color: #fff;
    background: #000;

    font-size: 20px;
    text-align: center;
}
</style>
