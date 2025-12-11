@props(['title' => '', 'value' => '—', 'icon' => '📌'])

<div class="rounded-2xl p-4 md:p-5
            bg-white/85 dark:bg-slate-900/80
            ring-1 ring-slate-200/70 dark:ring-white/10
            shadow-glass">
  <div class="flex items-center justify-between">
    <div class="text-sm text-slate-500 dark:text-slate-400">{{ $title }}</div>
    <div class="text-2xl">{{ $icon }}</div>
  </div>
  <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">
    {{ $value }}
  </div>
</div>
