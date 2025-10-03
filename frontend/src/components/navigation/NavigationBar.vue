<script setup lang="ts">
import { ref, watch } from 'vue'

const isMenuOpen = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

watch(isMenuOpen, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
})

const links = [
  { to: '#', label: 'Products' },
  { to: '#', label: 'Pricing' },
  { to: '#', label: 'Docs' },
  { to: '#', label: 'Blog' },
]
</script>

<template>
  <nav role="navigation" class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center h-16 gap-6">
        <!-- Logo -->
        <div class="flex items-center gap-2">
          <div class="w-5 h-5 bg-[#0A415C] rounded-full"></div>
          <span class="text-xl text-gray-900 font-medium">navlogo</span>
        </div>

        <div class="hidden md:flex items-center gap-x-2">
          <NuxtLink
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="px-4 py-2 text-sm font-normal cursor-pointer transition-colors text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50"
          >
            {{ link.label }}
          </NuxtLink>
        </div>

        <!-- Desktop Auth Buttons -->
        <div class="hidden md:flex items-center gap-3 ml-auto">
          <NuxtLink
            to="#"
            class="px-4 py-2 cursor-pointer text-sm font-normal transition-colors text-gray-600 hover:text-gray-900"
          >
            Sign In
          </NuxtLink>
          <button class="px-4 cursor-pointer py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            Start Free Trial
          </button>
        </div>

        <!-- Mobile Hamburger Button -->
        <button
          @click="toggleMenu"
          class="md:hidden p-2 cursor-pointer rounded-lg text-gray-600 hover:bg-gray-100 transition-colors ml-auto"
          aria-label="Toggle menu"
          :aria-expanded="isMenuOpen"
        >
          <!-- Close Icon -->
          <svg
            v-if="isMenuOpen"
            class="w-6 h-6"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
          <!-- Hamburger Icon -->
          <svg
            v-else
            class="w-6 h-6"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <Transition
        enter-active-class="transition-all duration-300 ease-in-out"
        enter-from-class="max-h-0 opacity-0"
        enter-to-class="max-h-96 opacity-100"
        leave-active-class="transition-all duration-300 ease-in-out"
        leave-from-class="max-h-96 opacity-100"
        leave-to-class="max-h-0 opacity-0"
      >
        <div v-if="isMenuOpen" class="md:hidden overflow-hidden">
          <div class="py-4 space-y-1 border-t border-gray-100">
            <NuxtLink
              v-for="link in links"
              :key="link.to"
              :to="link.to"
              @click="closeMenu"
              class="block cursor-pointer px-4 py-3 text-sm font-normal text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
            >
              {{ link.label }}
            </NuxtLink>

            <!-- Mobile Auth Buttons -->
            <div class="pt-4 cursor-pointer space-y-2 border-t border-gray-100">
              <NuxtLink
                to="#"
                @click="closeMenu"
                class="block px-4 py-3 text-sm font-normal text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors text-center"
              >
                Sign In
              </NuxtLink>
              <button
                @click="closeMenu"
                class="w-full cursor-pointer px-4 py-3 font-semibold bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
              >
                Start Free Trial
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </nav>
</template>
