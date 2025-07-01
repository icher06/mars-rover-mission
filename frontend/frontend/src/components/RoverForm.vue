<template>
  <form @submit.prevent="startMission" class="space-y-6" style="display: inline-block;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">X:</label>
      <input type="number" v-model.number="x" min="0" max="199" :disabled="positionLocked" required />
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Y:</label>
      <input type="number" v-model.number="y" min="0" max="199" :disabled="positionLocked" required />
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Direction:</label>
      <select v-model="direction" :disabled="positionLocked">
        <option>N</option>
        <option>E</option>
        <option>S</option>
        <option>W</option>
      </select>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Commands:</label>
      <input type="text" v-model="commands" placeholder="Ex: FFRFL" required />
    </div>

    <div class="button-container">
      <button type="submit">Enviar</button>
      <button type="button" @click="clearMission">Clear</button>
    </div>
  </form>
</template>

<style scoped>
.button-container {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
}
</style>
<script setup>
import { ref, onMounted } from 'vue'
import { startMissionAPI, executeMissionAPI } from '../../api/rover.js'
const emit = defineEmits(['mission-executed'])

const x = ref(0)
const y = ref(0)
const direction = ref('N')
const commands = ref('')
const positionLocked = ref(false)

onMounted(() => {
  const saved = localStorage.getItem('lastRoverPosition')
  const savedObstacles = localStorage.getItem('obstacles')

  if (saved) {
    try {
      const { x: savedX, y: savedY, direction: savedDir } = JSON.parse(saved)
      x.value = savedX
      y.value = savedY
      direction.value = savedDir
      positionLocked.value = true
    } catch (e) {
      console.error('Error al leer lastRoverPosition:', e)
      localStorage.removeItem('lastRoverPosition')
    }
  }

  // ⚠️ Emitimos el mapa automáticamente si hay datos
  if (saved && savedObstacles) {
    const position = JSON.parse(saved)
    const obstacles = JSON.parse(savedObstacles)

    emit('mission-executed', {
      finalPosition: position,
      steps: [],
      obstacles: obstacles,
      obstacleEncountered: false,
      obstaclePosition: null
    })
  }
})

async function startMission() {
  let obstacles = JSON.parse(localStorage.getItem('obstacles'))

  if (!obstacles) {
    const res = await startMissionAPI({ x: x.value, y: y.value, direction: direction.value })
    obstacles = res.obstacles
    localStorage.setItem('obstacles', JSON.stringify(obstacles))
  }

  const res = await executeMissionAPI({
    x: x.value,
    y: y.value,
    direction: direction.value,
    commands: commands.value,
    obstacles: obstacles
  })

  const final = res.finalPosition
  localStorage.setItem('lastRoverPosition', JSON.stringify({
    x: final.x,
    y: final.y,
    direction: final.direction
  }))

  localStorage.setItem('lastMission', JSON.stringify(res))
  emit('mission-executed', res)

  // Bloquea posición después de ejecutar
  x.value = final.x
  y.value = final.y
  direction.value = final.direction
  positionLocked.value = true
}

function clearMission() {
  localStorage.clear()
  x.value = 0
  y.value = 0
  direction.value = 'N'
  commands.value = ''
  positionLocked.value = false
emit('mission-executed', {
     finalPosition: { x: 0, y: 0, direction: 'N' },
    steps: [],
    obstacles: []
})
}
</script>
