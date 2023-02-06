<template>
  <div>
    <v-list-subheader>{{ header }}</v-list-subheader>
    <v-list v-if="items.length > 0" :lines="lines" density="compact" :item-value="itemValue" :items="items"
      :item-title="itemTitle">
      <template #item="{ title, value }">
        <v-list-item class="mb-2">
          <v-card variant="tonal" color="primary" class="pb-2">
            <v-card-text> {{ title }} </v-card-text>
          </v-card>
        </v-list-item>
      </template>
    </v-list>
    <v-list-item v-else class="mb-2"> None</v-list-item>
  </div>
</template>

<script setup>

const props = defineProps({
  items: Array,
  itemValue: {
    type: String,
    default: 'id',
  },
  header: String,
  type: String,
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