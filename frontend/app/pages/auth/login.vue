<script setup lang="ts">
definePageMeta({
    layout: 'auth',
    middleware: 'guest'
})
</script>

<template>
    <div class="relative grid grid-cols-1 items-stretch md:grid-cols-2">
        <AuthLoginBranding />
        <AuthLoginForm />
    </div>
</template>