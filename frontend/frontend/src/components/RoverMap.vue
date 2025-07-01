<script setup>
import { ref, onMounted, watch, computed } from 'vue'

const props = defineProps({
  steps: Array,
  obstacle: Array,
  obstacles: Array,
  finalPosition: Object
})

const emit = defineEmits(['animation-finished'])

const MAP_SIZE = 200
const VIEWPORT_SIZE = 11
const CELL_SIZE = 30

const currentStepIndex = ref(0)
const isAnimating = ref(false)
const seenObstacles = ref(new Set()) // Track unique obstacles seen during mission

const currentStep = computed(() => {
  if (props.steps?.length) {
    return props.steps[currentStepIndex.value] || props.steps.at(-1)
  }
  return { x: props.finalPosition?.x || 0, y: props.finalPosition?.y || 0 }
})

const roverX = computed(() => Math.floor(currentStep.value.x))
const roverY = computed(() => Math.floor(currentStep.value.y))

// Calculate viewport bounds - center the rover in the 11x11 view
const viewportBounds = computed(() => {
  const halfView = Math.floor(VIEWPORT_SIZE / 2)
  let startX = roverX.value - halfView
  let startY = roverY.value - halfView

  // Keep viewport within map bounds
  startX = Math.max(0, Math.min(startX, MAP_SIZE - VIEWPORT_SIZE))
  startY = Math.max(0, Math.min(startY, MAP_SIZE - VIEWPORT_SIZE))

  return {
    startX,
    startY,
    endX: startX + VIEWPORT_SIZE,
    endY: startY + VIEWPORT_SIZE
  }
})

// Get obstacles visible in current viewport
const visibleObstacles = computed(() => {
  const bounds = viewportBounds.value
  return props.obstacles?.filter(([x, y]) =>
    x >= bounds.startX && x < bounds.endX &&
    y >= bounds.startY && y < bounds.endY
  ) || []
})

// Check if mission was stopped (no steps or empty steps)
const missionStopped = computed(() => {
  return !props.steps || props.steps.length === 0
})

// Track obstacles seen in current viewport
const trackVisibleObstacles = () => {
  visibleObstacles.value.forEach(([x, y]) => {
    seenObstacles.value.add(`${x},${y}`)
  })
}

function isObstacle(viewX, viewY) {
  const mapX = viewportBounds.value.startX + viewX
  const mapY = viewportBounds.value.startY + viewY
  return visibleObstacles.value.some(([x, y]) => x === mapX && y === mapY)
}

function isRover(viewX, viewY) {
  const mapX = viewportBounds.value.startX + viewX
  const mapY = viewportBounds.value.startY + viewY
  return roverX.value === mapX && roverY.value === mapY
}

function animateStep() {
  // Track obstacles at current position
  trackVisibleObstacles()

  if (currentStepIndex.value < props.steps.length - 1) {
    isAnimating.value = true
    setTimeout(() => {
      currentStepIndex.value++
      animateStep()
    }, 500)
  } else {
    // Final tracking
    trackVisibleObstacles()
    isAnimating.value = false

    // Convert seen obstacles back to array format
    const seenObstaclesList = Array.from(seenObstacles.value).map(coord => {
      const [x, y] = coord.split(',').map(Number)
      return [x, y]
    })

    // Emit with seen obstacles
    emit('animation-finished', { seenObstacles: seenObstaclesList })
  }
}

function startAnimation() {
  if (props.steps?.length > 0) {
    seenObstacles.value.clear() // Reset seen obstacles
    currentStepIndex.value = 0
    animateStep()
  } else {
    // If no steps, just track current position
    trackVisibleObstacles()
    const seenObstaclesList = Array.from(seenObstacles.value).map(coord => {
      const [x, y] = coord.split(',').map(Number)
      return [x, y]
    })
    emit('animation-finished', { seenObstacles: seenObstaclesList })
  }
}

onMounted(() => {
  startAnimation()
})

