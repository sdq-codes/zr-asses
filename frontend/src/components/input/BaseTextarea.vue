<script setup lang="ts">
import { withDefaults, defineProps, defineModel } from 'vue'

const model = defineModel<string>()

const props = withDefaults(defineProps<{
  placeholder?: string
  disabled?: boolean
  id?: string
  name?: string
  rows?: number
  resize?: 'none' | 'x' | 'y' | 'both'
  error?: string
}>(), {
  placeholder: '',
  disabled: false,
  rows: 3,
  resize: 'y',
})
</script>

<template>
  <div class="flex flex-col gap-1">
    <textarea
      v-model="model"
      :id="props.id"
      :name="props.name"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      :rows="props.rows"
      :class="[
        'px-4 py-2.5 border rounded-lg transition-all duration-200 outline-none focus:ring-2 focus:border-transparent',
        props.error
          ? 'border-red-500 focus:ring-red-500'
          : 'border-gray-300 focus:ring-indigo-500',
        props.resize === 'none' && 'resize-none',
        props.resize === 'x' && 'resize-x',
        props.resize === 'y' && 'resize-y',
        props.resize === 'both' && 'resize',
      ]"
    />
    <p v-if="props.error" class="text-sm text-red-500">{{ props.error }}</p>
  </div>
</template>
