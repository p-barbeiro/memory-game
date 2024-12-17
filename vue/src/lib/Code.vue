<template>
  <div class="w-full overflow-x-auto">
    <pre class="language-json"><code>{{ JSON.stringify(props.var, null, 2) }}</code></pre>
  </div>
</template>

<script setup>
import { onMounted, onUpdated } from 'vue'
import Prism from 'prismjs'
import 'prismjs/components/prism-json.min.js'  // Import the JSON component

const props = defineProps({
  var: {
    type: Object,
    required: true
  }
})

onMounted(() => {
  Prism.highlightAll()  // Highlight syntax on mount
})

onUpdated(() => {
  Prism.highlightAll()  // Highlight syntax on update
})
</script>