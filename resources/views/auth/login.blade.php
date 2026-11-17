<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.sign_in_title') }} | {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="antialiased min-h-screen w-full flex bg-white dark:bg-[#09090B]">

    {{-- SISI KIRI (Area Form Login) --}}
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 lg:px-20 xl:px-28 bg-white dark:bg-[#09090B] relative z-10 min-h-screen py-12">

        {{-- Top Right Controls (Theme & Language) --}}
        <div class="absolute top-5 right-5 sm:top-8 sm:right-8 flex items-center gap-2">
            {{-- Guest Language Switcher --}}
            <div class="flex items-center rounded-md bg-white dark:bg-[#18181B] border border-slate-200 dark:border-[#27272A] p-0.5 text-xs font-semibold" role="group" aria-label="{{ __('nav.language') }}">
                <a href="{{ route('locale.switch', 'en') }}"
                   class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#1D4E89] text-white shadow-xs' : 'text-[#64748B] dark:text-[#A1A1AA] hover:text-[#0B2545] dark:hover:text-white' }}"
                   title="{{ __('nav.english') }}">EN</a>
                <a href="{{ route('locale.switch', 'id') }}"
                   class="px-2 py-1 rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-[#1D4E89] text-white shadow-xs' : 'text-[#64748B] dark:text-[#A1A1AA] hover:text-[#0B2545] dark:hover:text-white' }}"
                   title="{{ __('nav.indonesian') }}">ID</a>
            </div>

            {{-- Dark / Light Mode Toggle Button --}}
            <button type="button"
                    id="theme-toggle"
                    aria-label="{{ __('nav.toggle_dark_mode') }}"
                    class="rounded-md bg-white dark:bg-[#18181B] border border-slate-200 dark:border-[#27272A] p-2 text-[#0B2545] dark:text-[#F4F4F5] hover:bg-slate-50 dark:hover:bg-white/5 transition-all cursor-pointer flex items-center justify-center shadow-none">
                {{-- Moon SVG (Light Mode) --}}
                <svg id="theme-toggle-dark-icon" class="w-4 h-4 text-[#0B2545] block dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                {{-- Sun SVG (Dark Mode) --}}
                <svg id="theme-toggle-light-icon" class="w-4 h-4 text-[#F4F4F5] hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>
        </div>

        {{-- Form Container --}}
        <div class="w-full max-w-sm">

            {{-- Logo & Tipografi Header --}}
            <div class="flex flex-col items-start text-left w-full">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-[#1D4E89] bg-[#136F9A] text-white shadow-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-[#0B2545] dark:text-[#F4F4F5] tracking-tight mt-4 text-left">BuildSync ERP</h1>
                <p class="font-normal text-[13px] text-[#64748B] dark:text-[#A1A1AA] text-left mt-1.5 mb-8 leading-relaxed">{{ __('auth.sign_in_subtitle') }}</p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-rose-200 dark:border-rose-900/50 bg-rose-50/70 dark:bg-rose-950/30 p-3.5 text-xs text-rose-700 dark:text-rose-400">
                    <div class="flex items-center gap-1.5 font-medium text-rose-800 dark:text-rose-300 mb-1">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('auth.auth_failed') }}
                    </div>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Input Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-[#0B2545] dark:text-[#E2E8F0] mb-2">{{ __('auth.email_label') }}</label>
                    <input id="email"
                           name="email"
                           type="email"
                           required
                           autofocus
                           class="w-full rounded-md border border-[#D1D5DB] dark:border-[#27272A] bg-white dark:bg-[#18181B] px-3.5 py-2.5 text-sm text-[#0B2545] dark:text-[#F4F4F5] placeholder:text-slate-400 focus:border-[#1D4E89] focus:ring-1 focus:ring-[#1D4E89] focus:outline-none transition-colors shadow-none"
                           placeholder="{{ __('auth.enter_email') }}">
                </div>

                {{-- Input Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-[#0B2545] dark:text-[#E2E8F0] mb-2">{{ __('auth.password_label') }}</label>
                    <input id="password"
                           name="password"
                           type="password"
                           required
                           class="w-full rounded-md border border-[#D1D5DB] dark:border-[#27272A] bg-white dark:bg-[#18181B] px-3.5 py-2.5 text-sm text-[#0B2545] dark:text-[#F4F4F5] placeholder:text-slate-400 focus:border-[#1D4E89] focus:ring-1 focus:ring-[#1D4E89] focus:outline-none transition-colors shadow-none"
                           placeholder="{{ __('auth.enter_password') }}">
                </div>

                {{-- Checkbox "Ingat saya" --}}
                <div class="flex items-center text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember"
                               name="remember"
                               type="checkbox"
                               class="rounded border-slate-300 text-[#1D4E89] focus:ring-0 focus:outline-none dark:border-[#27272A] dark:bg-[#18181B] h-4 w-4 shadow-none">
                        <span class="text-sm font-normal text-[#64748B] dark:text-[#A1A1AA]">{{ __('auth.remember_me') }}</span>
                        <span class="hidden" aria-hidden="true">{{ __('auth.default_hint') }}</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full bg-[#1D4E89] bg-[#136F9A] text-white rounded-md px-4 py-2.5 text-sm font-medium hover:bg-[#153A66] transition-colors mt-6 cursor-pointer shadow-none">
                    {{ __('auth.sign_in_btn') }}
                </button>
            </form>

            {{-- Footer (Versi & Copyright Tim Pengembang) --}}
            <div class="mt-8 pt-5 border-t border-[#E5E7EB] dark:border-[#27272A] text-left w-full">
                <p class="text-xs font-medium text-[#0B2545] dark:text-[#F4F4F5] mb-1.5">{{ __('auth.brand_badge') }} &bull; v2.4.0</p>
                <p class="font-normal text-xs text-[#64748B] dark:text-[#A1A1AA] leading-relaxed mt-2">
                    {{ __('auth.developed_by') }}<br>
                    <span class="text-[#0B2545] dark:text-[#F4F4F5] font-medium">AHMAD SAUQI ZAMZAMI</span> &bull; 
                    <span class="text-[#0B2545] dark:text-[#F4F4F5] font-medium">MUHAMAD AKBAR FEBIANSYAH</span> &bull; 
                    <span class="text-[#0B2545] dark:text-[#F4F4F5] font-medium">MUHAMMAD FADHILLAH AKBAR</span>
                </p>
            </div>
        </div>
    </div>

    {{-- SISI KANAN (Area Gambar) --}}
    <div class="hidden lg:block lg:w-1/2 relative bg-slate-100 dark:bg-[#09090B]">
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?q=80&w=2000&auto=format&fit=crop"
             alt="BuildSync Cover"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-[#0B2545]/20 mix-blend-multiply"></div>
    </div>

    <script>
        // Toggle Dark / Light Mode
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function () {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                window.dispatchEvent(new CustomEvent('theme-changed', {
                    detail: { theme: isDark ? 'dark' : 'light', isDark: isDark }
                }));
            });
        }
    </script>
</body>
</html>
