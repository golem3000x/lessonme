<div class="inline-block" 
    x-data="{ 
        audioUrl: @entangle('audio_url'),
        isPlaying: false
    }"
    x-init="
        $watch('audioUrl', async (url) => {
            if (!url) return;
            
            isPlaying = true; // Show loading during file generation
            
            const audio = $refs.audio;
            if (!audio) {
                isPlaying = false;
                return;
            }
            
            const maxRetries = 10;
            const delay = 2000; // 2 seconds between retries
            
            console.log('🎵 Starting audio playback:', url);
            
            for (let i = 0; i < maxRetries; i++) {
                try {
                    audio.src = url;
                    audio.load();
                    
                    console.log(`🔄 Attempt ${i + 1}/${maxRetries} - waiting for file...`);
                    
                    // Wait for audio to be ready (10 seconds timeout per attempt)
                    await new Promise((resolve, reject) => {
                        audio.addEventListener('canplaythrough', () => {
                            console.log('✅ Audio file is ready!');
                            resolve();
                        }, { once: true });
                        audio.addEventListener('error', (e) => {
                            console.log('⚠️ Audio error event:', e);
                            reject(new Error('Audio error'));
                        }, { once: true });
                        setTimeout(() => reject(new Error('Timeout waiting for file')), 10000);
                    });
                    
                    // Try to play
                    console.log('▶️ Playing audio...');
                    await audio.play();
                    
                    // Wait for audio to finish playing
                    await new Promise(resolve => {
                        audio.addEventListener('ended', () => {
                            console.log('✅ Audio finished playing');
                            resolve();
                        }, { once: true });
                    });
                    
                    // Reset for next click
                    isPlaying = false;
                    audioUrl = null;
                    return; // Success!
                } catch (err) {
                    console.log(`❌ Attempt ${i + 1}/${maxRetries} failed:`, err.message);
                    if (i < maxRetries - 1) {
                        console.log(`⏳ Waiting ${delay/1000}s before retry...`);
                        await new Promise(resolve => setTimeout(resolve, delay));
                    } else {
                        console.error('❌ Audio playback failed after all retries. URL:', url);
                        isPlaying = false;
                    }
                }
            }
        })
    ">
    <button 
        wire:click="speak" 
        :disabled="isPlaying"
        class="text-gray-500 hover:text-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
        title="{{ __('lessons.speak_word') }}"
    >
        <span x-show="!isPlaying">🔊</span>
        <span x-show="isPlaying" class="inline-block animate-pulse">⏳</span>
    </button>

    <audio 
        x-ref="audio"
        x-show="false"
        preload="none"
    ></audio>
</div>
