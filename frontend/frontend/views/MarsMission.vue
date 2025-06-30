<template>
  <main class="flex flex-col items-center p-4 space-y-6 min-h-screen">
    <h1 class="text-3xl font-bold">Mars Rover Mission 🚀</h1>
    <RoverForm @mission-executed="onMissionExecuted" />

    <div class="overflow-auto max-w-full border border-gray-700">
      <RoverMap
        v-if="result"
        :steps="result.steps"
        :obstacle="result.obstaclePosition"
        :obstacles="result.obstacles"
        @animation-finished="animationDone = true"
      />
    </div>

    <RoverResult v-if="animationDone" :result="result" />
  </main>
</template>

<script setup>
import { ref } from 'vue'
import RoverForm from '../src/components/RoverForm.vue'
import RoverResult from '../src/components/RoverResult.vue'
import RoverMap from '../src/components/RoverMap.vue'

const result = ref(null)
const animationDone = ref(false)

function onMissionExecuted(data) {
  result.value = data
  animationDone.value = false // Reiniciar per si mission reset
}
</script>
