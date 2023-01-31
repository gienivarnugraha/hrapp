<template>
  <v-container fluid>
    <v-card class="pa-4" :loading="loading" :disabled="loading">
      <v-card-title class="px-8 my-4">
        <v-row align="center">
          <v-col cols="10">
            <v-icon class="mr-4">
              mdi-account-group
            </v-icon>
            Peoples

          </v-col>
          <v-col cols="2" class="text-right">
            <v-btn size="small" class="w-75" :rounded="false" prepend-icon="mdi-plus" variant="flat">Add</v-btn>
          </v-col>
        </v-row>
      </v-card-title>

      <v-card-text>
        <v-table fixed-header height="75vh">
          <thead>
            <tr>
              <th v-for="header in headers" :key="header.title" :class="header.class">
                {{ header.title }}
              </th>
            </tr>
          </thead>
          <tbody>
            <template v-for="item in items" :key="item.id">
              <tr>
                <td>{{ item.name }}</td>
                <td class="text-center">{{ item.nik }}</td>
                <td class="text-center">{{ item.org }}</td>
                <td>{{ item.job_title.name }}</td>
                <td class="text-center">{{ item.position }}</td>
                <td class="text-center"> <v-icon @click="onExpand(item)"> {{
                  isExpanded(item) ?'mdi-menu-up'
                    : 'mdi-menu-down'
                }}</v-icon> </td>
              </tr>
              <tr v-if="isExpanded(item)">
                <v-slide-y-transition>

                  <td :colspan="headers.length" class="py-4">
                    <v-card variant="outlined" color="indigo">
                      <v-card-title class="px-8 my-4">
                        <v-row align="center">
                          <v-col cols="10">
                            <v-icon class="mr-4">
                              mdi-account
                            </v-icon>
                            {{ item.name }}


                          </v-col>
                          <v-col cols="2" class="text-right">
                            <v-btn size="small" class="w-100" color="error" :rounded="false" prepend-icon="mdi-delete"
                              variant="flat">delete</v-btn>
                          </v-col>
                        </v-row>
                      </v-card-title>



                      <v-card-text>
                        <v-row>
                          <v-col cols="6">
                            <v-card-subtitle> Competencies: </v-card-subtitle>
                            <competency-list :items="item.skills">
                              <template #item="{ item: people }">
                                <v-list-item>
                                  <p>
                                    <v-icon icon="mdi-circle-medium"></v-icon>
                                    {{ people.name }}
                                  </p>
                                </v-list-item>
                              </template>
                            </competency-list>
                          </v-col>
                          <v-col cols="6">
                            <v-card-subtitle class="mb-4" v-if="item['next_position']"> Required Competencies to Promote
                              into {{
                              item['next_position'] }} </v-card-subtitle>

                            <v-row class=" px-4">
                              <v-col :cols="item['next_position'] ? 6 : 12">
                                <v-select class="w-100" :items="positions"
                                  v-model="expanded[findIndex(item)]['next_position']" label="Promote to"
                                  density="comfortable"></v-select>

                              </v-col>
                              <v-col cols="6" v-if="item['next_position']">
                                <v-btn stacked size="x-small" class="w-100" color="primary" :rounded="false"
                                  prepend-icon="mdi-sync" variant="flat" @click="generateSchedule(item)">Generate
                                  Schedule</v-btn>

                              </v-col>
                            </v-row>



                            <v-slide-y-transition v-if="item['next_position']">
                              <competency-list :items="item.required_skills[item['next_position']]">
                                <template #item="{ item: skill, index }">
                                  <v-list-item>
                                    <v-card variant="tonal" class="pb-2">
                                      <v-card-text> {{ skill.name }} </v-card-text>
                                      <v-card-subtitle> <v-icon> mdi-clock </v-icon> Training start at: {{ skill.start_date }} </v-card-subtitle>
                                    </v-card>
                                    <!--  <p :class="{
                                      'text-success': item.skills.find((i) => i.id === skill.id),
                                      'text-error': !item.skills.find((i) => i.id === skill.id),
                                      'text-info': skill.start_date
                                    }">
                                      <v-icon
                                        :icon="item.skills.find((i) => i.id === skill.id) ? 'mdi-check' : 'mdi-close'">
                                      </v-icon>

                                      <span>
                                        -
                                        {{ skill.name }}

                                      </span>

                                    </p>
                                    <p v-if="skill.start_date"> Training Start at: {{
                                      skill.start_date
                                    }}
                                    </p>  -->
                                  </v-list-item>
                                </template>
                              </competency-list>
                            </v-slide-y-transition>
                          </v-col>

                        </v-row>





                      </v-card-text>

                    </v-card>

                  </td>
                </v-slide-y-transition>
              </tr>
            </template>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  { title: "Name", class: "text-left", },
  { title: "NIK", class: "text-center", },
  { title: "ORG", class: "text-center", },
  { title: "Job Title", class: "text-center", },
  { title: "Position", class: "text-center", },
  { title: "Action", class: "text-center", },
]

const positions = [
  { title: "Junior", value: 'junior' },
  { title: "Medior", value: 'medior' },
  { title: "Senior", value: 'senior' },
]

const items = ref([])

const expanded = ref([])

const isExpanded = (item) => expanded.value.includes(item)

const findIndex = (item) => expanded.value.findIndex(exp => exp.id == item.id)

const onExpand = (item) => {
  const index = findIndex(item)

  if (index < 0) {
    expanded.value.push(item)
  } else {
    expanded.value.splice(index, 1)
  }
}

const generateSchedule = async (people) => {
  try {
    const { data: events } = await axios.post('/api/events/generate', {
      people_id: people.id,
      next_position: people.next_position
    })

    let findPeople = expanded.value.find(ppl => ppl.id === people.id)

    events.forEach(event => {
      let key = Object.keys(event)[0]

      let competency = findPeople.required_skills[people.next_position].find(comp => comp.id === key)

      competency['start_date'] = moment(event[key]).format('YYYY-MM-DD HH:mm')

    })



  } catch (error) {
    console.log(error)
  }

};

const loading = ref(true)

onMounted(async () => {
  try {
    const { data: peoples } = await axios.get('/api/peoples')

    items.value = peoples

  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
})
</script>

<route lang="yaml">
  name: peoples
  meta:
    requiresAuth: true
</route>