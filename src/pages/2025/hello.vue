<template>
  <h1>Hello VUE!!!</h1>

  <p v-for="a in data.list">{{a}}</p>

</template>


<script setup lang="ts">
const data = {
  list: ['1', '2', '3'],
}
</script>