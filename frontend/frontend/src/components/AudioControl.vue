<template>
  <div
    class="audio-control flex flex-row items-center"
    @mouseenter="showSlider = true"
    @mouseleave="showSlider = false"
  >
    <!-- Speaker Icon -->
    <button
      @click="toggleMute"
      class="btn speaker-btn"
      :class="{ muted: isMuted }"
    >
      <svg v-if="!isMuted" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
      </svg>
      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
        <path d="M3.63 3.63a.996.996 0 000 1.41L7.29 8.7 7 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81l2.04 2.04a.996.996 0 101.41-1.41L5.05 3.63c-.39-.39-1.02-.39-1.42 0zM19 12c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zm-7-8L9.91 6.09 12 8.18V4z"/>
      </svg>
    </button>

    <!-- Volume Slider - appears RIGHT next to button -->
    <div v-if="showSlider" class="volume-slider">
      <input
        type="range"
        min="0"
        max="100"
        v-model="volume"
        @input="updateVolume"
        class="slider"
      />
      <span class="volume-text">{{ volume }}%</span>
    </div>

    <!-- Audio Element -->
    <audio
      ref="audioElement"
      loop
      preload="auto"
      @canplaythrough="startAudio"
    >
      <source src="/space-groove.mp3" type="audio/mpeg">
    </audio>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const audioElement = ref(null)
const isMuted = ref(false)
const volume = ref(25)
const showSlider = ref(false)

onMounted(() => {
  // Load saved audio preferences
  const savedVolume = localStorage.getItem('audioVolume')
  const savedMuted = localStorage.getItem('audioMuted')

  if (savedVolume) {
    volume.value = parseInt(savedVolume)
  }

  if (savedMuted) {
    isMuted.value = savedMuted === 'true'
  }

  // Apply loaded settings
  if (audioElement.value) {
    audioElement.value.volume = volume.value / 100
  }
})

// Watch for volume changes and save them
watch(volume, (newVolume) => {
  localStorage.setItem('audioVolume', newVolume.toString())
})

// Watch for mute changes and save them
watch(isMuted, (newMuted) => {
  localStorage.setItem('audioMuted', newMuted.toString())
})

const startAudio = () => {
  document.addEventListener('click', playAudio, { once: true })
}

const playAudio = () => {
  if (audioElement.value && !isMuted.value) {
    audioElement.value.play().catch(e => {
      console.log('Audio play failed:', e)
    })
  }
}

const toggleMute = () => {
  if (!audioElement.value) return

  isMuted.value = !isMuted.value

  if (isMuted.value) {
    audioElement.value.pause()
  } else {
    audioElement.value.play().catch(e => {
      console.log('Play failed:', e)
    })
  }
}

const updateVolume = () => {
  if (audioElement.value) {
    audioElement.value.volume = volume.value / 100
  }
}
</script>

<style scoped>
.audio-control {
  display: flex;
  align-items: center;
  gap: 6px;
  margin: 0 !important;
  padding: 0 !important;
}
.btn {
  padding: 0.5rem 1.25rem;
  background-color: #374151; /* Gray-700 */
  color: white;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn:hover {
  background-color: #1f2937; /* Gray-800 */
  box-shadow: 0 0 0 2px #6ee7b7; /* Emerald ring */
}
.speaker-btn {
  padding: 6px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
  border: 1px solid;
}
@media (prefers-color-scheme: dark) {
  .speaker-btn {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
  }

  .speaker-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }
}
@media (prefers-color-scheme: light) {
  .speaker-btn {
    background-color: #f3f4f6; /* Gray-100 */
    border-color: #10b981; /* Emerald-500 */
    color: #111827; /* Gray-900 */
  }

  .speaker-btn:hover {
    background-color: #d1fae5; /* Emerald-100 */
  }
}
.speaker-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.speaker-btn.muted {
  background-color: #dc2626; /* Red-600 */
  color: white;
  box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.5);
}

.volume-slider {
  background: rgba(0, 0, 0, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 4px;
  padding: 6px 8px;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.slider {
  width: 60px;
  height: 2px;
  border-radius: 1px;
  background: rgba(255, 255, 255, 0.3);
  outline: none;
  cursor: pointer;
}

.slider::-webkit-slider-thumb {
  appearance: none;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  border: none;
}

.volume-text {
  font-size: 9px;
  color: rgba(255, 255, 255, 0.8);
  white-space: nowrap;
  min-width: 25px;
}
</style>
