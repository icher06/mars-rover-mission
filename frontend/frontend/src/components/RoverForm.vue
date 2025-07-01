<template>
  <form @submit.prevent="startMission" class="space-y-6" style="display: inline-block;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">X:</label>
      <input type="number" v-model.number="x" min="0" max="199" :disabled="positionLocked" :class="{ 'error-input': hasInvalidX }" @input="validateX" @blur="validateX" required />
    </div>
    <div v-if="hasInvalidX" class="error-message">
        X must be between 0 and 199
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Y:</label>
      <input type="number" v-model.number="y" min="0" max="199" :disabled="positionLocked" style=" margin-top: 10px" :class="{ 'error-input': hasInvalidY }" @input="validateY" @blur="validateY" required />
    </div>
    <div v-if="hasInvalidY" class="error-message">
        Y must be between 0 and 199
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Direction:</label>
      <select v-model="direction" :disabled="positionLocked" style="margin-top: 10px">
        <option>N</option>
        <option>E</option>
        <option>S</option>
        <option>W</option>
      </select>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <label style="min-width: 100px; text-align: right;">Commands:</label>
      <div style="display: flex; flex-direction: column; margin-top: 10px">
        <input
          type="text"
          v-model="commands"
          placeholder="Ex: FFRFL"
          :class="{ 'error-input': hasInvalidCommands }"
          @input="validateCommands"
          required
        />
        <div class="command-help">
          <small style="color: #6b7280;">F = Forward, L = Left, R = Right</small>
        </div>
        <div v-if="hasInvalidCommands" class="error-message">
          Only F, R, and L characters are allowed!
        </div>
      </div>
    </div>

    <div class="button-container">
      <button type="submit" class="btn" :disabled="hasInvalidCommands || hasInvalidX || hasInvalidY">Submit</button>
      <button type="button" class="btn" @click="clearMission">Clear</button>
    </div>
  </form>
</template>

<style scoped>
/* form styles */
.button-container {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn {
  padding: 0.5rem 1.25rem;
  background-color: #374151;
  color: white;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, box-shadow 0.2s;
}

.btn:hover:not(:disabled) {
  background-color: #1f2937;
  box-shadow: 0 0 0 2px #6ee7b7;
}

.btn:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
  opacity: 0.6;
}

.error-input {
  border: 2px solid #ef4444 !important;
  background-color: #fef2f2;
}

.error-message {
  color: #ef4444;
  font-size: 0.75rem;
  font-weight: 500;
  margin-top: 0.25rem;
}

.command-help {
  margin-top: 0.25rem;
}

input[type="text"], input[type="number"], select {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

input[type="text"]:focus, input[type="number"]:focus, select:focus {
  outline: none;
  border-color: #6ee7b7;
  box-shadow: 0 0 0 2px rgba(110, 231, 183, 0.2);
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { startMissionAPI, executeMissionAPI } from '../../api/rover.js'

const emit = defineEmits(['mission-executed'])

const x = ref(0)
const y = ref(0)
const direction = ref('N')
const commands = ref('')
const positionLocked = ref(false)

// validate command input
const hasInvalidCommands = computed(() => {
  if (!commands.value) return false
  const validPattern = /^[FRLfrl]*$/
  return !validPattern.test(commands.value)
})

// validate coordinates
const hasInvalidX = computed(() => {
  return x.value < 0 || x.value > 199 || isNaN(x.value)
})

const hasInvalidY = computed(() => {
  return y.value < 0 || y.value > 199 || isNaN(y.value)
})

function validateCommands() {
  // enforce uppercase
  commands.value = commands.value.toUpperCase()

  // drop invalid letters
  commands.value = commands.value.replace(/[^FRL]/g, '')
}
function validateX() {
  if (x.value < 0) x.value = 0
  if (x.value > 199) x.value = 199
  if (isNaN(x.value)) x.value = 0
}

function validateY() {
  if (y.value < 0) y.value = 0
  if (y.value > 199) y.value = 199
  if (isNaN(y.value)) y.value = 0
}
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

  // auto load map if saved
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
  // block submit when invalid
  if (hasInvalidCommands.value) {
    return
  }

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

  // lock position after run
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
