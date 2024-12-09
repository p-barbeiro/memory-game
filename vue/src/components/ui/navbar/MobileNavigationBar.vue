<template>
  <MobileMenuItem v-for="menu in menus" :name="menu.name" :link="menu.link" :dropdownList="menu.dropdownList" />
</template>

<script setup>
import MobileMenuItem from './MobileMenuItem.vue';

const props = defineProps({
  menus: { type: Array, required: true }
})

</script>
