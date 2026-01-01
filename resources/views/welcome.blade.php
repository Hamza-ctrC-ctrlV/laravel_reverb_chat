<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel Chat') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 500: '#0ea5e9', 600: '#0284c7' },
                        dark: { 800: '#1e293b', 900: '#0f172a' }
                    },
                    animation: { 'blob': 'blob 7s infinite' },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .cursor { display: inline-block; width: 3px; background-color: #0ea5e9; margin-left: 4px; animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    </style>
</head>
<body class="antialiased bg-dark-900 text-white">

    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-8 left-1/3 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen">
        <nav class="w-full px-6 py-4 glass sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-comments text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">Chat<span class="text-brand-500">App</span></span>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold text-gray-300">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-gray-300 hover:text-white transition">Log in</a>
                            <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold bg-brand-500 hover:bg-brand-600 rounded-full transition shadow-lg shadow-brand-500/30">Sign Up</a>
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <main class="flex-grow flex items-center justify-center px-6 py-20">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="space-y-8 text-center lg:text-left">
                    <h1 class="text-5xl md:text-7xl font-bold leading-tight">
                        Messaging for <br>
                        <span id="typewriter" class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-purple-500"></span><span class="cursor">&nbsp;</span>
                    </h1>
                    <p class="text-lg text-gray-400 max-w-xl mx-auto lg:mx-0">
                        Real-time conversation made simple. No clutter, no distractions. Just you and your messages.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-dark-900 font-bold rounded-xl hover:bg-gray-100 transition">Start Chatting Now</a>
                    </div>
                </div>

                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-brand-500 to-purple-600 rounded-2xl blur opacity-25"></div>
                    <div class="relative bg-dark-800 rounded-2xl p-6 border border-gray-700 shadow-2xl min-h-[400px] flex flex-col">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-500 to-blue-400 flex items-center justify-center font-bold">JD</div>
                                <div>
                                    <div class="font-semibold text-sm">John Doe</div>
                                    <div class="text-[10px] text-green-400 uppercase font-bold tracking-wider">Active Now</div>
                                </div>
                            </div>
                            <div class="text-gray-500 text-xs">Public Room</div>
                        </div>

                        <div class="flex-grow space-y-4 mb-6">
                            <div class="flex items-end gap-2">
                                <div class="bg-gray-700 p-3 rounded-2xl rounded-bl-none text-sm text-gray-200">
                                    Hey! How's the new project?
                                </div>
                            </div>
                            <div class="flex items-end gap-2 justify-end">
                                <div class="bg-brand-600 p-3 rounded-2xl rounded-br-none text-sm text-white">
                                    It's going great! The UI is so fast.
                                </div>
                            </div>
                        </div>

                        <div class="bg-dark-900 rounded-xl p-2 flex items-center gap-2 border border-gray-700">
                            <div id="demo-typing" class="bg-transparent flex-grow text-sm text-white/50 px-2"></div>
                            <button class="w-8 h-8 bg-brand-500 rounded-lg text-white flex items-center justify-center hover:bg-brand-600 transition">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-8 text-center text-gray-600 text-xs border-t border-gray-800/50">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel.
        </footer>
    </div>

    <script>
        const words = ["Communities.", "Developers.", "Friends.", "Teams."];
        let wordIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typewriterElement = document.getElementById("typewriter");
        const demoInput = document.getElementById("demo-typing");

        function type() {
            const currentWord = words[wordIndex];
            
            if (isDeleting) {
                typewriterElement.textContent = currentWord.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typewriterElement.textContent = currentWord.substring(0, charIndex + 1);
                charIndex++;
            }

            // Sync the demo input field with the main typing
            demoInput.textContent = `Typing something for ${currentWord.substring(0, charIndex)}...`;

            let typeSpeed = isDeleting ? 50 : 150;

            if (!isDeleting && charIndex === currentWord.length) {
                typeSpeed = 2000; // Pause at end
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                wordIndex = (wordIndex + 1) % words.length;
                typeSpeed = 500;
            }

            setTimeout(type, typeSpeed);
        }

        document.addEventListener("DOMContentLoaded", type);
    </script>
</body>
</html>