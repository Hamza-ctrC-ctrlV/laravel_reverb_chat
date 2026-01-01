<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); }
        body { background-color: #0f172a; }
    </style>
</head>
<body class="antialiased text-white font-sans">
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-sky-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-tr from-sky-500 to-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-comments text-white"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">Chat<span class="text-sky-500">App</span></span>
                </a>
            </div>

            <div class="glass p-8 rounded-3xl shadow-2xl border-t border-white/10">
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-white">Create Account</h1>
                    <p class="text-gray-400 text-sm mt-1">Get started with your free account today.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Full Name</label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                class="w-full bg-slate-900/50 border border-gray-700 rounded-xl py-3 pl-11 pr-4 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition placeholder-gray-600"
                                placeholder="Enter your name">
                        </div>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email Address</label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-slate-900/50 border border-gray-700 rounded-xl py-3 pl-11 pr-4 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition placeholder-gray-600"
                                placeholder="name@example.com">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                <input type="password" name="password" required
                                    class="w-full bg-slate-900/50 border border-gray-700 rounded-xl py-3 pl-11 pr-4 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition placeholder-gray-600"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Confirm Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-circle-check absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                                <input type="password" name="password_confirmation" required
                                    class="w-full bg-slate-900/50 border border-gray-700 rounded-xl py-3 pl-11 pr-4 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition placeholder-gray-600"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-400 hover:to-sky-500 text-white font-bold py-3.5 rounded-xl transition duration-300 shadow-lg shadow-sky-500/20 mt-4">
                        Create Account
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-800 text-center">
                    <p class="text-sm text-gray-400">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-sky-500 font-bold hover:text-sky-400 transition">Sign In here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>