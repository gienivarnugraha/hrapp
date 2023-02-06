<template>
  <v-container fluid>
    <v-card class="pa-4" :loading="loading" :disabled="loading">
      <v-card-item class="px-8">
        <v-row align="center">
          <v-col cols="1" class="text-center">
            <v-icon>mdi-briefcase</v-icon>
          </v-col>
          <v-col cols="9">
            <v-card-item>
              <v-card-title>
                Add Job Title
              </v-card-title>
              <v-card-subtitle> Required Competencies </v-card-subtitle>
            </v-card-item>

          </v-col>
          <v-col cols="2" class="text-right">
            <v-menu v-model="addJob" :close-on-content-click="false" location="end">
              <template v-slot:activator="{ props }">
                <v-btn size="small" class="w-75" :rounded="false" v-bind="props" prepend-icon="mdi-plus"
                  variant="flat">Add</v-btn>
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
          </v-col>
        </v-row>
      </v-card-item>

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
                <td class="text-center"> <v-icon @click="onExpand(item)" :icon="item.expanded ?'mdi-arrow-up-drop-circle': 'mdi-arrow-down-drop-circle'"></v-icon> </td>
              </tr>
              <v-slide-y-transition>
                <tr v-if="item.expanded">
                  <td :colspan="headers.length" class="py-4">
                    <v-card elevation="0">
                      <v-card-item class="px-8">
                        <v-row align="center">
                          <v-col cols="1" class="text-center">
                            <v-icon>mdi-briefcase</v-icon>
                          </v-col>
                          <v-col cols="7">
                            <v-card-item>
                              <v-card-title>
                                {{ item.name }}
                              </v-card-title>
                              <v-card-subtitle> Required Competencies </v-card-subtitle>
                            </v-card-item>
                          </v-col>
                          <v-col :cols="item.position ? 2 : 4">
                            <v-select class="w-100" :items="positions" v-model="item.position" label="Position"
                              density="comfortable"></v-select>
                          </v-col>
                          <v-col v-if="item.position" class="text-right">
                            <v-menu :key="item.id" v-model="item.edit" :close-on-content-click="false" location="end">
                              <template v-slot:activator="{ props }">
                                <v-btn size="small" color="info" class="w-75" :rounded="false" v-bind="props" prepend-icon="mdi-pencil"
                                  variant="flat">Edit</v-btn>
                              </template>

                              <v-card min-width="300" max-width="600" :title="`Edit Job Title for ${item.position} position `" class="pa-4" :disabled="loading">
                                <v-card-text>
                                  <v-textarea v-model="item.name" label="Job Name" density="compact"></v-textarea>

                                  <v-select label="Select Hard Skills" return-object v-model="item.competencies[item.position]['hard']"
                                    :items="competencies['hard']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>
                                  <v-select label="Select Soft Skills" return-object v-model="item.competencies[item.position]['soft']"
                                    :items="competencies['soft']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>
                                  <v-select label="Select DOA Skills" return-object v-model="item.competencies[item.position]['doa']"
                                    :items="competencies['doa']" item-value="id" item-title="name" chips multiple
                                    :value-comparator="compare"></v-select>

                                </v-card-text>
                                <v-card-actions>
                                  <v-spacer></v-spacer>

                                  <v-btn variant="text" color="gray-lighten-2" @click="item.edit = false">
                                    Cancel
                                  </v-btn>
                                  <v-btn color="primary" variant="flat" :rounded="false" @click="onSave(item)">
                                    Save
                                  </v-btn>
                                </v-card-actions>
                              </v-card>
                            </v-menu>
                          </v-col>
                        </v-row>
                      </v-card-item>
                      <v-card-text v-if="item.position">
                        <competency-list :items="item.competencies[item.position]['hard']" header="Hard Skills"></competency-list>
                        <competency-list :items="item.competencies[item.position]['soft']" header="Soft Skills"></competency-list>
                        <competency-list :items="item.competencies[item.position]['doa']" header="DOA Skills"></competency-list>
                      </v-card-text>
                    </v-card>
                  </td>
                </tr>
              </v-slide-y-transition>
            </template>
          </tbody>
        </v-table>
      </v-card-text>
      <v-card-actions>
        <v-pagination v-model="pagination.page" :length="pagination.lastPage" @next="getJobs(pagination.page)"
          @prev="getJobs(pagination.page)"></v-pagination>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  { title: "Name", class: "text-left", },
  { title: "Action", class: "text-center w-25", },
]

const items = ref([])

const competencies = ref([])

const expanded = ref([])

const pagination = ref({
  page: 1,
})

const loading = ref(true)

const addJob = ref(false)

const positions = [
  { title: "Junior", value: 'junior' },
  { title: "Medior", value: 'medior' },
  { title: "Senior", value: 'senior' },
]

const compare = (a, b) =>  a.id === b.id

const findIndex = (id) => items.value.findIndex((item) => item.id === id)

const addModel = reactive({
  name: ''
})

const onExpand = async (item) => {
  const index = expanded.value.findIndex(exp => exp.id == item.id)

  if (index < 0) {
    item.position = null

    const competencies = await showCompetencies(item)

    item.competencies = competencies

    expanded.value.push(item)

    item.edit = false
    nextTick(() => {
      item.expanded = true
    })

  } else {
    item.expanded = false
    item.edit = false
    
    expanded.value.splice(index, 1)
  }
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

const showCompetencies = async (item) => {
  try {
    loading.value = true
    
    const { data } = await axios.get(`/api/job-title/${item.id}`)
    
    return data.competencies
    
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false
  }
}

const getJobs = async (page = 1) => {
  try {
    const { data: job } = await axios.get(`/api/job-title?page=${page}`)

    items.value = job.data

    nextTick(() => {
      pagination.value.page = job.current_page
      pagination.value.lastPage = job.last_page
    })

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

const onSave = async (item) => {
  loading.value = true

  try {
    if (item && item.edit) {
      const { data: jobTitle } = await axios.put(`/api/job-title/${item.id}`, item)

      const index = findIndex(item.id)

      jobTitle['expanded'] = false 

      jobTitle['competencies'] = jobTitle['skills']

      jobTitle['edit'] = false 

      items.value.splice(index, 1, jobTitle)

    } else {
      const { data: competency } = await axios.post(`/api/job-title`, addModel)

      items.value.push(competency)

      addJob.value = false
    }

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

const onDelete = async (id) => {
  loading.value = true

  try {
    await axios.delete(`/api/job-title/${id}`)

    const index = findIndex(id)


    items.value.splice(index, 1)


  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  getJobs()

  if (competencies.value.length === 0) await getCompetencies()

})
</script>


<route lang="yaml">
  name: job
  meta:
    title: Jobs
    requiresAuth: true
</route>