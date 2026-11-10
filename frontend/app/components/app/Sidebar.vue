<script setup lang="ts">
const { isOpen } = useSidebar()
const { navItems, settingsItems } = useMenuSidebar()
</script>

<template>
  <aside
    class="relative flex flex-col justify-between border-r border-neutral-200/80 bg-white p-4 transition-all duration-300 dark:border-neutral-800 dark:bg-neutral-900"
    :class="isOpen ? 'w-64' : 'w-20'">
    <!-- Hilangkan overflow-y-auto yang terlalu ketat di container atas -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden pr-1">
      <!-- Brand Logo -->
      <div class="flex items-center justify-between px-2 py-3">
        <div class="flex items-center gap-3">
          <div
            class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-600 font-bold text-white dark:bg-primary-500">
            S
          </div>
          <span v-if="isOpen" class="text-lg font-bold text-neutral-900 dark:text-white">
            Signage Control Pane
          </span>
        </div>
      </div>

      <!-- Navigation Sections -->
      <div class="mt-6 space-y-6">
        <!-- Main Navigation -->
        <nav class="space-y-1">
          <NuxtLink v-for="item in navItems" :key="item.to" :to="item.to"
            class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all"
            active-class="bg-primary-50 text-primary-600 font-semibold dark:bg-primary-950/50 dark:text-primary-400"
            inactive-class="text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/60 dark:hover:text-neutral-200"
            :title="!isOpen ? item.label : undefined">
            <!-- Wrapper icon dipastikan shrink-0 dan memiliki ukuran pasti -->
            <div class="flex size-6 shrink-0 items-center justify-center">
              <UIcon :name="item.icon"
                class="size-5 transition-colors group-[.router-link-active]:text-primary-600 dark:group-[.router-link-active]:text-primary-400" />
            </div>
            <span v-if="isOpen" class="truncate">{{ item.label }}</span>
          </NuxtLink>
        </nav>
        <!-- Settings Section -->
        <div>
          <p v-if="isOpen"
            class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase dark:text-neutral-500">
            Pengaturan
          </p>
          <nav class="space-y-1">
            <NuxtLink v-for="item in settingsItems" :key="item.to" :to="item.to"
              class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all"
              active-class="bg-primary-50 text-primary-600 font-semibold dark:bg-primary-950/50 dark:text-primary-400"
              inactive-class="text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/60 dark:hover:text-neutral-200"
              :title="!isOpen ? item.label : undefined">
              <div class="flex size-6 shrink-0 items-center justify-center">
                <UIcon :name="item.icon"
                  class="size-5 transition-colors group-[.router-link-active]:text-primary-600 dark:group-[.router-link-active]:text-primary-400" />
              </div>
              <span v-if="isOpen" class="truncate">{{ item.label }}</span>
            </NuxtLink>
          </nav>
        </div>
      </div>
    </div>

  </aside>
</template>