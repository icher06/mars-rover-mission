<template>
  <div class="mt-6">
    <h2 class="text-xl font-semibold mb-2 text-center">Mission Outcome</h2>

    <div class="text-center">
      <p class="mb-2">
        <span>Final Position: </span>
        <strong>({{ finalPosition.x }}, {{ finalPosition.y }})</strong>
      </p>

      <p class="mb-2">
        <span>Direction: </span>
        <strong>{{ finalPosition.direction }}</strong>
      </p>

      <p class="mb-2" :class="props.result.obstacleEncountered ? 'text-red-400' : 'text-green-400'">
        <span>Status: </span>
        <strong>{{ props.result.obstacleEncountered ? 'Mission Stopped' : 'Mission Completed' }}</strong>
      </p>

      <!-- Show obstacle encounter details if mission was stopped -->
      <div v-if="props.result.obstacleEncountered" class="mt-4 p-3 bg-red-900/20 border border-red-500/30 rounded-lg">
        <p class="mb-2 text-red-300">
          <span>Obstacle encountered at: </span>
          <strong>({{ props.result.obstaclePosition[0] }}, {{ props.result.obstaclePosition[1] }})</strong>
        </p>
      </div>

      <!-- Always show obstacles seen during mission -->
      <div v-if="props.result.seenObstacles?.length > 0" class="mt-4 p-3 bg-blue-900/20 border border-blue-500/30 rounded-lg">
        <p class="text-blue-200 mb-2">Obstacles detected during mission:</p>
        <div class="flex flex-wrap justify-center gap-1">
          <span
            v-for="(obstacle, index) in props.result.seenObstacles"
            :key="index"
            class="text-xs bg-blue-800/40 px-2 py-1 rounded border border-blue-600/30"
          >
            ({{ obstacle[0] }}, {{ obstacle[1] }})
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import confetti from 'canvas-confetti'

const props = defineProps({
  result: Object
})

// Handle both cases: steps exist or finalPosition directly
const finalPosition = computed(() => {
  if (props.result.steps?.length > 0) {
    return props.result.steps.at(-1)
  }
  // Fallback to finalPosition if no steps (obstacle encountered immediately)
  return props.result.finalPosition || { x: 0, y: 0, direction: 'N' }
})

onMounted(() => {
  if (!props.result.obstacleEncountered) {
    confetti({
      particleCount: 100,
      spread: 60,
      origin: { y: 0.6 }
    })
  }
})
</script>