watch(
  () => props.steps,
  (newVal) => {
    startAnimation()
  }
)
</script>

<template>
  <div class="mars-container">
    <div class="mars-status">
      <span v-if="isAnimating" class="text-orange-400">
        🤖 Moving... Step {{ currentStepIndex + 1 }}/{{ props.steps?.length || 0 }}
      </span>
      <span v-else-if="missionStopped" class="text-red-400">
        Mission Stopped!
      </span>
      <span v-else class="text-green-400">
        Mission Complete!
      </span>
    </div>

    <!-- Viewport Info -->
    <div class="viewport-info">
      <div>Rover Position: ({{ roverX }}, {{ roverY }})</div>
      <div>Viewport: ({{ viewportBounds.startX }}, {{ viewportBounds.startY }}) to ({{ viewportBounds.endX - 1 }}, {{ viewportBounds.endY - 1 }})</div>
      <div>Visible Obstacles: {{ visibleObstacles.length }}</div>
      <div>Total Seen: {{ seenObstacles.size }}</div>
    </div>

    <div class="mars-grid">
      <div
        v-for="row in VIEWPORT_SIZE"
        :key="`row-${row}`"
        class="mars-row"
      >
        <div
          v-for="col in VIEWPORT_SIZE"
          :key="`cell-${row}-${col}`"
          class="mars-cell"
          :class="{
            'obstacle': isObstacle(col - 1, row - 1),
            'rover-cell': isRover(col - 1, row - 1)
          }"
        >
          <span v-if="isRover(col - 1, row - 1)" class="rover">🤖</span>
          <span v-else-if="isObstacle(col - 1, row - 1)" class="rock">🪨</span>
          <span v-else class="coords">{{ viewportBounds.startX + col - 1 }},{{ viewportBounds.startY + row - 1 }}</span>
        </div>
      </div>
    </div>

    <div class="mars-legend">
      <div><span class="legend-mars"></span> Mars Surface</div>
      <div><span class="legend-obstacle"></span> Obstacles</div>
      <div><span class="rover">🤖</span> Rover</div>
    </div>
  </div>
</template>

<!-- Keep the same styles -->
<style scoped>
.mars-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
}

.mars-status {
  font-size: 0.875rem;
  font-weight: 600;
}

.viewport-info {
  font-size: 0.75rem;
  text-align: center;
  color: #9ca3af;
  line-height: 1.4;
}

.mars-grid {
  display: flex;
  flex-direction: column;
  border: 3px solid #9a3412;
  border-radius: 8px;
  overflow: hidden;
  background: #1a1a1a;
  box-shadow: 0 0 20px rgba(234, 88, 12, 0.3);
}

.mars-row {
  display: flex;
}

.mars-cell {
  width: 30px;
  height: 30px;
  border: 1px solid #451a03;
  background-color: #ea580c;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  transition: all 0.3s;
  position: relative;
}

.mars-cell.obstacle {
  background-color: #6b7280;
}

.mars-cell.rover-cell {
  background-color: #fbbf24;
  animation: pulse 1s infinite;
  box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}

.rover {
  font-size: 18px;
  animation: bounce 0.8s infinite alternate;
  z-index: 10;
}

.rock {
  font-size: 12px;
  opacity: 0.8;
  z-index: 5;
}

.coords {
  font-size: 6px;
  color: rgba(255, 255, 255, 0.3);
  text-align: center;
  line-height: 1;
}

.mars-legend {
  display: flex;
  gap: 1rem;
  font-size: 0.75rem;
  align-items: center;
}

.mars-legend > div {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.legend-mars {
  width: 12px;
  height: 12px;
  background-color: #ea580c;
  border: 1px solid #451a03;
  border-radius: 2px;
}

.legend-obstacle {
  width: 12px;
  height: 12px;
  background-color: #6b7280;
  border: 1px solid #374151;
  border-radius: 2px;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes bounce {
  0% { transform: translateY(0); }
  100% { transform: translateY(-3px); }
}
</style>
