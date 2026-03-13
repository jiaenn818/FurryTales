<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Rider Panel')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>

<body>

    <div class="admin-layout">
        {{-- Include Sidebar --}}
        @include('layouts.rider-sidebar', ['currentPage' => $currentPage ?? ''])

        {{-- Main content --}}
        <main class="admin-main-layout">
            @yield('content')
        </main>
    </div>
    @stack('scripts')

</body>

</html>