<template>
  <div class="min-h-screen bg-black text-white font-sans">
    <!-- Audio Control in top LEFT corner -->
    <div class="fixed top-4 left-4 z-50">
      <AudioControl />
    </div>

    <MarsMission />
  </div>
</template>

<script setup>
import MarsMission from '../views/MarsMission.vue'
import AudioControl from '../src/components/AudioControl.vue'
</script>

<style>
body {
  margin: 0;
}
</style>
