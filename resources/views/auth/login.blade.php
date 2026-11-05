<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111c32">
    <title>Login | BuildSync ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { min-height:100vh; background:radial-gradient(circle at 15% 15%,#dbeafe 0,transparent 32%),radial-gradient(circle at 85% 85%,#dbeafe 0,transparent 30%),#f3f6fb; color:#172033; }
        .login-card { width:min(100% - 2rem, 510px); padding:clamp(2rem,5vw,3.25rem); border:1px solid #e5ebf3; border-radius:28px; background:rgba(255,255,255,.96); box-shadow:0 28px 80px rgba(15,23,42,.12); }
        .login-card input:not([type=checkbox]) { width:100%; min-height:54px; padding:.9rem 1rem; border:1px solid #d7e0ec; border-radius:13px; font-size:1rem; background:#fff; transition:.2s; }
        .login-card input:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,.12); }
        .login-card label { display:block; margin-bottom:.5rem; font-size:.9rem; font-weight:650; color:#344258; }
        .login-card button[type=submit] { min-height:54px; border-radius:13px; font-size:1rem; font-weight:700; box-shadow:0 9px 20px rgba(37,99,235,.2); transition:.2s; }
        .login-card button[type=submit]:hover { transform:translateY(-1px); box-shadow:0 13px 24px rgba(37,99,235,.25); }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center px-4 py-10">
    <main class="login-card">
        <div class="mb-9 text-center">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-600 text-2xl font-black text-white shadow-xl shadow-blue-200">B</div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-600">Construction Management</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">BuildSync ERP</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Masuk untuk mengelola proyek konstruksi dalam satu workspace.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email">Alamat Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan password">
            </div>
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="remember" class="ml-2 !mb-0 text-sm !font-medium text-slate-600">Ingat Saya</label>
            </div>
            <button type="submit" class="flex w-full items-center justify-center border border-transparent bg-blue-600 px-4 py-3 text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">Masuk ke Dashboard</button>
        </form>
        <p class="mt-8 text-center text-xs text-slate-400">BuildSync ERP · Manajemen Proyek Konstruksi</p>
    </main>
</body>
</html>