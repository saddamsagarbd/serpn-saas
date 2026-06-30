<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SERPN SaaS Admin' }}</title>
    <!-- Tailwind CSS এবং Alpine.js CDN (প্রয়োজন অনুযায়ী) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f4f7f6] font-sans antialiased">

    <div class="flex h-screen overflow-hidden" x-data="{ openModal: false }">
        
        <!-- ১. সাইডবার ইনক্লুড -->
        @include('partials.sidebar')

        <!-- ডানপাশের কন্টেন্ট এরিয়া wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- ২. হেডার ইনক্লুড -->
            @include('partials.header')

            <!-- ৩. মেইন ডাইনামিক কন্টেন্ট সেকশন -->
            <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">
                @yield('content')
            </main>
            
        </div>
    </div>

</body>
</html>