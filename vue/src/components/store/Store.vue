<template>
  <body class="place-self-center overflow-y-hidden w-full">
    <h1 class="text-2xl font-semibold mb-5 text-center">Game Store</h1>
    <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 lg:gap-y-3 lg:gap-x-3 sm:gap-y-10 sm:gap-x-6 gap-y-6">
      <CardStore :amount=10 img="/src/assets/coins/1.png"/>
      <CardStore :amount=20 img="/src/assets/coins/2.png"/>
      <CardStore :amount=30 img="/src/assets/coins/3.png"/>
      <CardStore :amount=50 img="/src/assets/coins/4.png"/>
      <CardStore :amount=100 img="/src/assets/coins/5.png"/>
      <CardStore custom="Customize" img="/src/assets/coins/6.png"/>
    </div>
  </body>
</template>

<script setup>
import CardStore from './CardStore.vue';

</script>

