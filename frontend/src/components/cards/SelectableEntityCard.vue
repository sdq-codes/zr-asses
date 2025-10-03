<template>
  <div>
    <BasicModal  v-model="showEditModal" size="xl">
      <template #title>
        Edit <span class="capitalize">{{ entityType }}</span>
      </template>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Name
          </label>
          <BaseInput
            v-model="name"
            type="text"
            class="w-full border rounded-lg px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description
          </label>
          <textarea
            v-model="description"
            class="w-full border rounded-lg px-3 py-2"
            rows="3"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Image URL
          </label>
          <BaseInput
            v-model="imageUrl"
            type="text"
            class="w-full border rounded-lg px-3 py-2"
          />
        </div>

        <div v-if="imageUrl" class="mt-4">
          <p class="text-sm text-gray-600 mb-2">Preview:</p>
          <img :src="imageUrl" alt="Preview" class="h-20 object-contain border rounded-md p-1" />
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-2">
          <button
            class="px-4 py-2 border cursor-pointer rounded-lg"
            @click="showEditModal = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-indigo-600 cursor-pointer text-white rounded-lg"
            @click="saveChanges"
          >
            Save
          </button>
        </div>
      </template>
    </BasicModal>
    <div
      :data-entity-id="item.id"
      class="rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-200 border my-4 cursor-pointer group entity-card"
      :class="[
      model === item.id ? 'border-[#3434F4] bg-[#EBEBFE]' : 'bg-white hover:bg-[#f3f4f7] border-gray-100',
      isAnimating ? 'animate-click' : ''
    ]"
      role="button"
      :aria-selected="model === item.id"
      @click="handleClick"
    >
      <div class="flex items-start gap-4">
        <div
          :style="{
          background: `linear-gradient(135deg, ${colorScheme?.gradientStart} 0%, ${colorScheme?.gradientEnd} 100%)`
        }"
          class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 relative overflow-hidden transition-transform duration-200"
          :class="isAnimating ? 'scale-95' : ''"
        >
          <img
            v-if="item.image"
            :src="item.image"
            :alt="item.name"
            class="w-8 h-8 bg-white rounded-lg object-contain"
          />
          <div v-else class="w-8 h-8 bg-gray-300 rounded"></div>
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-start">
            <h3 class="font-semibold text-sm text-[#001414] mb-1">
              {{ item.name }}
            </h3>

            <div class="flex items-center justify-center ml-auto gap-1">
              <IconPlug
                v-if="entityType === 'origin'"
                class="cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100 text-[#8D95A5] hover:text-black"
                @click="model = item.id"
              />
              <IconPlugConnected
                v-if="entityType === 'destination'"
                class="cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100 text-[#8D95A5] hover:text-black"
                @click="model = item.id"
              />

              <IconPencil
                class="text-[#8D95A5] hover:text-black cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                @click.stop="showEditModal = !showEditModal"
              />
              <IconTrash
                class="text-[#8D95A5] hover:text-black cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                @click.stop="deleteEntity"
              />
            </div>

          </div>
          <p class="text-xs font-regular text-[#515867] leading-relaxed">
            {{ item.description }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineModel, toRefs } from 'vue'
import { IconPlug, IconPencil, IconTrash, IconPlugConnected } from '@tabler/icons-vue';
import BasicModal from '@/components/modals/BasicModal.vue'
import BaseInput from '@/components/input/BaseInput.vue'
import { useOriginsAPI } from '@/composables/apis/origin.ts'
import type { OriginDTO, UpdateOriginDTO } from '@/composables/dtos/origin.ts'
import type { DestinationDTO, UpdateDestinationDTO } from '@/composables/dtos/destination.ts'
import { useDestinationAPI } from '@/composables/apis/destination.ts'

const props = defineProps<{
  item: DestinationDTO | OriginDTO;
  entityType: "origin" | "destination";
}>()

const { item } = toRefs(props)

const model = defineModel<string | undefined>();
const showEditModal = ref<boolean>(false)
const isAnimating = ref<boolean>(false)

const name = ref<string>(item.value.name)
const description = ref<string>(item.value.description)
const imageUrl = ref<string>(item.value.image)

const emit = defineEmits(['edit', 'delete']);

function handleClick() {
  isAnimating.value = true

  setTimeout(() => {
    isAnimating.value = false
  }, 400)
}

async function saveChanges() {
  const formData: [string, UpdateOriginDTO | UpdateDestinationDTO] = [item.value.id, {
    name: name.value,
    description: description.value,
    image: imageUrl.value,
  }];
  if (props.entityType === 'origin') {
    await useOriginsAPI().UpdateOrigin(...formData)
  } else if (props.entityType  === 'destination') {
    await useDestinationAPI().UpdateDestination(...formData)
  }

  emit('edit')
  showEditModal.value = false
}

async function deleteEntity() {
  if (props.entityType  === 'origin') {
    await useOriginsAPI().DeleteOrigin(item.value.id)
  } else if (props.entityType  === 'destination') {
    await useDestinationAPI().DeleteDestination(item.value.id)
  }
  emit('delete')
  showEditModal.value = false
}

const colorSchemes = [
  { gradientStart: '#000000', gradientEnd: '#F8981D' },
  { gradientStart: '#4285F4', gradientEnd: '#EA4335' },
  { gradientStart: '#E2001A', gradientEnd: '#FFFFFF' },
  { gradientStart: '#0071CE', gradientEnd: '#FFC120' },
];

const colorScheme = ref<{ gradientStart: string; gradientEnd: string }>(
  colorSchemes[Math.floor(Math.random() * colorSchemes.length)]!
);
</script>

<style scoped>
img {
  box-shadow: 0 4px 4px 0 #00000040;
}

@keyframes click-pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.97);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes ripple {
  0% {
    box-shadow: 0 0 0 0 rgba(52, 52, 244, 0.4);
  }
  50% {
    box-shadow: 0 0 0 8px rgba(52, 52, 244, 0.1);
  }
  100% {
    box-shadow: 0 0 0 12px rgba(52, 52, 244, 0);
  }
}

.animate-click {
  animation: click-pulse 0.4s ease-out, ripple 0.6s ease-out;
}
</style>
