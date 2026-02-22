<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ESSU CCS AI Powered Attendance System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-50 font-sans text-gray-800 overflow-hidden h-screen flex flex-col">

    <!-- TOAST CONTAINER -->
    <div id="toast-container" class="fixed top-6 right-6 z-[100] flex flex-col gap-3"></div>

    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('js/admin/dashboard.js') }}"></script>
    <script src="{{ asset('js/admin/eventManagement.js') }}"></script>
    <script src="{{ asset('js/admin/scanQrCode.js') }}"></script>
    <script src="{{ asset('js/admin/attendanceRecords.js') }}"></script>
    <script src="{{ asset('js/admin/studentMasterlist.js') }}"></script>
    <script src="{{ asset('js/admin/aiAnalytics.js') }}"></script>
    <script src="{{ asset('js/admin/activityLogs.js') }}"></script>
    <script src="{{ asset('js/admin/settings.js') }}"></script>
</body>
</html>
