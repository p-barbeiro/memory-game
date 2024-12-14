<template>
  <tr class="h-10 border-b border-gray-100 hover:bg-slate-50">
    <td v-for="column in columns" class="px-2 text-center text-sm text-gray-600">
      <span v-if="column.key !== 'status'">
        {{ (model[column.key]?.[column.subkey] || model[column.key]) ?? (column.link && model.status === 'Pending' ? '' : '-') }}
        {{ column.append && model[column.key] ? column.append : '' }}
      </span>
      <div v-if="column.link && model.status === 'Pending'" @click="linkTo(column, model)" class="text-indigo-800 cursor-pointer ml-2 hover:underline">
        {{ column.linkText }}
      </div>
      <!-- //if column.key is status show a badge -->
      <Badge class="my-auto" v-if="column.key === 'status'" :variant="model[column.key] === 'Pending' ? 'indigo' : model[column.key] === 'Playing' ? 'yellow' : model[column.key] === 'Ended'? 'gray' : 'red'">
        {{ model[column.key] }}
      </Badge>
    </td>
  </tr>
</template>

<script setup>
import { useRouter } from 'vue-router';
import Badge from '../ui/badge/Badge.vue';

const props = defineProps({
  model: { type: Object, required: true },
  columns: { type: Array, required: true }
})

const router = useRouter()

const linkTo = (column, model) => {
  if (column.link) {
    const link = column.linkTo.replace(/:(\w+)/g, (_, key) => model[key])
    router.push(link)
  }
}

</script>
