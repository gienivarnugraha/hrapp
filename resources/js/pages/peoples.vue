<template>
  <Teleport to="#page-header">
    <div class="d-flex justify-space-between align-center px-4">
      <div  v-if="!xs" class="w-50">
        <v-icon class="mr-4" icon="mdi-account-group" size="large" />
        <span class="text-h6"> Peoples </span>
      </div>
      <v-spacer></v-spacer>
      <v-menu v-model="addMenu" :close-on-content-click="false" location="end">
        <template v-slot:activator="{ props }">

          <v-btn  :size="xs ? 'x-small' : 'small'" :rounded="false" prepend-icon="mdi-plus" variant="flat" v-bind="props">Add People</v-btn>
        </template>

        <v-card class="pa-4" min-width="300" title="Add People" :disabled="addModel.loading" :loading="addModel.loading">
          <v-card-text>

            <v-text-field v-model="addModel.name" label="Name"> </v-text-field>

            <v-text-field v-model="addModel.nik" label="NIK"> </v-text-field>

            <v-text-field v-model="addModel.email" label="Email"> </v-text-field>

            <v-text-field v-model="addModel.org" label="Organization Code"> </v-text-field>

            <v-autocomplete label="Position" v-model="addModel.position" :items="positions"></v-autocomplete>

            <v-autocomplete label="Job Title" v-model="addModel.job_title_id" :items="jobTitles" item-value="id" item-title="name" ></v-autocomplete>

            <v-card-subtitle>Skills:</v-card-subtitle>

            <v-autocomplete label="Select Hard Skills" v-model="addModel.skills['hard']" :items="competencies['hard']"
              item-value="id" item-title="name" chips multiple></v-autocomplete>
            <v-autocomplete label="Select Soft Skills" v-model="addModel.skills['soft']" :items="competencies['soft']"
              item-value="id" item-title="name" chips multiple></v-autocomplete>
            <v-autocomplete label="Select DOA Skills" v-model="addModel.skills['doa']" :items="competencies['doa']"
              item-value="id" item-title="name" chips multiple></v-autocomplete>

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
    </div>
  </Teleport>

  <v-container fluid>
    <v-card class="px-4 pt-4" :loading="loading" :disabled="loading">
      <v-card-title>
        <v-autocomplete label="Filter By Jobs" hide-details variant="outlined" @update:modelValue="filterByJobs" :items="jobTitles" item-value="id" item-title="name"></v-autocomplete>
      </v-card-title>

      <v-card-text>
        <v-data-table-server :headers="headers" :items="items" :items-length="total" :loading="loading"
          :group-by="groupBy" :items-per-page="15" item-value="id" item-title="name" show-expand
          @update:options="options = $event" @update:expanded="onExpand($event)" @update:page="getPeoples($event)">
          <template v-slot:expanded-row="{ columns, item }">
            <tr>
              <td :colspan="columns.length">
                <Loading v-if="!item.raw.loaded" />
                <v-card variant="outlined" color="indigo" class="pa-4 mt-2 mb-4"  v-else>
                  <v-card-title class="px-8 my-2">
                    <v-row align="center">
                      <v-col :cols="xs ? 12 : 8">
                        <v-icon class="mr-4">
                          mdi-account
                        </v-icon>

                        {{ item.raw.name }}
                      </v-col>
                      <v-col :cols="xs ? 12 : 2" class="text-right">
                        <v-menu :key="item.raw.id" v-model="item.raw.edit" :close-on-content-click="false" location="end">
                          <template v-slot:activator="{ props }">
                            <v-btn size="small" :key="item.raw.id" class="w-100" :rounded="false" prepend-icon="mdi-plus"
                              variant="flat" v-bind="props">Add Skill</v-btn>
                          </template>

                          <v-card class="pa-4" min-width="300" :title="`Add Skills to ${item.raw.name}`"
                            :disabled="item.raw.loadCompetencies" :loading="item.raw.loadCompetencies">
                            <v-card-text>
                              <v-text-field label="Name" v-model="item.raw.name"></v-text-field>

                              <v-text-field label="Email" v-model="item.raw.email"></v-text-field>

                              <v-select label="Position" v-model="item.raw.position" :items="positions"></v-select>

                              <v-autocomplete label="Select Hard Skills" return-object v-model="item.raw.skills['hard']"
                                :items="competencies['hard']" item-value="id" item-title="name" chips multiple
                                :value-comparator="compare"></v-autocomplete>
                              <v-autocomplete label="Select Soft Skills" return-object v-model="item.raw.skills['soft']"
                                :items="competencies['soft']" item-value="id" item-title="name" chips multiple
                                :value-comparator="compare"></v-autocomplete>
                              <v-autocomplete label="Select DOA Skills" return-object v-model="item.raw.skills['doa']"
                                :items="competencies['doa']" item-value="id" item-title="name" chips multiple
                                :value-comparator="compare"></v-autocomplete>

                            </v-card-text>
                            <v-card-actions>
                              <v-spacer></v-spacer>

                              <v-btn variant="text" color="error" @click="item.raw.edit = false">
                                Cancel
                              </v-btn>
                              <v-btn color="primary" variant="flat" @click="onSave(item.raw)">
                                Save
                              </v-btn>
                            </v-card-actions>
                          </v-card>
                        </v-menu>

                      </v-col>
                      <v-col :cols="xs ? 12 : 2" class="text-right">
                        <v-btn size="small" class="w-100" color="error" :rounded="false" prepend-icon="mdi-delete"
                          variant="flat" @click="onDelete(item.raw.id)">delete</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-title>

                  <v-slide-y-transition>
                    <v-card-text>
                      <v-row>
                        <v-col :cols="xs ? 12 : 6">
                          <v-card-subtitle> Competencies: </v-card-subtitle>
                          <competency-list :items="item.raw.skills['hard']" header="Hard Skills"></competency-list>
                          <competency-list :items="item.raw.skills['soft']" header="Soft Skills"></competency-list>
                          <competency-list :items="item.raw.skills['doa']" header="DOA Skills"></competency-list>

                        </v-col>

                        <v-col :cols="xs ? 12 : 6">
                          <v-card-subtitle class="mb-4" v-if="item.raw.next_position"> Required
                            Competencies to Promote
                            into {{
                              item.raw.next_position
                            }} </v-card-subtitle>

                          <v-row class=" px-4">
                            <v-col :cols="item.raw.next_position ? 6 : 12">
                              <v-select class="w-100" :items="positions" v-model="item.raw.next_position"
                                label="Promote to" density="comfortable"></v-select>

                            </v-col>
                            <v-col cols="6" v-if="item.raw.next_position">
                              <v-btn stacked size="x-small" class="w-100" color="primary" :rounded="false"
                                prepend-icon="mdi-sync" variant="flat" @click="generateSchedule(item.raw)">Generate
                                Schedule</v-btn>

                            </v-col>
                          </v-row>

                          <v-slide-y-transition>
                            <div v-if="item.raw.next_position">
                              <competency-list :items="item.raw.required_skills[item.raw.next_position]['hard']"
                                :compare="item.raw.skills['hard']" header="Hard Skills"></competency-list>
                              <competency-list :items="item.raw.required_skills[item.raw.next_position]['soft']"
                                :compare="item.raw.skills['soft']" header="Soft Skills"></competency-list>
                              <competency-list :items="item.raw.required_skills[item.raw.next_position]['doa']"
                                :compare="item.raw.skills['doa']" header="DOA Skills"></competency-list>

                            </div>
                          </v-slide-y-transition>
                        </v-col>

                      </v-row>
                    </v-card-text>
                  </v-slide-y-transition>

                </v-card>
              </td>
            </tr>
          </template>
        </v-data-table-server>

        <v-divider class="my-2"></v-divider>

      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { get } from '../composables/api'
