<template>
  <div class="flex flex-col items-center justify-center py-20 text-center text-gray-300">
    <div class="text-6xl animate-bounce" style="margin-top: 20px">🤖</div>
    <p class="mt-4 text-lg">Waiting for a new mission...</p>
  </div>
</template>

<style scoped>
.animate-bounce {
  animation: bounce 1s infinite alternate;
}

@keyframes bounce {
  0% { transform: translateY(0); }
  100% { transform: translateY(-10px); }
}
</style>
