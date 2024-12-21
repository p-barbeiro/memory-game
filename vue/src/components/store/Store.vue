<template>
  <body class="place-self-center overflow-y-hidden w-full">
    <h1 class="text-2xl font-semibold mb-5 text-center">Game Store</h1>
    <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 lg:gap-y-3 lg:gap-x-3 sm:gap-y-10 sm:gap-x-6 gap-y-6">
      <CardStore :amount="10" img="/coins/1.png" />
      <CardStore :amount="20" img="/coins/2.png" />
      <CardStore :amount="30" img="/coins/3.png" />
      <CardStore :amount="50" img="/coins/4.png" />
      <CardStore :amount="100" img="/coins/5.png" />
      <CardStore custom="Customize" img="/coins/6.png" />
    </div>
    <hr class="my-5" />
    <h1 class="text-2xl font-semibold mb-5 text-center">Deck Store</h1>
    <div class="grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 lg:gap-y-3 lg:gap-x-3 sm:gap-y-10 sm:gap-x-6 gap-y-6">
      <DeckCardStore item="Animals" :price="0" img="/decks/Animals/" default />
      <DeckCardStore item="Emoji" :price="10" img="/decks/Emoji/" />
      <DeckCardStore item="Landscape" :price="10" img="/decks/Landscape/" />
      <DeckCardStore item="Food" :price="15" img="/decks/Food/" />
      <DeckCardStore item="Travel" :price="15" img="/decks/Travel/" />
      <DeckCardStore item="Logos" :price="15" img="/decks/Logos/" />
      <DeckCardStore item="Musical" :price="20" img="/decks/Musical/" />
      <DeckCardStore item="Basketball" :price="20" img="/decks/Basketball/" />
      <DeckCardStore item="Football" :price="20" img="/decks/Football/" />
    </div>
  </body>
  {{ store }}
</template>

<script setup>
import CardStore from './CardStore.vue'
import DeckCardStore from './DeckCardStore.vue'
</script>
