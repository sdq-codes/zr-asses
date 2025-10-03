<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, defineProps, nextTick, defineExpose } from 'vue'
import { debounce } from "lodash-es";
import { buildPath } from "@/helpers/pathBuilder.ts";

interface Connection {
  originId: string
  destinationId: string
}

const props = defineProps<{
  connections: Connection[]
}>()

const containerElement = ref<HTMLElement | null>(null)

const entityRegistry = new Map<string | number, HTMLElement>()

function registerEntity(el: HTMLElement | null, id: string | number) {
  if (el) {
    entityRegistry.set(id, el)
  } else {
    entityRegistry.delete(id)
  }
}

defineExpose({
  registerEntity
})

interface ArrowPath {
  path: string;
  id: string;
  connection: Connection;
  length: number;
  color: string;
}

const arrowPaths = ref<ArrowPath[]>([])
const showArrows = computed(() => props.connections.length > 0)

// Color palette with consistent tone/saturation
const colors = [
  '#7878f6', // Blue
  '#f67878', // Red
  '#78f678', // Green
  '#f6c878', // Orange
  '#f678f6', // Magenta
  '#78f6f6', // Cyan
  '#c878f6', // Purple
  '#f6f678', // Yellow
]

const getColorForIndex = (index: number): string => {
  return colors[index % colors.length] ?? "#7878f6";
}

const calculatePaths = async () => {
  if (!showArrows.value || !containerElement.value) {
    arrowPaths.value = []
    return
  }

  await nextTick() // wait for DOM updates

  const containerRect = containerElement.value.getBoundingClientRect()
  const paths: ArrowPath[] = []

  props.connections.forEach((connection, index) => {
    const originEl = entityRegistry.get(connection.originId)
    const destEl = entityRegistry.get(connection.destinationId)

    if (!originEl || !destEl) return

    const originRect = originEl.getBoundingClientRect()
    const destRect = destEl.getBoundingClientRect()

    // Relative positions
    const startX = originRect.right - containerRect.left
    const startY = originRect.top + originRect.height / 2 - containerRect.top
    const endX = destRect.left - containerRect.left
    const endY = destRect.top + destRect.height / 2 - containerRect.top

    // Rounded step path
    const midX = (startX + endX) / 2
    const radius = 12
    const path = buildPath(startX, startY, midX, endX, endY, radius)

    // Create a real <path> element once to measure
    const tempPath = document.createElementNS("http://www.w3.org/2000/svg", "path")
    tempPath.setAttribute("d", path)
    const length = tempPath.getTotalLength()

    const color = getColorForIndex(index)

    paths.push({
      path,
      id: `arrow-${connection.originId}-${connection.destinationId}`,
      connection,
      length,
      color,
    })
  })

  arrowPaths.value = paths
}

// Watch for changes
watch(
  () => props.connections,
  () => {
    calculatePaths()
  },
  { deep: true, immediate: true }
)

const handleResize = debounce(() => {
  calculatePaths()
}, 100)

onMounted(() => {
  window.addEventListener('resize', handleResize)
  calculatePaths()
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<template>
  <div ref="containerElement" class="relative">
    <!-- SVG Arrow Overlay -->
    <svg
      v-if="showArrows"
      class="absolute top-0 left-0 w-full h-full pointer-events-none z-10"
      style="overflow: visible;"
    >
      <defs>
        <!-- Arrow markers with different colors -->
        <marker
          v-for="(color, index) in colors"
          :key="`marker-${index}`"
          :id="`arrowhead-${index}`"
          markerWidth="6"
          markerHeight="6"
          refX="3"
          refY="3"
          orient="auto"
        >
          <path d="M 1 1 L 5 3 L 1 5 z" :fill="color" />
        </marker>
      </defs>

      <!-- Render multiple arrow paths -->
      <path
        v-for="(arrow, index) in arrowPaths"
        :key="arrow.id"
        :d="arrow.path"
        fill="none"
        :stroke="arrow.color"
        stroke-width="2"
        :marker-end="`url(#arrowhead-${index % colors.length})`"
        class="arrow-path"
        stroke-linecap="round"
        stroke-linejoin="round"
        :style="{
          strokeDasharray: arrow.length,
          strokeDashoffset: arrow.length
        }"
      />
    </svg>

    <!-- Content slot -->
    <slot />
  </div>
</template>

<style scoped>
.arrow-path {
  animation: draw-arrow 1.5s ease-out forwards;
}

@keyframes draw-arrow {
  to {
    stroke-dashoffset: 0;
  }
}
</style>
