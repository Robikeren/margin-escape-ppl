<script setup>
import StokOpnameCard from '../../components/StokOpnameCard.vue'
</script>

<template>
  <div class="input-stok-page">
    <h1>Input Stok Opname</h1>
    <p class="subtitle">Catat stok kopi hari ini</p>

    <div class="cards-row">
      <StokOpnameCard :jenis-kopi-id="1" nama-kopi="Robusta" />
      <StokOpnameCard :jenis-kopi-id="2" nama-kopi="Arabika" />
    </div>
  </div>
</template>

<style scoped>
.input-stok-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.cards-row {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}
</style>