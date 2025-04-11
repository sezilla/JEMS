<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Landing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white dark:bg-zinc-900 text-black dark:text-white">

    <header class="py-6 px-8 flex justify-between items-center bg-[#FF1493]/10 border-b dark:border-zinc-700">
        <img src="storage/images/jemlogo.png" class="w-40 h-auto" />
        <!-- <h1 class="text-2xl font-bold tracking-tight text-[#FF1493]">AdminHQ</h1> -->
        <nav class="space-x-4">
            <a href="#" class="hover:underline">Home</a>
            <a href="#" class="hover:underline">Features</a>
            <a href="#" class="hover:underline">Login</a>
        </nav>
    </header>

    <main class="px-8 py-16">
        <section class="text-center max-w-3xl mx-auto">
            <h2 class="text-4xl font-extrabold mb-4">Welcome to <span class="text-[#FF1493]">JEMS</span>!</h2>
            <p class="text-lg text-zinc-600 dark:text-zinc-300 mb-8">
                A powerful admin management system to streamline operations, manage teams, and make data-driven decisions.
            </p>
            <a href="#features" class="inline-block bg-[#FF1493] hover:bg-pink-600 text-white font-medium px-6 py-3 rounded-2xl shadow-md transition">
                Explore Features
            </a>
        </section>

        <section id="features" class="mt-20 grid gap-8 lg:grid-cols-3 sm:grid-cols-2">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 transition hover:ring-2 hover:ring-[#FF1493]">
            <img src="storage/images/banner.jpg" />
                <div class="text-[#FF1493] mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="..."/></svg>
                    
                </div>
                <h3 class="text-xl font-semibold mb-2">User Role Management</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Assign, restrict, and manage user permissions effortlessly.</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 transition hover:ring-2 hover:ring-[#FF1493]">
                <div class="text-[#FF1493] mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="..."/></svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Analytics Dashboard</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Real-time insights and performance reports at your fingertips.</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-lg p-6 transition hover:ring-2 hover:ring-[#FF1493]">
                <div class="text-[#FF1493] mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="..."/></svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Task Automation</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Automate workflows and reduce manual tasks across departments.</p>
            </div>
        </section>
    </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    DDDM
                    @auth
                        <a href="{{ url('/app') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF1493] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Dashboard
                        </a>
                        <a href="{{ url('/admin/login') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF1493] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Admin Dashboard
                        </a>
                    @else
                        <a href="{{ url('/app/edit-profile') }}" {{-- href="{{ url('/app/edit-profile') }}"  if user's first log in --}}
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF1493] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Log in
                        </a>
                        <a href="{{ url('/admin/login') }}" {{-- href="{{ url('/app/edit-profile') }}"  if user's first log in --}}
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF1493] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Log in as admin
                        </a>
                    @endauth
                </footer>
</body>
</html>
