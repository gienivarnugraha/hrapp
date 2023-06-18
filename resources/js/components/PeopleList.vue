<template>
  <v-card variant="outlined" class="pt-4 mt-2">
    <v-list-item class="mb-2" v-for="(position, key) in items" :key="key" :title="key">
      <v-list lines="two" density="compact" v-if="position.length > 0">
        <v-list-item v-for="(people, index) in position" :key="people.id" :title="people.name" :subtitle="people.nik">
          <template v-slot:prepend>
            <v-avatar color="grey-lighten-1">
              <v-icon color="white">mdi-account</v-icon>
            </v-avatar>
          </template>

          <template v-slot:append>
            <v-btn color="grey-lighten-1" icon="mdi-information" variant="text"></v-btn>
          </template>
        </v-list-item>
      </v-list>
      <v-list lines="two" v-else density="compact">
        <v-list-item class="mb-2">
          <template v-slot:append>
            <v-menu :key="`${key}-${jobTitleId}`"   :close-on-content-click="false" location="end">
              <template v-slot:activator="{ props }">
                <v-btn :size="xs ? 'x-small' : 'small'" color="info" class="w-100" :rounded="false" v-bind="props"
                  prepend-icon="mdi-plus" variant="flat" @click="addDialog(key)"> Add Member
                </v-btn>
              </template>

              <v-card min-width="300" title="Add People" :loading="loading" :disabled="loading">
                <v-card-text>
                  <v-select :items="peoples" item-title="name" item-value="id" v-model="selectedPeople" label="Select People to Assign Position"
                    density="compact"></v-select>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                  <v-btn class="d-block" color="primary" variant="flat" @click="generateSchedule()">
                    Generate Schedule
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>

          </template>
          <v-card class="pa-2" color="error" variant="tonal" :rounded="false">
            None
          </v-card>
        </v-list-item>
      </v-list>
    </v-list-item>

  </v-card>
</template>

<script setup>
import { useDisplay } from 'vuetify';

const { xs } = useDisplay()

const props = defineProps({
  items: Object,
  jobTitleId: Number,
})

const selectedPosition = ref('')
const peoples = ref([])
const loading = ref(false)
const selectedPeople = ref()
const positions = [
  { title: "Junior", value: 1 },
  { title: "Medior", value: 2 },
  { title: "Senior", value: 3 },
]

const addDialog = (position) => {
  selectedPosition.value = positions.find(pos => position === pos.title).value
}

const unwatchAdd = watch(
  () => selectedPosition.value,
  (value) => {
    if (value) {
      console.log(value);
      console.log('opened');
      getPeopleByJobTitle(value)
    }
  },
  { immediate: true, flush: 'sync' }
)

onUnmounted(() => {
  unwatchAdd()
})



const getPeopleByJobTitle = async (position) => {
  const { data } = await axios.get(`/api/job-title/${props.jobTitleId}/peoples/${position}`)

  peoples.value = data

}

const generateSchedule = async (people) => {
  loading.value = true
  try {
    console.log(selectedPeople);
    const { data: events } = await axios.post('/api/events/generate', {
      people_id: selectedPeople.value,
      next_position: selectedPosition.value
    })

  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }

};

</script>
