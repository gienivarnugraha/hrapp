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
            <v-menu v-model="addMenu" :close-on-content-click="false" location="end">
              <template v-slot:activator="{ props }">

                <v-btn size="small" class="w-100" :rounded="false" prepend-icon="mdi-plus" variant="flat"
                  v-bind="props">Add People</v-btn>
              </template>

              <v-card class="pa-4" min-width="300" title="Add People" :disabled="addModel.loading"
                :loading="addModel.loading">
                <v-card-text>

                  <v-text-field v-model="addModel.name" label="Name"> </v-text-field>

                  <v-text-field v-model="addModel.nik" label="NIK"> </v-text-field>

                  <v-text-field v-model="addModel.org" label="Organization Code"> </v-text-field>

                  <v-autocomplete label="Position" v-model="addModel.position" :items="positions"></v-autocomplete>

                  <v-autocomplete label="Job Title" v-model="addModel.job_title_id" :items="jobTitles"  item-value="id" item-title="name"></v-autocomplete>

                  <v-card-subtitle>Skills:</v-card-subtitle>

                  <v-autocomplete label="Select Hard Skills" v-model="addModel.skills['hard']"
                    :items="competencies['hard']" item-value="id" item-title="name" chips multiple></v-autocomplete>
                  <v-autocomplete label="Select Soft Skills" v-model="addModel.skills['soft']"
                    :items="competencies['soft']" item-value="id" item-title="name" chips multiple></v-autocomplete>
                  <v-autocomplete label="Select DOA Skills" v-model="addModel.skills['doa']"
                    :items="competencies['doa']" item-value="id" item-title="name" chips multiple></v-autocomplete>

                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>

                  <v-btn variant="text" color="error" @click="addMenu = false">
                    Cancel
                  </v-btn>
                  <v-btn color="primary" variant="flat" @click="onSave(addModel)">
                    Save
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>
          </v-col>
        </v-row>
      </v-card-title>

      <v-card-text>
        <Loading v-if="loading"></Loading>
        <v-table fixed-header height="75vh" v-if="!loading">
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
                <td class="text-center"> <v-btn @click="onExpand(item)" variant="text" size="small"
                    :loading="item.loadingComptencies" :icon="item.expanded ? 'mdi-menu-up'
                    : 'mdi-menu-down'"></v-btn> </td>
              </tr>
              <v-slide-y-transition>
                <tr v-if="item.expanded">
                  <td :colspan="headers.length" class="py-4">
                    <v-card variant="outlined" color="indigo" class="pa-4">
                      <v-card-title class="px-8 my-4">
                        <v-row align="center">
                          <v-col cols="8">
                            <v-icon class="mr-4">
                              mdi-account
                            </v-icon>
                            {{ item.name }}


                          </v-col>
                          <v-col cols="2" class="text-right">
                            <v-menu :key="item.id" v-model="item.edit" :close-on-content-click="false" location="end">
                              <template v-slot:activator="{ props }">

                                <v-btn size="small" :key="item.id" class="w-100" :rounded="false"
                                  prepend-icon="mdi-plus" variant="flat" v-bind="props">Add Skill</v-btn>
                              </template>

                              <v-card class="pa-4" min-width="300" :title="`Add Skills to ${item.name}`"
                                :disabled="item.loadCompetencies" :loading="item.loadCompetencies">
                                <v-card-text>

                                  <v-select label="Position" v-model="item.position" :items="positions"></v-select>

                                  <v-select label="Select Hard Skills" return-object v-model="item.skills['hard']"
                                    :items="competencies['hard']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>
                                  <v-select label="Select Soft Skills" return-object v-model="item.skills['soft']"
                                    :items="competencies['soft']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>
                                  <v-select label="Select DOA Skills" return-object v-model="item.skills['doa']"
                                    :items="competencies['doa']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>

                                </v-card-text>
                                <v-card-actions>
                                  <v-spacer></v-spacer>

                                  <v-btn variant="text" color="error" @click="item.edit = false">
                                    Cancel
                                  </v-btn>
                                  <v-btn color="primary" variant="flat" @click="onSave(item)">
                                    Save
                                  </v-btn>
                                </v-card-actions>
                              </v-card>
                            </v-menu>

                          </v-col>
                          <v-col cols="2" class="text-right">
                            <v-btn size="small" class="w-100" color="error" :rounded="false" prepend-icon="mdi-delete"
                              variant="flat" @click="onDelete(item.id)">delete</v-btn>
                          </v-col>
                        </v-row>
                      </v-card-title>



                      <v-card-text>
                        <v-row>
                          <v-col cols="6">
                            <v-card-subtitle> Competencies: </v-card-subtitle>
                            <competency-list :items="item.skills['hard']" header="Hard Skills"></competency-list>
                            <competency-list :items="item.skills['soft']" header="Soft Skills"></competency-list>
                            <competency-list :items="item.skills['doa']" header="DOA Skills"></competency-list>

                          </v-col>

                          <v-col cols="6">
                            <v-card-subtitle class="mb-4" v-if="item.next_position"> Required
                              Competencies to Promote
                              into {{
                                item.next_position
                              }} </v-card-subtitle>

                            <v-row class=" px-4">
                              <v-col :cols="item.next_position ? 6 : 12">
                                <v-select class="w-100" :items="positions" v-model="item.next_position"
                                  label="Promote to" density="comfortable"></v-select>

                              </v-col>
                              <v-col cols="6" v-if="item.next_position">
                                <v-btn stacked size="x-small" class="w-100" color="primary" :rounded="false"
                                  prepend-icon="mdi-sync" variant="flat" @click="generateSchedule(item)">Generate
                                  Schedule</v-btn>

                              </v-col>
                            </v-row>

                            <v-slide-y-transition>
                              <div v-if="item.next_position">
                                <competency-list :items="item.required_skills[item.next_position]['hard']"
                                  :compare="item.skills['hard']" header="Hard Skills"></competency-list>
                                <competency-list :items="item.required_skills[item.next_position]['soft']"
                                  :compare="item.skills['soft']" header="Soft Skills"></competency-list>
                                <competency-list :items="item.required_skills[item.next_position]['doa']"
                                  :compare="item.skills['doa']" header="DOA Skills"></competency-list>

                              </div>
                            </v-slide-y-transition>
                          </v-col>

                        </v-row>
                      </v-card-text>

                    </v-card>

                  </td>
                </tr>
              </v-slide-y-transition>
            </template>
          </tbody>
        </v-table>

        <v-divider class="my-2"></v-divider>

        <v-pagination v-model="pagination.page" :length="pagination.totalPage" @update:modelValue="getPeoples($event)"
          @next="getPeoples($event)" @prev="getPeoples($event)"></v-pagination>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { nextTick } from 'vue';

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

