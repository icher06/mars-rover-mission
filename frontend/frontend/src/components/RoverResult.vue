<template>
  <div class="mt-6">
    <h2 class="text-xl font-semibold mb-2">Resultat de la missió 🧾</h2>
    <p>Posició final: <strong>({{ lastStep.x }}, {{ lastStep.y }})</strong></p>
    <p>Direcció: <strong>{{ lastStep.direction }}</strong></p>
    <p v-if="props.obstacle">💥 Obstacle trobat a: <strong>({{ props.obstacle[0] }}, {{ props.obstacle[1] }})</strong></p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import confetti from 'canvas-confetti'

const props = defineProps({
  result: Object
})
const lastStep = props.result.steps.at(-1)
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
