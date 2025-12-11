<x-guest-layout :title="'Masuk — '.config('app.name')">

  <div class="mx-auto w-full max-w-md animate-fade-up" style="animation-duration: 0.5s;">
    <!-- Header / Logo Section -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <img src="/assets/logo-yapi.png" alt="YAPI" width="40" height="40" loading="eager"
             class="h-10 w-10 object-contain rounded-full ring-1 ring-slate-200 dark:ring-white/15 bg-white">
        <img src="/assets/logo-alazhar13.png" alt="SDI Al Azhar 13" width="40" height="40" loading="eager"
             class="h-10 w-10 object-contain rounded-full ring-1 ring-slate-200 dark:ring-white/15 bg-white">
      </div>
      <div class="text-right">
        <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Masuk</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Akses petugas / admin LibMS</p>
      </div>
    </div>

    <!-- Session Status -->
    @if (session('status'))
      <div class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-400/10 text-emerald-700 dark:text-emerald-300 px-4 py-3">
        {{ session('status') }}
      </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
      @csrf

      <!-- Email Address -->
      <div class="animate-fade-up" style="animation-delay: 0.1s;">
        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
               class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700
                      bg-white dark:bg-slate-900/60 text-slate-900 dark:text-slate-100
                      shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors duration-200">
        @error('email')
          <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div class="animate-fade-up" style="animation-delay: 0.2s;">
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kata Sandi</label>
          
        </div>
        <input id="password" name="password" type="password" required autocomplete="current-password"
               class="mt-1 block w-full rounded-xl border-slate-300 dark:border-slate-700
                      bg-white dark:bg-slate-900/60 text-slate-900 dark:text-slate-100
                      shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors duration-200">
        @error('password')
          <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
      </div>

      <!-- Remember Me -->
      <div class="animate-fade-up" style="animation-delay: 0.3s;">
        <label class="flex items-center gap-2 select-none cursor-pointer">
          <input type="checkbox"
                 name="remember"
                 class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500">
          <span class="text-sm text-slate-600 dark:text-slate-300">
            Ingat saya di perangkat ini
          </span>
        </label>
      </div>

      <!-- Submit Button -->
      <div class="animate-fade-up" style="animation-delay: 0.4s;">
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl
                       bg-indigo-700 hover:bg-indigo-600 text-white px-4 py-3 font-semibold
                       shadow-md hover:shadow-lg transition-all duration-200
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 dark:focus:ring-offset-slate-900">
          Masuk ke LibMS
        </button>
      </div>
    </form>
  </div>
</x-guest-layout>
