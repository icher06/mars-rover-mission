<template>
  <form @submit.prevent="startMission" class="space-y-4">
    <div>
      <label>X:</label>
      <input type="number" v-model.number="x" min="0" max="199" required />
    </div>
    <div>
      <label>Y:</label>
      <input type="number" v-model.number="y" min="0" max="199" required />
    </div>
    <div>
      <label>Direcció:</label>
      <select v-model="direction">
        <option>N</option>
        <option>E</option>
        <option>S</option>
        <option>W</option>
      </select>
    </div>
    <div>
      <label>Comandes:</label>
      <input type="text" v-model="commands" placeholder="Ex: FFRFL" required />
    </div>
    <div class="flex gap-4">
      <button type="submit">Enviar</button>
      <button type="button" @click="clearMission">Clear</button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { startMissionAPI, executeMissionAPI } from '../../api/rover.js'
const emit = defineEmits(['mission-executed'])

const x = ref(0)
const y = ref(0)
const direction = ref('N')
const commands = ref('')

async function startMission() {
  // Comprova si ja hi ha obstacles
  let obstacles = JSON.parse(localStorage.getItem('obstacles'))

  if (!obstacles) {
    const res = await startMissionAPI({ x: x.value, y: y.value, direction: direction.value })
    obstacles = res.obstacles
    localStorage.setItem('obstacles', JSON.stringify(obstacles))
    localStorage.setItem('start', JSON.stringify({ x: x.value, y: y.value, direction: direction.value }))
  }

  const res = await executeMissionAPI({
    x: x.value,
    y: y.value,
    direction: direction.value,
    commands: commands.value,
    obstacles: obstacles
  })

  localStorage.setItem('lastMission', JSON.stringify(res))
  emit('mission-executed', res)
}

function clearMission() {
  localStorage.clear()
  location.reload()
}
</script>