import { useDisplay } from 'vuetify'

const {xs} = useDisplay()

const headers = [
  { title: "Name", key: 'name', },
  { title: "NIK", key: 'nik', },
  { title: "ORG", key: 'org', },
  { title: "Job Title", key: 'job_title.name', },
  { title: "Position", key: 'position', },
]

const positions = [
  { title: "Junior", value: 'junior' },
  { title: "Medior", value: 'medior' },
  { title: "Senior", value: 'senior' },
]

let items = ref([])

let groupBy = ref([])

let total = ref(0)

let competencies = ref([])

let jobTitles = ref([])

let options = ref({})

let loading = ref(true)


let addMenu = ref(false)

let addModel = ref({
  name: '',
  nik: '',
  email: '',
  org: '',
  job_title_id: null,
  position: null,
  loading: false,
  skills: { hard: [], soft: [], doa: [] },

})

const compare = (a, b) => a.id === b.id

const findIndex = (id) => items.value.findIndex(item => item.id == id)

const showCompetencies = async (item) => {
  try {
    const { data: people } = await axios.get(`/api/peoples/${item.id}`)

    item.skills = people.skills

    item.required_skills = people.required_skills

    item.next_position = ''

    item.edit = false

    return item

  } catch (error) {
    console.error(error);
  }
}

