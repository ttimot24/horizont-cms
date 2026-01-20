import { defineConfig } from 'vitest/config'

export default defineConfig({
  test: {
    include: ['resources/vue/**/*.(test|spec).{ts,js}'],
    globals: true,
    reporters: ['default', 'junit', 'html'],
    outputFile: {
      junit: 'reports/junit.xml',
      html: 'reports/html/report.html',
    },
    coverage: {
        provider: 'v8',
        reporter: ['text', 'html', 'lcov'],
        reportsDirectory: 'reports/coverage',
    },
  },
})