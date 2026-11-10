<template>
  <div
    class="relative flex min-h-[460px] flex-col justify-between overflow-hidden bg-primary-600 p-8 text-white md:p-10">
    <!-- Brand Name -->
    <div class="z-10 text-lg font-bold">
      Signage Panel
    </div>

    <!-- Hero Text -->
    <div class="z-10 my-auto max-w-xs py-6">
      <h1 class="text-2xl font-bold leading-tight md:text-3xl">
        Kelola Display,<br>
        Real-time & Sentralisasi.
      </h1>
      <p class="mt-3 text-xs leading-relaxed opacity-80">
        Monitor status device online/offline, atur playlist, dan push konten ke layar secara langsung dari dashboard.
      </p>
    </div>

    <!-- Ilustrasi Device / Screen Visual -->
    <div class="pointer-events-none absolute -bottom-10 -right-10 z-0">
      <div
        class="relative flex size-56 items-center justify-center rounded-2xl border-4 border-indigo-400/30 bg-slate-900 text-3xl font-black text-indigo-400 shadow-lg">
        <UIcon name="i-heroicons-tv" class="size-24 text-indigo-300 opacity-80" />
        <div class="absolute -top-10 -left-8 size-24 rounded-full bg-indigo-500/40 blur-sm" />
        <div class="absolute -bottom-4 -left-12 size-20 rounded-full bg-teal-500/40 blur-sm" />
      </div>
    </div>
  </div>
</template>