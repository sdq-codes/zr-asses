<script setup lang="ts">
import { defineProps, defineModel } from 'vue'

const model = defineModel<string>();

const props = withDefaults(defineProps<{
  type?: string;
  placeholder?: string;
  disabled?: boolean;
}>(), {
  type: 'text',
  placeholder: '',
  disabled: false,
});
</script>

<template >
  <input
    :type="props.type"
    v-model="model"
    :placeholder="props.placeholder"
    :disabled="props.disabled"
    class="px-4 py-2.5 border border-gray-300 rounded-lg
           focus:ring-2 focus:ring-indigo-500 focus:border-transparent
           transition-all duration-200 outline-none"
  />
</template>
