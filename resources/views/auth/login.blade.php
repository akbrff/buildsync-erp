<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | BuildSync ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm rounded-md border border-slate-200 bg-white p-6 shadow-xs">
        {{-- Brand / Header --}}
        <div class="mb-5 flex flex-col items-center text-center">
            <div class="flex h-10 w-10 items-center justify-center rounded"
                 style="background: #2563eb;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="mt-3 text-base font-bold text-slate-900 tracking-tight">BuildSync ERP</h1>
            <p class="mt-0.5 text-xs text-slate-500">Sistem Manajemen Konstruksi &amp; Finansial</p>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                <div class="flex items-center gap-1.5 font-semibold text-red-800 mb-0.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Autentikasi Gagal
                </div>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="erp-label mb-1 block">Alamat Email Kerja</label>
                <input id="email" name="email" type="email" value="{{ old('email', 'admin@buildsync.com') }}" required autofocus
                       class="erp-input font-medium" placeholder="admin@buildsync.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="erp-label">Kata Sandi</label>
                </div>
                <input id="password" name="password" type="password" required
                       class="erp-input font-medium" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                    <input id="remember" name="remember" type="checkbox"
                           class="h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Ingat saya</span>
                </label>
                <span class="text-slate-400 text-[11px]">Default: password</span>
            </div>

            <button type="submit"
                    class="w-full rounded bg-blue-600 py-2 px-4 text-xs font-semibold text-white hover:bg-blue-700 transition-colors shadow-xs">
                Masuk ke Workspace
            </button>
        </form>

        {{-- Footer System Info --}}
        <div class="mt-6 border-t border-slate-100 pt-3 text-center">
            <p class="text-[10px] text-slate-400">
                BuildSync ERP Enterprise &bull; v2.4.0
            </p>
        </div>
    </div>

</body>
</html>
