<template>
  <div>
    <v-list-subheader>{{ header }}</v-list-subheader>
    <v-list v-if="items.length > 0" :lines="lines" density="compact">
      <v-list-item class="mb-2" v-for="item in items" :key="item[itemValue]">
        <v-card variant="tonal" :color="isPassed(item).color" class="pa-2 d-flex flex-no-wrap justify-space-between align-center ">
          <v-avatar :color="isPassed(item).color" size="32" class="mr-2">
            <v-icon :icon="isPassed(item).icon"></v-icon>
          </v-avatar>

          <v-card-text class="pa-1">
            {{ item[itemTitle] }} 
            <p v-if="item.start_date && !isPassed(item).passed ">
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

const isPassed = item => {
  let trained = toRaw(hasComparator(item))

  let color, icon, passed

  if (hasComparator(item) && trained.pivot.grade <=70 ){
    color = 'warning'
    icon = 'mdi-alert'
    passed = false
  } else if (hasComparator(item) && trained.pivot.grade >=70){
    color = 'success'
    icon = 'mdi-check'
    passed = true
  } else {
    color = 'error'
    icon = 'mdi-close'
    passed = false
  }
  
  return { color, icon, passed }
}

</script>