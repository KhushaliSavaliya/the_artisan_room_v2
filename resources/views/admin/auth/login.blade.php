<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login — KD Artisan Room</title>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-[#0d1410] text-[#f0ede4] font-sans flex items-center justify-center p-6">

    <div class="max-w-md w-full border border-gold-accent/15 p-8 md:p-10 rounded-sm bg-[#070b09] shadow-lg">
        
        <!-- Header -->
        <div class="text-center mb-8 pb-6 border-b border-gold-accent/10">
            <span class="font-serif italic text-gold-accent text-2xl font-bold tracking-wider mb-2 block">
                KD Artisan Room
            </span>
            <span class="text-[9px] uppercase tracking-[0.2em] text-muted-content font-sans">
                Admin Panel Access
            </span>
        </div>

        <!-- Validation Alerts -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-sans rounded-sm">
                <ul class="list-disc list-inside m-0 flex flex-col gap-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 bg-warm-amber/10 border border-warm-amber/20 text-warm-amber text-xs font-sans rounded-sm">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="flex flex-col gap-5">
            @csrf

            <!-- Email -->
            <div class="flex flex-col gap-2">
                <label for="email" class="text-[10px] uppercase tracking-wider font-sans text-muted-content">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="bg-bg-content border border-gold-accent/20 rounded-sm px-4 py-3 text-xs font-sans text-[#f0ede4] focus:outline-none focus:border-gold-accent" placeholder="admin@kdartisanroom.com" required autofocus />
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <label for="password" class="text-[10px] uppercase tracking-wider font-sans text-muted-content">Password</label>
                <input type="password" id="password" name="password" class="bg-bg-content border border-gold-accent/20 rounded-sm px-4 py-3 text-xs font-sans text-[#f0ede4] focus:outline-none focus:border-gold-accent" placeholder="Enter password" required />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center gap-2.5">
                <input type="checkbox" id="remember" name="remember" class="accent-gold-accent w-4 h-4 cursor-pointer" />
                <label for="remember" class="text-[10px] uppercase tracking-wider font-sans text-muted-content hover:text-gold-accent cursor-pointer select-none">
                    Remember Me
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3.5 bg-gold-accent hover:bg-gold-accent/90 text-[#0d1410] font-sans font-semibold text-xs uppercase tracking-wider rounded-sm transition-colors duration-300 shadow-md cursor-pointer mt-2">
                Log In
            </button>
        </form>

    </div>

</body>
</html>
