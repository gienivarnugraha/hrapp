<template>
  <v-list :lines="lines" density="compact" :items="items" :item-value="itemValue" :item-title="itemTitle">
    <template v-for="(item,index) in items" :key="item.id">
      <v-list-subheader v-if="item.type === 'subheader'" color="primary"> {{ item.name }} </v-list-subheader>
      <slot v-else-if="item.type==='item'" name="item" :index="index" :item="item"></slot>

      <v-divider  class="my-2" v-else-if="item.type==='divider'"></v-divider>
    </template>
  </v-list>
</template>

<script setup>

const props = defineProps({
  items: Array,
  itemValue: {
    type: String,
    default: 'id',
  },
  itemTitle: {
    type: String,
    default: 'name',
  },
  lines: {
    type: String,
    default: 'two',
  },
})
</script>