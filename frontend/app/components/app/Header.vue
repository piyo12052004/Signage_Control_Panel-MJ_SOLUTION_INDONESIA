<script setup lang="ts">
import type { DropdownMenuItem } from '@nuxt/ui'

const { toggle } = useSidebar()
const authStore = useAuthStore()

const userName = computed(() => authStore.user?.name || 'User')

const userInitial = computed(() => {
  return userName.value.charAt(0).toUpperCase()
})

const handleLogout = async () => {
  await authStore.logout()
}

const dropdownItems: DropdownMenuItem[][] = [
  [
    {
      label: 'Logout',
      icon: 'i-lucide-log-out',
      color: 'error',
      onSelect: handleLogout,
    },
  ],
]
</script>

<template>
  <header
    class="flex items-center justify-between border-b border-neutral-200/60 bg-neutral-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950">
    <!-- Sidebar Toggle -->
    <div class="flex items-center gap-3">
      <UButton color="neutral" variant="ghost" icon="i-lucide-menu" size="md" @click="toggle" />
    </div>

    <!-- User Profile Dropdown -->
    <div class="flex items-center gap-4">
      <UDropdownMenu :items="dropdownItems" :content="{ align: 'end', side: 'bottom' }">
        <UButton color="neutral" variant="ghost" class="flex items-center gap-2.5">
          <UAvatar :text="userInitial" size="sm" class="bg-primary-600 font-bold text-white dark:bg-primary-500" />

          <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
            {{ userName }}
          </span>

          <!-- <UIcon name="i-lucide-chevron-down" class="size-4 text-neutral-500" /> -->
        </UButton>
      </UDropdownMenu>
    </div>
  </header>
</template>