let pagination = ref({
  page: 1,
  totalPage: 1,
})

let items = ref([])

let competencies = ref([])

let jobTitles = ref([])

let expanded = ref([])

let loading = ref(true)

let addMenu = ref(false)

let addModel = ref({
  name: '',
  nik: '',
  org: '',
  job_title_id: null,
  position: null,
  loading: false,
  skills: { hard: [], soft: [], doa: [] },

})

const compare = (a, b) => a.id === b.id

const findIndex = (item) => expanded.value.findIndex(exp => exp.id == item.id)

const showCompetencies = async (item) => {
  try {
    const { data: people } = await axios.get(`/api/peoples/${item.id}`)

    item.skills = people.skills

    item.required_skills = people.required_skills

    item.next_position = ''

    item.expanded = true

    item.edit = false

    return item

  } catch (error) {
    console.error(error);
  } finally {

  }
}

const onExpand = async (item) => {
  const index = findIndex(item)

  if (index < 0) {
    try {
      item.loadCompetencies = true

      const people = await showCompetencies(item)

      expanded.value.push(people)

    } catch (error) {
      console.error(error);
    } finally {
      item.loadCompetencies = false
    }

  } else {
    nextTick(() => {
      expanded.value.splice(index, 1)
      item.expanded = false
    })
  }
}

const generateSchedule = async (people) => {
  try {
    const { data: events } = await axios.post('/api/events/generate', {
      people_id: people.id,
      next_position: people.next_position
    })

    let findPeople = expanded.value.find(ppl => ppl.id === people.id)

    console.log(findPeople);

    events.forEach(event => {
      let compId = Object.keys(event)[0];

      ['hard','soft','doa'].forEach((type)=> {
        let competency = findPeople.required_skills[people.next_position][type].find(comp => comp.id === parseInt(compId))

        if(competency) {
          competency['start_date'] = moment(event[compId]).format('YYYY-MM-DD HH:mm')
          console.log(competency);
        }
      })
    })

  } catch (error) {
    console.error(error)
  }

};

const getPeoples = async (page = 1) => {
  loading.value = true

  try {
    const { data: peoples } = await axios.get(`/api/peoples?page=${page}`)

    items.value = peoples.data

    pagination.value.page = peoples.current_page
    pagination.value.totalPage = peoples.total_page

  } catch (error) {
    console.error(error);
  } finally {
    nextTick(() => loading.value = false
    )
  }
}

const getCompetencies = async () => {
  try {
    const { data: competency } = await axios.get('/api/competencies/types')
    competencies.value = competency
  } catch (error) {
    console.error(error);
  }
}
const getJobs = async () => {
  try {
    const { data: jobs } = await axios.get('/api/job-title/all')
    jobTitles.value = jobs[0]
  } catch (error) {
    console.error(error);
  }
}


const onSave = async (item) => {
  item.loadCompetencies = true

  try {
    if (item && item.edit) {
      const { data: people } = await axios.put(`/api/peoples/${item.id}`, item)

      const index = findIndex(item.id)

      people['expanded'] = false

      people['edit'] = false

      items.value.splice(index, 1, people)

    } else {
      const { data: people } = await axios.post(`/api/peoples`, addModel.value)

      items.value.push(people)

      addMenu.value = false
    }

  } catch (error) {
    console.error(error);
  } finally {
    item.loadCompetencies = false;
  }
}

const onDelete = async (id) => {
  loading.value = true

  try {
    await axios.delete(`/api/peoples/${id}`)

    const expIndex = findIndex(id)
    const index = items.value.findIndex(item => item.id == id)

    expanded.value.splice(expIndex, 1)
    items.value.splice(index, 1)

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(async() => {
  await getPeoples()
  await getCompetencies()
  await getJobs()

})
</script>

<route lang="yaml">
  name: peoples
  meta:
    title: Peoples
    requiresAuth: true
</route>