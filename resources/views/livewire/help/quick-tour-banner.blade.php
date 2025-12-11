<div>
    @if($show_banner)
        <div class="help-banner-enter mb-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-xl shadow-2xl border-2 border-blue-400 p-6 text-white relative overflow-hidden">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <circle cx="20" cy="20" r="1" fill="white"/>
                    </pattern>
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                </svg>
            </div>

            {{-- Close Icon (X) - absolute positioned like greeting banner --}}
            <button 
                wire:click="dismissPermanently"
                class="absolute top-3 right-3 p-1 text-white/70 hover:text-white transition-all duration-300 hover:rotate-90 hover:scale-110 z-20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="relative z-10 pr-8">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-5xl help-icon-pulse">🎓</span>
                    <h3 class="text-2xl font-bold drop-shadow-lg">{{ __('help.banner_title') }}</h3>
                </div>
                <p class="text-blue-50 mb-5 text-base leading-relaxed max-w-2xl">
                    {{ __('help.banner_message') }}
                </p>
                <div class="flex flex-wrap gap-3">
                    {{-- Start Tutorial Button --}}
                    <button 
                        wire:click="startTour"
                        class="btn-shine inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 transform">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('help.start_tour') }}
                    </button>

                    {{-- Maybe Later Button --}}
                    <button 
                        wire:click="dismissTemporarily"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm text-white font-semibold rounded-lg hover:bg-white/30 transition-all duration-300 border-2 border-white/40 hover:border-white/60 hover:scale-105 transform shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('help.maybe_later') }}
                    </button>

                    {{-- Don't Show Again Button --}}
                    <button 
                        wire:click="dismissPermanently"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-transparent text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 border-2 border-white/40 hover:border-white/60 hover:scale-105 transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        {{ __('help.dont_show_again') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Script to handle tour start event --}}
    @script
    <script>
        // Listen for start-shepherd-tour event from Livewire
        $wire.on('start-shepherd-tour', () => {
            if (typeof window.startTour === 'function') {
                window.startTour();
            } else {
                console.error('Tour function not available');
            }
        });
    </script>
    @endscript
</div>
