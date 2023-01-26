<template>
  <v-container fluid  >
    <v-card class="pa-4" prepend-icon="mdi-home" title="Login" >
      <v-card-text>

      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
</script>


<route lang="yaml">
  name: dashboard
  meta:
    requiresAuth: true
</route>