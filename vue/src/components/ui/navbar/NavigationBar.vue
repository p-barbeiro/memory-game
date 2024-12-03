<template>
  <ul class="pr-32 xl:flex hidden items-center gap-2 h-full">
    <MenuItem v-for="menu in menus" :name="menu.name" :link="menu.link" :dropdownList="menu.dropdownList" />
  </ul>
</template>

<script setup>
import MenuItem from './MenuItem.vue';

const props = defineProps({
	menus: { type: Array, required: true }
})

</script>
