@extends('dash.main')

@section('content')
    {{-- The login view is replaced by real Laravel auth at /login --}}
    {{-- The user is already authenticated + verified to reach here --}}

    <div id="main-view" class="flex h-screen w-full relative overflow-hidden">
        <!-- Sidebar Mobile Overlay -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-30 transition-opacity"></div>

        @include('dash.sidebar')

        <div class="flex-1 flex flex-col relative overflow-hidden bg-gray-50 transition-all duration-300">
            <!-- HEADER -->
            <header class="bg-white px-4 md:px-8 py-5 shadow-sm flex justify-between items-center z-10">
                <div class="flex items-center">
                    <button id="sidebar-toggle"
                        class="p-2 mr-4 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h2 id="header-title" class="text-xl md:text-2xl font-semibold text-gray-800 capitalize">Dashboard</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div
                        class="flex items-center text-sm font-medium text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                        <i data-lucide="calendar" class="w-[14px] h-[14px] mr-2"></i>
                        <select id="event-selector"
                            class="bg-transparent outline-none font-bold text-blue-800 cursor-pointer">
                            <!-- Populated via JS -->
                        </select>
                    </div>
                    <!-- User info + Logout -->
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-8 h-8 rounded-full bg-green-700 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors flex items-center space-x-1">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-8 relative">
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

        <!-- FLOATING AI CHATBOT WIDGET -->
        <div class="fixed bottom-6 right-6 z-[60] flex flex-col items-end">
            <!-- Chat Window -->
            <div id="chatbot-window"
                class="hidden bg-white rounded-2xl shadow-2xl border border-gray-100 w-80 mb-4 overflow-hidden flex flex-col transition-all duration-300 transform origin-bottom-right"
                style="height: 400px;">
                <!-- Header -->
                <div class="bg-indigo-600 text-white p-4 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="brain" class="w-5 h-5"></i>
                        <span class="font-bold">CCS AI Assistant</span>
                    </div>
                    <button id="close-chat" class="text-indigo-200 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Messages Area -->
                <div id="chat-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col space-y-3">
                </div>

                <!-- Input Area -->
                <form id="chat-form" class="p-3 bg-white border-t border-gray-100 flex items-center space-x-2">
                    <input type="text" id="chat-input" placeholder="Ask me anything..."
                        class="flex-1 bg-gray-100 text-sm px-4 py-2 rounded-full outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow"
                        autocomplete="off" />
                    <button type="submit" id="chat-send"
                        class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition-colors disabled:opacity-50">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Floating Action Button -->
            <button id="chatbot-fab"
                class="h-14 w-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-105 bg-indigo-600 text-white">
                <i data-lucide="message-square" class="w-6 h-6 fab-icon-open"></i>
                <i data-lucide="x" class="w-6 h-6 hidden fab-icon-close"></i>
            </button>
        </div>
    </div>
@endsection