<template>
  <v-container fluid>
    <v-card class="pa-4" prepend-icon="mdi-account-group" title="Job Titles" :loading="loading" :disabled="loading">
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
                <td class="text-center"> <v-icon @click="onExpand(item)"> {{
                  isExpanded(item)
                                    ?'mdi-arrow-up-drop-circle': 'mdi-arrow-down-drop-circle'
                }}</v-icon> </td>
              </tr>
              <tr v-if="isExpanded(item)">
                <td :colspan="headers.length" class="py-4">
                  <v-card elevation="0" :title="item.name" prepend-icon="mdi-briefcase"
                    subtitle="Required Competencies">
                    <v-card-text>
                      <v-list lines="one" density="compact" item-value="id" :items="item.skills" item-title="name">
                        <template #item="{ title, value }">
                          <v-list-item class="mb-2">
                            <v-card variant="tonal" color="primary" class="pb-2">
                              <v-card-text> {{ title }} </v-card-text>
                            </v-card>
                          </v-list-item>
                        </template>
                      </v-list>
                    </v-card-text>


                  </v-card>

                </td>
              </tr>
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

const pagination = ref({
  page: 1,
})


const items = ref([])

const expanded = ref([])
const isExpanded = (item) => expanded.value.includes(item)

const onExpand = (item) => {
  const index = expanded.value.findIndex(exp => exp.id == item.id)

  if (index < 0) {
    expanded.value.push(item)
  } else {
    expanded.value.splice(index, 1)
  }
}

const loading = ref(true)

const getJobs = async (page=1) => {
  try {
    const { data: job } = await axios.get(`/api/job-title?page=${page}`)

    items.value = job.data

    nextTick(()=>{
      pagination.value.page = job.current_page
      pagination.value.lastPage = job.last_page
    })
    
  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
}

onMounted(()=>{
  getJobs()
})
</script>


<route lang="yaml">
  name: job
  meta:
    title: Jobs
    requiresAuth: true
</route>