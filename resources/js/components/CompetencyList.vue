<template>
  <div>
    <v-list-subheader>{{ header }}</v-list-subheader>
    <v-list v-if="items.length > 0" :lines="lines" density="compact">
      <v-list-item class="mb-2" v-for="item in items" :key="item[itemValue]">
        <v-card variant="tonal" :color="hasComparator(item) ? 'success' : 'primary'" class="pb-2">
          <v-card-text>
            {{ item[itemTitle] }} 
            <v-icon v-if="hasComparator(item)" :icon="hasComparator(item) ? 'mdi-check' : 'mdi-close'"></v-icon>
            <p v-if="item.start_date">
              <v-icon>mdi-clock</v-icon>
               Training Start At: {{ dateForHuman(item.start_date) }} </p>
          </v-card-text>
        </v-card>
      </v-list-item>
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
  compare: Array,
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

const dateForHuman = (date) => moment(date).format("dddd, MMMM Do YYYY, h:mm:ss a")

const hasComparator = (item) => props.compare && props.compare.find(comp => comp.id === item.id)

</script>