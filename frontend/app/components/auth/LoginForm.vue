<script setup lang="ts">
const { state, errors, isLoading, handleLogin } = useLogin()
</script>

<template>
  <div class="flex flex-col justify-center bg-white p-8 dark:bg-neutral-900 md:p-10">
    <div class="mb-6">
      <h2 class="text-xl font-bold text-neutral-800 dark:text-white">
        Masuk ke Control Panel
      </h2>

      <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
        Kelola perangkat digital signage dan distribusi konten Anda.
      </p>
    </div>

    <UForm :state="state" class="space-y-4" @submit="handleLogin">
      <UFormField label="Email" name="email" required :error="errors.email">
        <UInput v-model="state.email" type="email" placeholder="admin@signage.id" size="md" class="w-full" />
      </UFormField>

      <UFormField label="Kata sandi" name="password" required :error="errors.password">
        <UInput v-model="state.password" type="password" placeholder="Masukkan kata sandi" size="md" class="w-full" />
      </UFormField>

      <p v-if="errors.general" class="text-sm text-red-500">
        {{ errors.general }}
      </p>

      <UButton type="submit" block size="md" color="primary" :loading="isLoading" :disabled="isLoading" class="mt-2">
        Masuk
      </UButton>
    </UForm>
  </div>
</template>