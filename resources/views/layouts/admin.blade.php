<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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