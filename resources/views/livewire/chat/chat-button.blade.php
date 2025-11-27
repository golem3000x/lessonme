<!-- Chat button in bottom right corner -->
<div 
    class="fixed bottom-6 right-6 z-50"
>
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        
        /* Slow pulse */
        @keyframes slow-pulse {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.15);
            }
        }
        
        .animate-slow-pulse {
            animation: slow-pulse 3s ease-in-out infinite;
        }
    </style>

    <button 
        wire:click="toggleChat"
        class="group relative flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-full shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-blue-300"
        aria-label="Open chat"
    >
        <!-- Slow pulse -->
        <span class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 opacity-40 animate-slow-pulse"></span>
        
        <!-- Icon without rotation -->
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="relative h-8 w-8 z-10 transition-transform duration-300" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            />
        </svg>

        <!-- Badge with unread message count -->
        @if($unread_count > 0)
            <span class="absolute -top-2 -right-2 flex items-center justify-center w-7 h-7 bg-red-500 text-white text-xs font-bold rounded-full border-2 border-white shadow-lg animate-bounce">
                {{ $unread_count > 9 ? '9+' : $unread_count }}
            </span>
        @endif
    </button>

    <!-- Tooltip on hover -->
    <div class="absolute bottom-full right-0 mb-3 hidden group-hover:block animate-fade-in">
        <div class="bg-gray-900 text-white text-sm py-2 px-4 rounded-lg shadow-xl whitespace-nowrap">
            {{ __('nav.chat') }}
            <div class="absolute top-full right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-gray-900"></div>
        </div>
    </div>
</div>
