<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useDashboardAPI } from '@/composables/apis/dashboard.ts'
import SelectableEntityCard from '@/components/cards/SelectableEntityCard.vue'
import type { OriginDTO } from '@/composables/dtos/origin.ts'
import type { DestinationDTO } from '@/composables/dtos/destination.ts'
import type { CreateTaskDTO, TaskDTO } from '@/composables/dtos/Task.ts'
import ScrapeData from '@/components/sections/ScrapeData.vue'
import AnimatedConnectionArrow from '@/components/AnimatedConnectionArrow.vue'
import { useTasksAPI } from '@/composables/apis/tasks.ts'
import BasicModal from '@/components/modals/BasicModal.vue'
import BaseInput from '@/components/input/BaseInput.vue'
import { useOriginsAPI } from '@/composables/apis/origin.ts'
import { useDestinationAPI } from '@/composables/apis/destination.ts'
import BaseTextarea from '@/components/input/BaseTextarea.vue'

const allowMultiplePendingTasks = ref(false)

const isLoaded = ref(false)
const tasks = ref<TaskDTO[]>()
const origins = ref<OriginDTO[]>()
const destinations = ref<DestinationDTO[]>()
const selectedOrigin = ref<string>()
const selectedDestination = ref<string>()
const cronSchedule = ref<string>( "0 8 * * 1")

// Create modal state
const showCreateModal = ref(false)
const createEntityType = ref<'origin' | 'destination'>('origin')
const newEntityName = ref('')
const newEntityDescription = ref('')
const newEntityImageUrl = ref('')

const open = ref(false);

const arrowsRef = ref<InstanceType<typeof AnimatedConnectionArrow> | null>(null)

const pendingUpdates = ref<Array<{ taskId: string, newDestinationId: string, oldDestinationId: string, originName: string , destinationName: string}>>([])
const pendingNewTasks = ref<Array<{ originId: string, destinationId: string, tempId: string, destinationName: string, originName: string }>>([])

onMounted(async () => {
  await dashboardInfo()
  isLoaded.value = true
})

const dashboardInfo = async () => {
  const dashboardApiResponse = await useDashboardAPI().FetchDashboardInfo()
  if (dashboardApiResponse.success) {
    tasks.value = dashboardApiResponse.data.data.tasks
    origins.value = dashboardApiResponse.data.data.origins
    destinations.value = dashboardApiResponse.data.data.destinations
  }
}

// Build connections from tasks AND pending new tasks
const connections = computed(() => {
  // Existing tasks
  const existingConnections = tasks.value?.map(task => ({
    originId: task.origin.id,
    destinationId: task.destination.id,
    taskId: task.id
  })) ?? []

  // Pending new tasks
  const pendingConnections = pendingNewTasks.value.map(pending => ({
    originId: pending.originId,
    destinationId: pending.destinationId,
    taskId: pending.tempId
  }))

  return [...existingConnections, ...pendingConnections]
})

// Get all origin IDs that have connections (including pending)
const connectedOriginIds = computed(() =>
  connections.value.map(c => c.originId)
)

// Get all destination IDs that have connections (including pending)
const connectedDestinationIds = computed(() =>
  connections.value.map(c => c.destinationId)
)

// Check if an origin already has a connection
const getOriginConnection = (originId: string) => {
  return connections.value.find(c => c.originId === originId)
}

// Handle origin click
const handleOriginClick = (originId: string) => {
  // If single pending task mode is enabled and there's already a pending task for a different origin
  if (!allowMultiplePendingTasks.value && pendingNewTasks.value.length > 0) {
    const pendingOriginId = pendingNewTasks.value[0].originId
    if (pendingOriginId !== originId) {
      // Remove the old pending task before selecting new origin
      pendingNewTasks.value = []
    }
  }

  if (selectedOrigin.value === originId) {
    selectedOrigin.value = ""
    selectedDestination.value = ""
  } else {
    const existingConnection = getOriginConnection(originId)

    if (existingConnection) {
      // Origin already connected - select it to change destination
      selectedOrigin.value = originId
    } else {
      // New origin - select it
      selectedOrigin.value = originId
    }
  }
}

