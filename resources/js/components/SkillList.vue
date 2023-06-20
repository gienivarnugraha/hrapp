<template>
  <div>
    <v-list-subheader>{{ header }}</v-list-subheader>
    <v-list v-if="items.length > 0" :lines="lines" density="compact">
      <v-list-item class="mb-2" v-for="item in items" :key="item[itemValue]">
        <v-card variant="tonal" :color="isGradePassed(item.pivot.grade) ? 'success' : 'error'" class="pa-2 d-flex flex-no-wrap justify-space-between align-center ">
          <v-avatar :color="isGradePassed(item.pivot.grade) ? 'success' : 'error'" class="mr-2">
            {{ item.pivot.grade }}

          </v-avatar>

          <v-card-text class="pa-1">
            {{ item[itemTitle] }} 
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

const dateForHuman = (date) => moment(date).format("dddd, MMMM Do YYYY, h:mm a")

const hasComparator = (item) => props.compare && props.compare.find(comp => comp.id === item.id)

const isGradePassed = (grade) => parseInt(grade) >= 70

</script>