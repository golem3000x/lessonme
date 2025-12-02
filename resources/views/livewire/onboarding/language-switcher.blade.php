<div class="flex items-center">
    <a href="{{ route('locale.switch', $target_locale) }}"
       class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
       title="{{ $current_locale === 'en' ? 'Switch to Vietnamese' : 'Chuyển sang Tiếng Anh' }}">
        <span class="font-semibold">
            {{ $current_locale === 'en' ? 'VI' : 'EN' }}
        </span>
    </a>
</div>
