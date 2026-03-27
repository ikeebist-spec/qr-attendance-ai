@extends('dash.main')

@section('content')
    {{-- The login view is replaced by real Laravel auth at /login --}}
    {{-- The user is already authenticated + verified to reach here --}}

    <script>
        window.USER_ROLE = '{{ auth()->user()->role ?? "admin" }}';
    </script>

    <div id="main-view" class="flex h-[100dvh] w-full relative overflow-hidden">
        <!-- Sidebar Mobile Overlay -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-30 transition-opacity"></div>

        @include('dash.sidebar')

        <div class="flex-1 flex flex-col relative overflow-hidden bg-purple-100/60 transition-all duration-300">
            <!-- HEADER -->
            <header class="bg-white px-4 md:px-8 py-5 shadow-sm flex justify-between items-center z-10">
                <div class="flex items-center">
                    <button id="sidebar-toggle"
                        class="p-2 mr-4 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <button onclick="window.history.back()"
                        class="p-2 mr-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors flex items-center justify-center"
                        title="Go Back">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <h2 id="header-title" class="text-xl md:text-2xl font-semibold text-gray-800 capitalize">Dashboard</h2>
                </div>

            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8 relative">
                @include('admin.dashboard')
                @include('admin.eventManagement')
                @include('admin.scanQrCode')
                @include('admin.attendanceRecords')
                @include('admin.studentMasterlist')
                @include('admin.aiAnalytics')
                @include('admin.fineComputation')
                @include('admin.activityLogs')
                @include('admin.settings')
            </main>
        </div>

    </div>
@endsection