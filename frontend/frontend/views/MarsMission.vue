<template>
  <main class="flex flex-col items-center p-4 space-y-6 min-h-screen">
    <h1 class="text-3xl font-bold">Mars Rover Mission</h1>

    <RoverForm @mission-executed="onMissionExecuted" />

    <div class="overflow-auto max-w-full border border-gray-700">
      <RoverMap
        v-if="shouldShowMap"
        :steps="result.steps"
        :obstacle="result.obstaclePosition"
        :obstacles="result.obstacles"
        :finalPosition="result.finalPosition"
        @animation-finished="onAnimationFinished"
      />
      <RobotIdle v-else />
    </div>

    <!-- Show results for both completed and stopped missions -->
    <RoverResult v-if="animationDone && result" :result="resultWithSeenObstacles" />
  </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import RoverForm from '../src/components/RoverForm.vue'
import RoverResult from '../src/components/RoverResult.vue'
import RoverMap from '../src/components/RoverMap.vue'
import RobotIdle from '../src/components/RobotIdle.vue'

const result = ref(null)
const animationDone = ref(false)
const seenObstacles = ref([])

const shouldShowMap = computed(() => {
  return result.value &&
    (result.value.steps?.length > 0 || result.value.obstacles?.length > 0)
})

const resultWithSeenObstacles = computed(() => {
  if (!result.value) return null
  return {
    ...result.value,
    seenObstacles: seenObstacles.value
  }
})

function onMissionExecuted(data) {
  result.value = data
  animationDone.value = false
  seenObstacles.value = []
}

function onAnimationFinished(data) {
  animationDone.value = true
  if (data?.seenObstacles) {
    seenObstacles.value = data.seenObstacles
  }
}
</script>
