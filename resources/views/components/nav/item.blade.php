@props(['href' => '#', 'icon' => 'bar'])
@php
$icons = [
  'bar'  => 'M3 3h4v18H3zM10 10h4v11h-4zM17 6h4v15h-4z',
  'users'=> 'M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm-7 9a7 7 0 0 1 14 0v1H5Z',
  'doc'  => 'M4 4h16v4H4zM6 10h12v10H6z',
  'list' => 'M3 6h18v2H3zM3 11h18v2H3zM3 16h12v2H3z',
  'user' => 'M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0v1H5z',
];
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge([
      'class' =>
      'flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-900/5 dark:hover:bg-white/10 transition'
   ]) }}>
  <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-500/15 text-indigo-400">
    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
      <path d="{{ $icons[$icon] ?? $icons['bar'] }}"/>
    </svg>
  </span>
  <span class="truncate">{{ $slot }}</span>
</a>
