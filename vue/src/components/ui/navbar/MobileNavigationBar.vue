<template>
  <MobileMenuItem v-for="menu in menus" v-show="menu.visible" :name="menu.name" :link="menu.link" :dropdownList="menu.dropdownList" />
</template>

<script setup>
import MobileMenuItem from './MobileMenuItem.vue';

const props = defineProps({
  menus: { type: Array, required: true }
})

</script>
