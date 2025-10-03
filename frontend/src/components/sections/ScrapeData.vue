<script setup lang="ts">
import { onMounted, ref } from 'vue'
import BaseButton from '@/components/buttons/BaseButton.vue'
import UrlInput from '@/components/input/UrlInput.vue'
import { notify } from '@/helpers/notification.ts'

const url = ref<string>();
const isLoading = ref<boolean>();

onMounted(() => {
  url.value = 'https://www.amazon.com/s?ref=dp_bc_aui_C-RpcUTfB'
})

function handleScrape() {
  if (!url.value) return;
  isLoading.value = true;

  setTimeout(() => {
    notify(`Scraping: ${url.value}`, "success");
    isLoading.value = false;
  }, 1500);
}
</script>

<template>
  <div class="max-w-4xl mx-auto bg-gradient-to-br flex items-center justify-center px-4 py-8">
    <div class="w-full">
      <div class="p-8 md:p-12">
        <!-- Header -->
        <div class="text-center mb-20">
          <h2 class="text-[2.5rem] font-bold text-gray-900 mb-2">
            Didn't see your target website?
          </h2>
          <p class="text-gray-600 font-normal text-md">
            We can scrape any website on the internet. Try it out for free!
          </p>
        </div>

        <!-- URL Input Section -->
        <div class="mb-6">
          <label class="block text-sm font-regular text-gray-700 mb-2">
            URL to Scrape
          </label>
          <div class="flex gap-3 flex-col sm:flex-row items-center">
            <UrlInput
              v-model="url"
              placeholder="https://www.example.com/123-oak-av_LA/rent123"
            />
            <BaseButton
              @click="handleScrape"
              :disabled="isLoading"
              class="whitespace-nowrap"
            >
              <span v-if="isLoading" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    fill="none"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Scraping...
              </span>
              <span v-else>Scrape URL</span>
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
