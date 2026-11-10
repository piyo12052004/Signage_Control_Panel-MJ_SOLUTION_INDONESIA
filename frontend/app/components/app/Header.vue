<script setup lang="ts">
const { toggle } = useSidebar()
const authStore = useAuthStore()

const userName = computed(() => authStore.user?.name || 'User')

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase()
})
</script>

<template>
  <header
    class="flex items-center justify-between border-b border-neutral-200/60 bg-neutral-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950">
    <div class="flex items-center gap-3">
      <UButton color="neutral" variant="ghost" icon="i-lucide-menu" size="md" @click="toggle" />
    </div>

    <div class="flex items-center gap-4">
      <!-- User Profile -->
      <div class="flex items-center gap-2.5">
        <UAvatar :text="userInitial" size="sm" class="bg-primary-800 font-semibold text-white dark:bg-primary-800" />

        <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
          {{ userName }}
        </span>
      </div>
    </div>
  </header>
</template>