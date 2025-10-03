# Testing Guide

This project uses a comprehensive testing setup with **Vitest**, **Vue Testing Library**, and **TypeScript** to ensure code quality and reliability.

## Testing Stack

- **Vitest** - Fast unit test runner with native ESM support
- **@testing-library/vue** - Simple and complete testing utilities for Vue components
- **@testing-library/jest-dom** - Custom DOM matchers for better assertions
- **@testing-library/user-event** - Advanced simulation of browser interactions
- **MSW (Mock Service Worker)** - API mocking for integration tests
- **jsdom** - DOM implementation for Node.js

## Running Tests

### Basic Commands

```bash
# Run tests in watch mode (development)
npm run test:unit

# Run tests once (CI/production)
npm run test:unit:run

# Run tests with coverage report
npm run test:unit:coverage

# Run tests with UI interface
npm run test:unit:ui

# Run all tests (unit + e2e)
npm run test:all

# Run CI pipeline (lint + format + type-check + test with coverage)
npm run ci
```

### Test File Patterns

Tests are located in `__tests__` directories or files ending with `.spec.ts`:

```
src/
├── components/
│   ├── __tests__/
│   │   ├── AppLayout.spec.ts
│   │   └── CreatePostModal.spec.ts
│   └── AppLayout.vue
├── stores/
│   ├── __tests__/
│   │   └── counter.spec.ts
│   └── counter.ts
├── services/
│   ├── __tests__/
│   │   └── api.spec.ts
│   └── api.ts
└── composables/
    ├── __tests__/
    │   └── usePosts.spec.ts
    └── usePosts.ts
```

## Test Categories

### 1. Unit Tests

Test individual functions, composables, and stores in isolation.

```typescript
// Example: Testing a Pinia store
import { useCounterStore } from '../counter'

describe('Counter Store', () => {
  it('should increment count', () => {
    const counter = useCounterStore()
    counter.increment()
    expect(counter.count).toBe(1)
  })
})
```

### 2. Component Tests

Test Vue components with user interactions and state changes.

```typescript
// Example: Testing a Vue component
import { renderWithProviders } from '@/test/utils'
import AppLayout from '../AppLayout.vue'

describe('AppLayout', () => {
  it('renders navigation links', () => {
    renderWithProviders(AppLayout)
    expect(screen.getByRole('link', { name: /home/i })).toBeInTheDocument()
  })
})
```

### 3. Integration Tests

Test multiple components working together or API integrations.

```typescript
// Example: Testing a composable with API calls
import { usePosts } from '../usePosts'

describe('usePosts Integration', () => {
  it('should fetch posts from API', async () => {
    const { state, fetchPosts } = usePosts()
    await fetchPosts()
    expect(state.posts).toHaveLength(10)
  })
})
```

## Testing Utilities

### Custom Render Function

The `renderWithProviders` utility automatically sets up Vue Router and Pinia:

```typescript
import { renderWithProviders } from '@/test/utils'

// Render component with default providers
renderWithProviders(MyComponent)

// Render with custom route
renderWithProviders(MyComponent, {
  initialRoute: '/about'
})

// Render with custom stores
renderWithProviders(MyComponent, {
  piniaStores: [useCounterStore]
})
```

### Mock Data

Use predefined mock data for consistent testing:

```typescript
import { mockPosts, mockProducts, createMockUser } from '@/test/utils'

describe('My Test', () => {
  it('should work with mock data', () => {
    const user = createMockUser({ name: 'Test User' })
    expect(user.name).toBe('Test User')
  })
})
```

### API Mocking

Mock API calls for predictable testing:

```typescript
import { vi } from 'vitest'
import { postsService } from '@/services/postsService'

// Mock the entire service
vi.mock('@/services/postsService', () => ({
  postsService: {
    getPosts: vi.fn().mockResolvedValue(mockPosts),
  }
}))
```

## Best Practices

### 1. Test Structure

Use the **Arrange-Act-Assert** pattern:

```typescript
it('should create a new post', async () => {
  // Arrange
  const newPostData = { title: 'Test Post', body: 'Content' }
  postsService.createPost.mockResolvedValue({ id: 1, ...newPostData })

  // Act
  const result = await createPost(newPostData)

  // Assert
  expect(result).toEqual({ id: 1, ...newPostData })
  expect(postsService.createPost).toHaveBeenCalledWith(newPostData)
})
```

### 2. User-Centric Testing

Test how users interact with your app, not implementation details:

