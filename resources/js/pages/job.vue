<template>
  <Teleport to="#page-header">
    <div class="d-flex justify-space-between align-center px-4">
      <div  v-if="!xs" class="w-50">
        <v-icon class="mr-4" icon="mdi-briefcase" size="large" />
        <span class="text-h6"> Job Titles </span>
      </div>
      <v-spacer></v-spacer>
      <v-menu v-model="addJob" :close-on-content-click="false" location="end">
        <template v-slot:activator="{ props }">
          <v-btn :size="xs ? 'x-small' : 'small'"  :rounded="false" v-bind="props" prepend-icon="mdi-plus" variant="flat">Add Title</v-btn>
        </template>

        <v-card min-width="300" title="Add Job">
          <v-card-text>
            <v-textarea v-model="addModel.name" label="Job Name" density="compact"></v-textarea>

          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn variant="text" color="error" @click="addJob = false">
              Cancel
            </v-btn>
            <v-btn color="primary" variant="flat" @click="onSave()">
              Save
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
    </div>
  </Teleport>

  <v-container fluid>
    <v-card class="px-4 py-2" :loading="loading" :disabled="loading">
      <v-card-text>
        <v-data-table-server :headers="headers" :items="items" :items-length="total" :loading="loading"
          :items-per-page="15" item-value="id" item-title="name" show-expand @update:options="options = $event"
          @update:expanded="onExpand($event)" @update:page="getJobs($event)">
          <template v-slot:expanded-row="{ columns, item }">
            <tr>
              <td :colspan="columns.length">
                <v-card elevation="0" :loading="!item.raw.loaded">
                  <v-card-item class="px-8">
                    <v-row align="center">
                      <v-col v-if="!xs" :cols="1" class="text-center">
                        <v-icon>mdi-briefcase</v-icon>
                      </v-col>
                      <v-col :cols="xs ? 12 : 6">
                        <v-card-item>
                          <v-card-title>
                            {{ item.raw.name }}
                          </v-card-title>
                          <v-card-subtitle> Required Competencies </v-card-subtitle>
                        </v-card-item>
                      </v-col>
                      <v-col :cols="xs ? 12 : 3">
                        <v-select class="w-100" :items="positions" v-model="item.raw.position" label="Position"
                          density="compact" hide-details></v-select>
                      </v-col>
                      <v-col v-if="item.raw.position" class="text-right" :cols="xs ? 12 : 2">
                        <v-menu :key="item.raw.id" v-model="item.raw.edit" :close-on-content-click="false" location="end">
                          <template v-slot:activator="{ props }">
                            <v-btn :size="xs ? 'x-small' : 'small'" color="info" class="w-100" :rounded="false" v-bind="props"
                              prepend-icon="mdi-pencil" variant="flat">Edit</v-btn>
                          </template>

                          <v-card min-width="300" max-width="600"
                            :title="`Edit Job Title for ${item.raw.position} position `" class="pa-4" :disabled="loading">
                            <v-card-text>
                              <v-text-field v-model="item.raw.name" label="Job Name"></v-text-field>

                              <v-select label="Select Hard Skills" return-object
                                v-model="item.raw.competencies[item.raw.position]['hard']" :items="competencies['hard']"
                                item-value="id" item-title="name" chips multiple :value-comparator="compare"></v-select>
                              <v-select label="Select Soft Skills" return-object
                                v-model="item.raw.competencies[item.raw.position]['soft']" :items="competencies['soft']"
                                item-value="id" item-title="name" chips multiple :value-comparator="compare"></v-select>
                              <v-select label="Select DOA Skills" return-object
                                v-model="item.raw.competencies[item.raw.position]['doa']" :items="competencies['doa']"
                                item-value="id" item-title="name" chips multiple :value-comparator="compare"></v-select>

                            </v-card-text>
                            <v-card-actions>
                              <v-spacer></v-spacer>

                              <v-btn variant="text" color="gray-lighten-2" @click="item.raw.edit = false">
                                Cancel
                              </v-btn>
                              <v-btn color="primary" variant="flat" :rounded="false" @click="onSave(item.raw)">
                                Save
                              </v-btn>
                            </v-card-actions>
                          </v-card>
                        </v-menu>
                      </v-col>
                    </v-row>
                  </v-card-item>

                  <v-card-text v-if="item.raw.loaded">
                    <v-card-subtitle class="mb-4">
                      Member Lists :
                    </v-card-subtitle>

                    <people-list :items="item.raw.peoples" :job-title-id="item.raw.id"></people-list>
                  </v-card-text>

                  <v-card-text v-if="item.raw.loaded && item.raw.position">
                    <v-card-subtitle>
                      Required Competencies :
                    </v-card-subtitle>

                    <competency-list :items="item.raw.competencies[item.raw.position]['hard']"
                      header="Hard Skills"></competency-list>
                    <competency-list :items="item.raw.competencies[item.raw.position]['soft']"
                      header="Soft Skills"></competency-list>
                    <competency-list :items="item.raw.competencies[item.raw.position]['doa']"
                      header="DOA Skills"></competency-list>
                  </v-card-text>
                </v-card>
              </td>
            </tr>
          </template>

          <!-- <template v-slot:item.actions="{ item }"> -->
          <!--   <v-btn size="x-small" color="red-lighten-2" variant="flat" @click="item.raw.modalDelete = true" icon="mdi-delete" /> -->
          <!--   <DialogDelete v-model="item.raw.modalDelete" name="job-title" :id="item.raw.id" :item="item.raw"  @success="onDelete" @cancel="item.raw.modalDelete = false" /> -->
          <!-- </template> -->
        </v-data-table-server>
      </v-card-text>

    </v-card>
  </v-container>
