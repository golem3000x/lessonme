<div class="flex items-center">
    <a href="{{ route('locale.switch', $target_locale) }}"
       class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 group"
       title="{{ $current_locale === 'en' ? 'Switch to Vietnamese' : 'Chuyển sang Tiếng Anh' }}">
        <!-- Globe Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5 transition-transform duration-300 group-hover:rotate-180" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        
        <!-- Language Code -->
        <span class="font-semibold">
            {{ $current_locale === 'en' ? 'VI' : 'EN' }}
        </span>
    </a>
</div>