```typescript
// ✅ Good - Test user behavior
it('should submit form when user fills required fields', async () => {
  const user = userEvent.setup()
  renderWithProviders(CreatePostModal, { props: { show: true } })
  
  await user.type(screen.getByLabelText(/title/i), 'My Post')
  await user.type(screen.getByLabelText(/content/i), 'Content')
  await user.click(screen.getByRole('button', { name: /create/i }))
  
  expect(screen.getByText('Post created!')).toBeInTheDocument()
})

// ❌ Bad - Test implementation details
it('should call createPost method', () => {
  const wrapper = mount(CreatePostModal)
  wrapper.vm.createPost()
  expect(wrapper.vm.isSubmitting).toBe(true)
})
```

### 3. Async Testing

Properly handle asynchronous operations:

```typescript
it('should handle loading states', async () => {
  // Mock delayed response
  postsService.getPosts.mockImplementation(() => 
    new Promise(resolve => setTimeout(() => resolve(mockPosts), 100))
  )

  const { fetchPosts } = usePosts()
  
  // Start async operation
  const promise = fetchPosts()
  
  // Check loading state
  expect(screen.getByText('Loading...')).toBeInTheDocument()
  
  // Wait for completion
  await promise
  await waitFor(() => {
    expect(screen.queryByText('Loading...')).not.toBeInTheDocument()
  })
})
```

### 4. Error Scenarios

Always test error cases:

```typescript
it('should handle API errors gracefully', async () => {
  postsService.getPosts.mockRejectedValue(new Error('Network error'))
  
  const { state, fetchPosts } = usePosts()
  await fetchPosts()
  
  expect(state.error).toBe('Network error')
  expect(screen.getByText(/error occurred/i)).toBeInTheDocument()
})
```

### 5. Accessibility Testing

Ensure your components are accessible:

```typescript
it('should have proper accessibility attributes', () => {
  renderWithProviders(CreatePostModal, { props: { show: true } })
  
  expect(screen.getByLabelText(/title/i)).toBeRequired()
  expect(screen.getByRole('button', { name: /submit/i })).toBeInTheDocument()
})
```

## Coverage Goals

- **Statements**: 90%+
- **Branches**: 85%+
- **Functions**: 90%+
- **Lines**: 90%+

Run `npm run test:unit:coverage` to generate detailed coverage reports.

## Debugging Tests

### VS Code Integration

Add this configuration to `.vscode/launch.json`:

```json
{
  "type": "node",
  "request": "launch",
  "name": "Debug Vitest Tests",
  "program": "${workspaceFolder}/node_modules/vitest/vitest.mjs",
  "args": ["run", "--reporter=verbose"],
  "console": "integratedTerminal",
  "internalConsoleOptions": "neverOpen"
}
```

### Browser Debugging

Use Vitest UI for interactive debugging:

```bash
npm run test:unit:ui
```

## Continuous Integration

The `npm run ci` command runs all quality checks:

1. **Linting** - Code style and potential issues
2. **Formatting** - Code formatting consistency
3. **Type Checking** - TypeScript type safety
4. **Testing** - Unit tests with coverage

Use this in your CI/CD pipeline for comprehensive quality assurance.

## Writing New Tests

### Component Test Template

```typescript
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { screen } from '@testing-library/vue'
import userEvent from '@testing-library/user-event'
import { renderWithProviders } from '@/test/utils'
import MyComponent from '../MyComponent.vue'

describe('MyComponent', () => {
  const user = userEvent.setup()

  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should render correctly', () => {
    renderWithProviders(MyComponent)
    expect(screen.getByRole('button')).toBeInTheDocument()
  })

  it('should handle user interactions', async () => {
    renderWithProviders(MyComponent)
    await user.click(screen.getByRole('button'))
    expect(screen.getByText('Clicked!')).toBeInTheDocument()
  })
})
```

### Composable Test Template

```typescript
import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useMyComposable } from '../useMyComposable'

describe('useMyComposable', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('should initialize with correct defaults', () => {
    const { state } = useMyComposable()
    expect(state.value).toBe('default')
  })

  it('should handle state changes', async () => {
    const { state, updateState } = useMyComposable()
    await updateState('new value')
    expect(state.value).toBe('new value')
  })
})
```

## Resources

- [Vitest Documentation](https://vitest.dev/)
- [Vue Testing Library](https://testing-library.com/docs/vue-testing-library/intro)
- [Vue Test Utils](https://vue-test-utils.vuejs.org/)
- [Testing Best Practices](https://kentcdodds.com/blog/common-mistakes-with-react-testing-library)