</template>

<script setup>

import { useDisplay } from 'vuetify';
import { get } from '../composables/api'

const {xs} = useDisplay ()

const headers = [
  { title: "Name", key: 'name', },
  { title: "Head of Department", key: 'user.name', },
  { title: "Department Member", key: 'peoples_count', align:'center'},
  // { title: 'Actions', key: 'actions', align: 'center', sortable: false, width: '25px' },
]

const total = ref(0)

const options = ref({})

const items = ref([])

const competencies = ref([])

const loading = ref(true)

const addJob = ref(false)

const positions = [
  { title: "Junior", value: 1 },
  { title: "Medior", value: 2 },
  { title: "Senior", value: 3 },
]

const compare = (a, b) => a.id === b.id

const findIndex = (id) => items.value.findIndex((item) => item.id === id)

const addModel = reactive({
  name: ''
})

const onExpand = (ids) => {
  ids.forEach(async (id) => {
    const item = items.value.find(item => item.id === id)

    if (item.loaded === true) return

    item.position = null

    item.edit = false

    const details = await showDetails(id)

    item.competencies = details.competencies

    item.peoples = details.peoples

    item.loaded = true
  });
}

const getCompetencies = async () => {
  try {
    loading.value = true
    const { data: competency } = await axios.get('/api/competencies/types')
    competencies.value = competency

  } catch (error) {

  } finally {
    loading.value = false
  }
}

const showDetails = async (id) => {
  try {
    const { data } = await axios.get(`/api/job-title/${id}`)

    return data

  } catch (error) {
    console.error(error);
  }
}

const getJobs = async () => {
  loading.value = true
  try {
    const { items: jobs, totalItems } = await get('/api/job-title', options)

    items.value = jobs

    total.value = totalItems

  } catch (error) {
    console.error(error);
  } finally {

    nextTick(() => loading.value = false)
  }

}

const onSave = async (item) => {
  loading.value = true

  try {
    if (item && item.edit) {
      const { data: jobTitle } = await axios.put(`/api/job-title/${item.id}`, item)

      const index = findIndex(item.id)

      jobTitle['competencies'] = jobTitle['skills']

      jobTitle['edit'] = false

      jobTitle['loaded'] = true

      jobTitle['position'] = item.position

      items.value.splice(index, 1, jobTitle)

    } else {
      const { data: competency } = await axios.post(`/api/job-title`, addModel)

      competency['loaded'] = true

      items.value.push(competency)

      addJob.value = false
    }

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

const onDelete = (id) => {
  const index = findIndex(id)

  items.value.splice(index, 1)
}

const unwatchQuery = watch(
  () => options,
  () => getJobs(),
  { deep: true, flush: 'sync' }
)

onUnmounted(() => {
  unwatchQuery()
})

onMounted(async () => {

  if (competencies.value.length === 0) await getCompetencies()

})
</script>


<route lang="yaml">
  name: job
  meta:
    title: Jobs
    requiresAuth: true
</route>
