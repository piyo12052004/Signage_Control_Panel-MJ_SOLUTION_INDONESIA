<script lang="ts" setup>
</script>

<template>
  <div class="flex min-h-screen bg-slate-50 dark:bg-slate-950">
    <AppSidebar />
    <div class="flex flex-1 flex-col">
      <AppHeader />
      <main class="flex-1 ">
        <slot />
      </main>
    </div>
  </div>
</template>