<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? (config('app.name').' — Masuk') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>:root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial}</style>

  <link rel="preload" as="image" href="/assets/bg-sekolah.jpeg" />

  <script>
    (function(){
      try {
        const pref = localStorage.getItem('theme');
        const wantsDark = pref === 'dark' || (!pref && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (wantsDark) document.documentElement.classList.add('dark');
      } catch(e) {}
    })();

    function toggleTheme(){
      const isDark = document.documentElement.classList.toggle('dark');
      try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('[data-anim-delay]').forEach(el => {
        const d = el.getAttribute('data-anim-delay');
        if (d) el.style.animationDelay = d;
      });
    });
  </script>

  {{-- HANYA CSS, TANPA JS BUNDLE --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-x-hidden bg-slate-100 dark:bg-slate-950">

  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-center bg-cover animate-blur-in"
         style="background-image:url('/assets/bg-sekolah.jpeg');"></div>
    <div class="absolute inset-0 bg-slate-900/55 dark:bg-slate-950/70 mix-blend-multiply"></div>
    <div class="absolute inset-0 pointer-events-none"
         style="background: radial-gradient(1200px 600px at 50% 20%, rgba(0,0,0,0) 40%, rgba(0,0,0,.35) 100%);"></div>
  </div>

  <header class="relative z-10 animate-fade-right" data-anim-delay="0s">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="/assets/logo-yapi.png" alt="YAPI" class="h-9 w-9 object-contain rounded-full ring-1 ring-white/40 bg-white/90">
        <img src="/assets/logo-alazhar13.png" alt="SDI Al Azhar 13" class="h-9 w-9 object-contain rounded-full ring-1 ring-white/40 bg-white/90">
        <span class="ml-1 text-white/95 dark:text-white font-semibold tracking-wide">LibMS</span>
      </div>
      <button type="button" onclick="toggleTheme()"
        class="rounded-xl px-3 py-2 text-sm font-medium bg-white/15 dark:bg-white/10 text-white
               hover:bg-white/25 dark:hover:bg-white/20 backdrop-blur ring-1 ring-white/25 transition"
        aria-label="Toggle tema">Toggle Tema</button>
    </div>
  </header>

  <main class="relative min-h-[calc(100%-64px)] flex items-center justify-center py-10 px-6">
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-5 gap-8 items-stretch">

      <section class="hidden lg:block lg:col-span-3 animate-fade-right" data-anim-delay=".04s">
        <div class="relative h-full rounded-3xl bg-white/5 dark:bg-white/5 backdrop-blur-2xl
                    ring-1 ring-white/15 shadow-glass overflow-hidden">

          <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-32 -left-10 h-80 w-80 rotate-12 rounded-3xl border border-white/15"></div>
            <div class="absolute top-20 -right-10 h-72 w-72 -rotate-6 rounded-3xl border border-white/10"></div>
          </div>

          <div class="relative p-10 text-white">
            <div class="flex items-center gap-3">
              <img src="/assets/logo-yapi.png" alt="YAPI" class="h-10 w-10 object-contain rounded-full ring-1 ring-white/40 bg-white/90">
              <img src="/assets/logo-alazhar13.png" alt="SDI Al Azhar 13" class="h-10 w-10 object-contain rounded-full ring-1 ring-white/40 bg-white/90">
              <span class="ml-1 text-white/90 font-semibold tracking-wide">LibMS</span>

            </div>
            <h1 class="mt-8 text-4xl font-bold tracking-tight leading-tight
                       bg-gradient-to-r from-white to-white/70 bg-clip-text text-transparent">
              Library Management System
            </h1>
            <p class="mt-3 max-w-2xl text-base leading-relaxed text-white/85">
              Sistem perpustakaan modern untuk sekolah—cepat, rapi, dan nyaman dipakai
              setiap hari. Fokus pada kecepatan scan, data akurat, dan tampilan yang menenangkan mata.
            </p>

            <ul class="mt-8 grid grid-cols-2 gap-3 max-w-2xl">
              @php
                $features = [
                  ['title'=>'Sirkulasi','desc'=>'Scan cepat pinjam/kembali','icon'=>'
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3zM3 11h18v2H3zM3 16h12v2H3z"/></svg>'],
                  ['title'=>'Kunjungan','desc'=>'Check-in/out & statistik','icon'=>'
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0v1H5v-1z"/></svg>'],
                  ['title'=>'Dashboard','desc'=>'KPI harian & bulanan','icon'=>'
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h4v18H3zM10 10h4v11h-4zM17 6h4v15h-4z"/></svg>'],
                  ['title'=>'Report','desc'=>'Report Sirkulasi & Visit','icon'=>'
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v4H4zM6 10h12v10H6z"/></svg>'],
                ];
              @endphp
              @foreach ($features as $i => $f)
                <li class="group rounded-2xl border border-white/15 bg-white/8 backdrop-blur-md
                           px-4 py-3 hover:bg-white/12 transition animate-fade-up"
                    data-anim-delay="{{ 0.06 + ($i*0.04) }}s">
                  <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-teal-200/90">{!! $f['icon'] !!}</span>
                    <div>
                      <div class="text-sm font-semibold text-white">{{ $f['title'] }}</div>
                      <div class="text-sm text-white/80">{{ $f['desc'] }}</div>
                    </div>
                  </div>
                </li>
              @endforeach
            </ul>

            <div class="mt-8">
              <button type="button" onclick="document.getElementById('email')?.focus()"
                class="inline-flex items-center gap-2 rounded-xl bg-white/15 hover:bg-white/25
                       text-white px-4 py-2 font-medium backdrop-blur ring-1 ring-white/25 transition animate-fade-up"
                data-anim-delay=".28s">
                Mulai Masuk
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 5l7 7-7 7v-4H4v-6h9V5z"/></svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="lg:col-span-2 animate-fade-up" data-anim-delay=".12s">
        <div class="rounded-3xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-2xl p-8 md:p-10
                    ring-1 ring-slate-200/70 dark:ring-white/10 shadow-glass">
          {{ $slot }}
          <p class="mt-8 text-center text-xs text-slate-600 dark:text-slate-400">
            © {{ date('Y') }} YAPI • SDI Al Azhar 13 — LibMS
          </p>
        </div>
      </section>
    </div>

    <div class="pointer-events-none absolute right-[10%] top-[30%] h-40 w-40 rounded-full bg-teal-400/25 blur-3xl animate-pulse-glow"></div>
  </main>
</body>
</html>
