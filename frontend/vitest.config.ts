import { fileURLToPath } from 'node:url'
import { mergeConfig, defineConfig, configDefaults } from 'vitest/config'
import viteConfig from './vite.config'

export default mergeConfig(
  viteConfig,
  defineConfig({
    test: {
      environment: 'jsdom',
      exclude: [...configDefaults.exclude, 'e2e/**'],
      root: fileURLToPath(new URL('./', import.meta.url)),
      setupFiles: ['src/test/setup.ts'],
      globals: true,
      coverage: {
        provider: 'v8',
        reporter: ['text', 'json', 'html'],
        exclude: [
          'node_modules/',
          'src/test/setup.ts',
          '**/*.d.ts',
          '**/*.config.*',
          'dist/',
        ],
      },
      // Mock console methods during tests
      silent: false,
      // Allow test files to be located anywhere
      includeSource: ['src/**/*.{js,ts,vue}'],
      // Test timeout
      testTimeout: 10000,
    },
    // In-source testing configuration
    define: {
      __TEST__: true,
    },
  }),
)