// Handle destination click
const handleDestinationClick = async (destinationId: string) => {
  console.log(destinationId)
  console.log(selectedDestination.value)
  if (selectedDestination.value === destinationId) {
    selectedDestination.value = ""
    return
  }
  if (!selectedOrigin.value) {
    // No origin selected, just highlight the destination
    selectedDestination.value = destinationId
    return
  }

  const existingConnection = getOriginConnection(selectedOrigin.value)

  if (existingConnection) {
    // Update existing connection
    if (existingConnection.destinationId === destinationId) {
      // Clicking same destination - deselect
      selectedOrigin.value = ""
      selectedDestination.value = ""
      return
    }

    // Check if this is a real task or pending new task
    const isNewTask = pendingNewTasks.value.some(t => t.tempId === existingConnection.taskId)

    if (isNewTask) {
      if (existingConnection) {
        const pendingTask = pendingNewTasks.value.find(t => t.tempId === existingConnection.taskId)
        if (pendingTask) {
          pendingTask.destinationId = destinationId
          const destination = destinations.value?.find(d => d.id === destinationId)
          if (destination) {
            pendingTask.destinationName = destination.name
          }
        }
      }
    } else {
      // Track update for existing task
      const existingTask = tasks.value?.find(t => t.id === existingConnection.taskId)
      if (existingTask) {
        // Remove any previous pending update for this task
        pendingUpdates.value = pendingUpdates.value.filter(u => u.taskId !== existingConnection.taskId)

        // Add new pending update
        pendingUpdates.value.push({
          taskId: existingConnection.taskId,
          newDestinationId: destinationId,
          oldDestinationId: existingTask.destination.id,
          originName: existingTask.origin.name,
          destinationName: destinations.value?.find(destination => destination.id === destinationId)?.name ?? ""
        })

        const destination = destinations.value?.find(d => d.id === destinationId)
        if (destination) {
          existingTask.destination = destination
        }
      }
    }
  } else {
    // Create new connection (track as pending)
    // Check if we're in single pending task mode and already have a pending task
    if (!allowMultiplePendingTasks.value && pendingNewTasks.value.length > 0) {
      // Remove the old pending task before creating a new one
      pendingNewTasks.value = []
    }

    const tempId = `temp-${crypto.randomUUID()}`
    pendingNewTasks.value.push({
      originId: selectedOrigin.value,
      destinationId: destinationId,
      tempId: tempId,
      destinationName: destinations.value?.find(destination => destination.id === destinationId)?.name ?? "",
      originName: origins.value?.find(origin => origin.id === selectedOrigin.value)?.name ?? ""
    })
  }

  // Reset selections
  selectedOrigin.value = ""
  selectedDestination.value = ""
}

// Save all changes
const saveAllChanges = async () => {
  try {
    for (const update of pendingUpdates.value) {
      await useTasksAPI().UpdateTask(update.taskId, {
        destination_id: update.newDestinationId
      }, update.destinationName, update.originName)
    }
    pendingUpdates.value = []

    for (const newTask of pendingNewTasks.value) {
      const taskData: CreateTaskDTO = {
        origin_id: newTask.originId,
        destination_id: newTask.destinationId,
        schedule_expression: cronSchedule.value ?? "0 8 * * 1",
      }
      await useTasksAPI().CreateTask(taskData, newTask.destinationName, newTask.originName)
    }

    pendingUpdates.value = []
    pendingNewTasks.value = []

    await dashboardInfo()

    console.log('All changes saved successfully')
  } catch (error) {
    console.error('Error saving changes:', error)
  }
}

// Discard all changes
const discardChanges = () => {
  pendingUpdates.value = []
  pendingNewTasks.value = []
  selectedOrigin.value = ""
  selectedDestination.value = ""

  // Reload from server to reset UI
  dashboardInfo()
}

// Check if there are any pending changes
const hasPendingChanges = computed(() => {
  return pendingUpdates.value.length > 0 || pendingNewTasks.value.length > 0
})

// Open create modal
const openCreateModal = (type: 'origin' | 'destination') => {
  createEntityType.value = type
  newEntityName.value = ''
  newEntityDescription.value = ''
  newEntityImageUrl.value = ''
  showCreateModal.value = true
}

// Create new entity
const createEntity = async () => {
  try {
    const data = {
      name: newEntityName.value,
      description: newEntityDescription.value,
      image: newEntityImageUrl.value
    }
    if (createEntityType.value === 'origin') {
      await useOriginsAPI().CreateOrigin(data)
    } else {
      await useDestinationAPI().CreateDestination(data)
    }

    await dashboardInfo()
    showCreateModal.value = false
  } catch (error) {
    console.error('Error creating entity:', error)
  }
}
</script>

