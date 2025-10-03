<script setup lang="ts">
import { defineModel, ref, watch } from 'vue'

const model = defineModel<string | null>("");

const isValid = ref(true);

watch(
  () => model.value,
  (newValue) => {
    if (newValue) {
      const urlPattern = /^https?:\/\/.+/i;
      isValid.value = urlPattern.test(newValue);
    } else {
      isValid.value = true;
    }
  },
  { immediate: true }
);
</script>

<template>
  <div class="flex-1">
    <input
      type="url"
      v-model="model"
      :class="[
        'w-full px-4 py-2.5 text-[#3434F4] border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none',
        isValid ? 'border-gray-300' : 'border-red-300'
      ]"
    />
    <p v-if="!isValid" aria-describedby="This is an invalid url" class="text-red-500 text-xs mt-1">
      Please enter a valid URL starting with http:// or https://
    </p>
  </div>
</template>

<style scoped>
input {
  position: relative;
  border-radius: 12px;
  background:
    linear-gradient(white, white) padding-box,
    linear-gradient(180deg, rgba(20, 184, 184, 0.24) 0%, rgba(117, 117, 240, 0.24) 100%) border-box;
  border: 4px solid transparent;
}
</style>
