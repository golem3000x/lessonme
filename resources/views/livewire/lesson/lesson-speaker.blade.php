<div>
    @if($audio_url)
        <button 
            type="button"
            @click="$refs.audio.play()"
            class="text-gray-500 hover:text-green-700 transition"
            title="{{ __('lessons.speak_word') }}"
        >
            🔊
        </button>

        <audio x-ref="audio" src="{{ $audio_url }}" preload="auto" class="hidden"></audio>
    @else
        <span 
            class="text-gray-300 cursor-not-allowed" 
            title="{{ __('lessons.audio_not_available') }}"
        >
            🔇
        </span>
    @endif
</div>
