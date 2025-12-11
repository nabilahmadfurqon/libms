<header class="sticky top-0 z-40 bg-white/70 dark:bg-slate-900/60 backdrop-blur
               border-b border-slate-200/70 dark:border-white/10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button id="btnSidebar" class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-xl
              bg-slate-900/5 dark:bg-white/10 hover:bg-slate-900/10 dark:hover:bg-white/20 transition"
              aria-label="Toggle Sidebar">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4zM4 11h16v2H4zM4 16h10v2H4z"/></svg>
      </button>
      <img src="/assets/logo-yapi.png" class="h-8 w-8 rounded-full ring-1 ring-slate-200 dark:ring-white/10 bg-white" alt="YAPI">
      <img src="/assets/logo-alazhar13.png" class="h-8 w-8 rounded-full ring-1 ring-slate-200 dark:ring-white/10 bg-white" alt="Al Azhar 13">
      <span class="font-semibold tracking-wide">LibMS</span>
      <span class="text-xs ml-2 px-2 py-0.5 rounded-full bg-indigo-600/10 text-indigo-700
                   dark:bg-indigo-400/10 dark:text-indigo-300 ring-1 ring-indigo-600/20 dark:ring-indigo-400/20">
        {{ strtoupper(auth()->user()->role ?? '-') }}
      </span>
    </div>

    <div class="hidden md:flex items-center gap-3">
      <button id="btnTheme"
              class="rounded-xl px-3 py-2 text-sm font-medium bg-slate-900/5 dark:bg-white/10
                     hover:bg-slate-900/10 dark:hover:bg-white/20 transition">Tema</button>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="rounded-xl px-3 py-2 text-sm bg-rose-600 text-white hover:bg-rose-500 transition">Keluar</button>
      </form>
    </div>
  </div>
</header>
