<script setup lang="ts">
import { computed, toRefs } from 'vue'

type ButtonVariant = "primary" | "secondary" | "outline";
type ButtonType = "button" | "submit" | "reset"

const props = withDefaults(defineProps<{
  variant?: ButtonVariant
  disabled?: boolean
  type?: ButtonType
}>(), {
  variant: 'primary',
  disabled: false,
  type: 'button'
});

const { variant, disabled, type } = toRefs(props)

const variants: Record<ButtonVariant, string> = {
  primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm hover:shadow-md disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-gray-500',
  secondary: 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-500',
  outline: 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500'
};

const buttonClasses = computed(() => variants[variant.value as ButtonVariant] ?? variants['primary'])

const emit = defineEmits<{
  (e: 'click', event: MouseEvent): void
}>()

</script>

<template>
  <div>
    <button
      :disabled="disabled"
      @click="emit('click', $event)"
      :class="buttonClasses"
      :type="type"
      :aria-disabled="disabled"
      class="px-6 py-2.5 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
    >
      <slot />
    </button>
  </div>
</template>
