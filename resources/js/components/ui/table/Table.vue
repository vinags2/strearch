<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div class="relative w-auto overflow-auto">
    <table :class="cn('w-auto caption-bottom text-sm', props.class)">
      <slot />
    </table>
  </div>
</template>
