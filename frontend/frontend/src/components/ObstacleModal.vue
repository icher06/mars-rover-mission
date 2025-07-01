<template>
  <!-- overlay -->
  <div
    v-if="isVisible"
    class="modal-overlay"
    @click="closeModal"
  >
    <!-- content -->
    <div
      class="modal-content"
      @click.stop
    >
      <!-- icon -->
      <div class="warning-icon">
        <svg
          width="64"
          height="64"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          class="warning-svg"
        >
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
          <path d="M12 9v4"/>
          <path d="m12 17 .01 0"/>
        </svg>
      </div>

      <!-- header -->
      <h3 class="modal-title">Mission Interrupted!</h3>

      <!-- body -->
      <div class="modal-body">
        <p class="obstacle-message">
          🤖 The rover encountered an obstacle and had to stop its mission.
        </p>

        <div class="obstacle-details">
          <div class="detail-item">
            <span class="detail-label">Obstacle Location:</span>
            <span class="detail-value">({{ obstaclePosition[0] }}, {{ obstaclePosition[1] }})</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Mission Status:</span>
            <span class="detail-value status-stopped">Stopped</span>
          </div>
        </div>

        <p class="safety-message">
          ⚠️ Safety protocols activated. The rover is secure and awaiting new instructions.
        </p>
      </div>

      <!-- footer -->
      <div class="modal-footer">
        <button
          @click="closeModal"
          class="close-button"
        >
          Acknowledge
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  obstaclePosition: Array
})

const emit = defineEmits(['close'])

const isVisible = ref(false)

onMounted(() => {
  // show modal
  setTimeout(() => {
    isVisible.value = true
  }, 100)
})

function closeModal() {
  isVisible.value = false
  setTimeout(() => {
    emit('close')
  }, 300)
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

.modal-content {
  background-color: #ffffff;
  border-radius: 16px;
  padding: 32px;
  max-width: 480px;
  width: 90%;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: slideIn 0.3s ease-out;
  text-align: center;
}

.warning-icon {
  margin: 0 auto 24px;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #fef3c7, #fbbf24);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 2s infinite;
}

.warning-svg {
  color: #d97706;
}

.modal-title {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 16px;
}

.modal-body {
  margin-bottom: 24px;
}

.obstacle-message {
  font-size: 16px;
  color: #4b5563;
  margin-bottom: 20px;
  line-height: 1.6;
}

.obstacle-details {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 20px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.detail-item:last-child {
  margin-bottom: 0;
}

.detail-label {
  font-weight: 500;
  color: #6b7280;
}

.detail-value {
  font-weight: 600;
  color: #1f2937;
}

.status-stopped {
  color: #dc2626;
  background-color: #fee2e2;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 14px;
}

.safety-message {
  font-size: 14px;
  color: #6b7280;
  font-style: italic;
}

.modal-footer {
  border-top: 1px solid #e5e7eb;
  padding-top: 20px;
}

.close-button {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border: none;
  padding: 12px 32px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.close-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 8px -1px rgba(0, 0, 0, 0.15);
}

.close-button:active {
  transform: translateY(0);
}

/* dark mode */
@media (prefers-color-scheme: dark) {
  .modal-content {
    background-color: #1f2937;
    color: #ffffff;
  }

  .modal-title {
    color: #ffffff;
  }

  .obstacle-message {
    color: #d1d5db;
  }

  .obstacle-details {
    background-color: #7f1d1d;
    border-color: #dc2626;
  }

  .detail-label {
    color: #9ca3af;
  }

  .detail-value {
    color: #ffffff;
  }

  .status-stopped {
    background-color: #7f1d1d;
    color: #fca5a5;
  }

  .safety-message {
    color: #9ca3af;
  }

  .modal-footer {
    border-color: #374151;
  }
}

/* animations */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}
</style>