const onExpand = (ids) => {
  ids.forEach(async (id) => {

    const item = items.value.find(item => item.id === id)

    if (item.loaded === true) return

    item.edit = false

    const competencies = await showCompetencies(item)

    item.competencies = competencies

    item.loaded = true
  });
}

const filterByJobs = async (job) => {
  options.value['filterBy'] = job

  try {
    const { items: peoples } = await get(`/api/peoples`, options)
    items.value = peoples
  } catch (error) {
    console.error(error);
  } 
}

const onSave = async (item) => {
  item.loadCompetencies = true

  const { name, nik, org, position, skills, } = item

  try {
    if (item && item.edit) {
      const { data: people } = await axios.put(`/api/peoples/${item.id}`, { name, nik, org, position, skills })

      const index = findIndex(item.id)

      people['loaded'] = true

      people['next_position'] = item.next_position

      items.value.splice(index, 1, people)

    } else {
      await axios.post(`/api/peoples`, addModel.value)

      addMenu.value = false

      getPeoples()
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

    items.value.splice(index, 1)

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

const generateSchedule = async (people) => {
  loading.value = true
  try {
    const { data: events } = await axios.post('/api/events/generate', {
      people_id: people.id,
      next_position: people.next_position
    })

    let item = items.value.find(item => item.id === people.id)

    events.forEach(event => {
      let compId = Object.keys(event)[0];

      ['hard', 'soft', 'doa'].forEach((type) => {
        let competency = item.required_skills[people.next_position][type].find(comp => comp.id === parseInt(compId))

        if (competency) {
          competency['start_date'] = moment(event[compId]).format('YYYY-MM-DD HH:mm')
        }
      })
    })

  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }

};

const getPeoples = async () => {
  loading.value = true

  try {
    const { items: peoples, totalItems } = await get('/api/peoples', options)

    items.value = peoples

    total.value = totalItems

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

const getJobs = async (q) => {
  //loadingSearch.value = true
  try {
    const { data: jobs } = await axios.get(`/api/job-title`)
    jobTitles.value = jobs
  } catch (error) {
    console.error(error);
  } finally {
    //loadingSearch.value = false
  }
}

const unwatchQuery = watch(
  () => options,
  () => getPeoples(),
  { deep: true, flush: 'sync' }
)

onUnmounted(() => {
  unwatchQuery()
})

onMounted(() => {
  if (competencies.value.length === 0) getCompetencies()
  if (jobTitles.value.length === 0) getJobs()
})
</script>

<route lang="yaml">
  name: peoples
  meta:
    title: Peoples
    requiresAuth: true
</route>