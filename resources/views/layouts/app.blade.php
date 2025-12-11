<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>{{ $title ?? 'LibMS — Dashboard' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Dark mode sync --}}
  <script>
    (function(){try{
      const pref=localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (pref==='dark' || (!pref && prefersDark)) document.documentElement.classList.add('dark');
    }catch(e){}})();

    document.addEventListener('DOMContentLoaded', () => {
      document.getElementById('btnTheme')?.addEventListener('click', ()=>{
        const isDark = document.documentElement.classList.toggle('dark');
        try{localStorage.setItem('theme', isDark ? 'dark' : 'light');}catch(e){}
      });
      const sb = document.getElementById('sidebar');
      document.getElementById('btnSidebar')?.addEventListener('click', ()=> sb?.classList.toggle('-translate-x-full'));
    });
  </script>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100">

  @include('partials.topbar')

  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 grid grid-cols-12 gap-6">
    @include('partials.sidebar')

    <main class="col-span-12 lg:col-span-9">
      <div class="rounded-3xl bg-white/85 dark:bg-slate-900/80 backdrop-blur-2xl p-6
                  ring-1 ring-slate-200/70 dark:ring-white/10 shadow-glass animate-fade-up">
        @yield('content')
      </div>
    </main>
  </div>
</body>
</html>