<template>
  <div>
    <div class="bg-[#F9F9FB]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Create buttons in top right -->
        <AnimatedConnectionArrow :connections="connections" ref="arrowsRef">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16">
            <div class="col-span-1">
              <h6 class="font-normal text-lg my-4">Origins</h6>
              <selectable-entity-card
                v-for="origin in origins"
                :key="origin.id"
                :model-value="selectedOrigin === origin.id || connectedOriginIds.includes(origin.id) ? origin.id : undefined"
                :ref="el => arrowsRef?.registerEntity((el as any)?.$el ?? el, origin.id)"
                :item="origin"
                entityType="origin"
                @edit="dashboardInfo"
                @delete="dashboardInfo"
                @click="handleOriginClick(origin.id)"
              />
            </div>
            <div class="col-span-1">
              <h6 class="font-normal text-lg my-4">Destinations</h6>
              <selectable-entity-card
                v-for="destination in destinations"
                :key="destination.id"
                entityType="destination"
                url="/destinations"
                @edit="dashboardInfo"
                @delete="dashboardInfo"
                :model-value="selectedDestination === destination.id || connectedDestinationIds.includes(destination.id) ? destination.id : undefined"
                :ref="el => arrowsRef?.registerEntity((el as any)?.$el ?? el, destination.id)"
                :item="destination"
                @click="handleDestinationClick(destination.id)"
              />
            </div>
          </div>
        </AnimatedConnectionArrow>

        <!-- Selection state banner -->
        <div v-if="selectedOrigin" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <p class="text-sm text-blue-800">
            <span class="font-semibold">{{ origins?.find(o => o.id === selectedOrigin)?.name }}</span> selected.
            Click a destination to {{ getOriginConnection(selectedOrigin) ? 'change' : 'create' }} connection.
          </p>
        </div>

        <!-- Pending changes banner -->
        <div v-if="hasPendingChanges" class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-semibold text-amber-900 mb-2">Unsaved Changes</p>
              <ul class="text-sm text-amber-800 space-y-1">
                <li v-if="pendingUpdates.length > 0">
                  {{ pendingUpdates.length }} task{{ pendingUpdates.length > 1 ? 's' : '' }} updated
                </li>
                <li v-if="pendingNewTasks.length > 0">
                  {{ pendingNewTasks.length }} new task{{ pendingNewTasks.length > 1 ? 's' : '' }} created
                </li>
              </ul>
            </div>
            <div v-if="pendingNewTasks.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Cron Schedule
              </label>
              <BaseInput
                v-model="cronSchedule"
                type="text"
                class="w-full border rounded-lg px-3 py-2"
                placeholder="Enter Cron Schedule"
              />
            </div>
            <div class="flex gap-2">
              <button
                @click="discardChanges"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Discard
              </button>
              <button
                @click="saveAllChanges"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
              >
                Save Changes
              </button>
            </div>
          </div>
        </div>

        <div class="fixed bottom-6 right-6 flex flex-col items-end space-y-2">
          <!-- Options -->
          <transition name="fade">
            <div v-if="open" class="flex flex-col items-end space-y-2 mb-2">
              <button
                class="bg-white text-gray-800 px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition"
                @click="openCreateModal('origin')"
              >
                + New Origin
              </button>
              <button
                class="bg-white text-gray-800 px-4 py-2 rounded-lg shadow-md hover:bg-gray-100 transition"
                @click="openCreateModal('destination')"
              >
                + New Destination
              </button>
            </div>
          </transition>

          <!-- Floating button -->
          <button
            @click="open = !open"
            class="bg-indigo-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-indigo-700 transition"
          >
            <span v-if="!open">+</span>
            <span v-else>✕</span>
          </button>
        </div>
      </div>
    </div>
    <div class="bg-[#FFFFFF]">
      <scrape-data />
    </div>

    <!-- Create Entity Modal -->
    <BasicModal v-model="showCreateModal" size="xl">
      <template #title>
        Create New <span class="capitalize">{{ createEntityType }}</span>
      </template>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Name
          </label>
          <BaseInput
            v-model="newEntityName"
            type="text"
            class="w-full border rounded-lg px-3 py-2"
            placeholder="Enter name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description
          </label>
          <BaseTextarea
            v-model="newEntityDescription"
            class="w-full rounded-lg"
            placeholder="Enter description"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Image URL
          </label>
          <BaseInput
            v-model="newEntityImageUrl"
            type="text"
            class="w-full border rounded-lg px-3 py-2"
            placeholder="Enter image URL"
          />
        </div>

        <!-- Image preview -->
        <div v-if="newEntityImageUrl" class="mt-4">
          <p class="text-sm text-gray-600 mb-2">Preview:</p>
          <img
            :src="newEntityImageUrl"
            alt="Preview"
            class="h-20 object-contain border rounded-md p-1"
          />
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-2">
          <button
            class="px-4 py-2 border cursor-pointer rounded-lg hover:bg-gray-50 transition-colors"
            @click="showCreateModal = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 bg-indigo-600 cursor-pointer text-white rounded-lg hover:bg-indigo-700 transition-colors"
            @click="createEntity"
          >
            Create
          </button>
        </div>
      </template>
    </BasicModal>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
