<template>
  <div>
   <NavigationBar />
    <main role="main">
      <transition name="fade" mode="out-in">
        <router-view v-slot="{ Component }">
          <component :is="Component" />
        </router-view>
      </transition>
    </main>

    <AppFooter />
  </div>
</template>

<script setup lang="ts">
import NavigationBar from '@/components/navigation/NavigationBar.vue'
import AppFooter from '@/components/footer/AppFooter.vue'
</script>

<style scoped>
/* Simple fade transition for route changes */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
