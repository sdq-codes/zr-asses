<template>
  <!-- Background overlay -->
  <div class="" />
  <div
    v-if="model"
    class="fixed inset-0 bg-gradient-to-b from-black/10 to-black/0 backdrop-blur-[2px] z-50 flex items-center justify-center bg-black/50"
  >
    <!-- Modal container -->
    <div
      :class="modalClass"
      class="bg-white absolute rounded-xl shadow-lg w-[90vw] md:w-full max-h-[80vh] "
    >
      <div class="header h-[10%] p-6 px-10">
        <div class="flex w-full">
          <h6 class="text-xl font-semibold my-auto text-[#000000]">
            <slot name="title" />
          </h6>
          <button
            class="ml-auto cursor-pointer text-gray-500 hover:text-gray-800 text-2xl my-auto"
            @click="model = false"
            aria-label="Close modal"
          >
            ✕
          </button>
        </div>
      </div>
      <div class="body h-[80%] overflow-scroll relative  p-6 px-10">
        <slot />
      </div>
      <div class="footer h-[10%]  p-6 px-10">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {computed, defineModel, defineProps} from "vue";

const props = withDefaults(defineProps<{
  size: 'lg' | 'xl',
}>(), {
  size: 'lg'
});

const model = defineModel<boolean>({ default: false });

const sizeClasses: Record<string, string> = {
  xl: 'max-w-3xl',
  lg: 'max-w-xl'
}

const modalClass = computed(() => {
  return sizeClasses[props.size]
})
</script>
