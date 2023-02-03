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
                <tr v-if="item.expanded && !item.loadCompetencies">
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
                                <v-select class="w-100" :items="positions"
                                  v-model="item.next_position" label="Promote to"
                                  density="comfortable"></v-select>

                              </v-col>
                              <v-col cols="6" v-if="item.next_position">
                                <v-btn stacked size="x-small" class="w-100" color="primary" :rounded="false"
                                  prepend-icon="mdi-sync" variant="flat" @click="generateSchedule(item)">Generate
                                  Schedule</v-btn>

                              </v-col>
                            </v-row>

                            <v-slide-y-transition>
                              <div v-if="item.next_position">
                                <competency-list :items="item.required_skills[item.next_position]['hard']" header="Hard Skills"></competency-list>
                                <competency-list :items="item.required_skills[item.next_position]['soft']" header="Soft Skills"></competency-list>
                                <competency-list :items="item.required_skills[item.next_position]['doa']" header="DOA Skills"></competency-list>

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

const pagination = ref({
  page: 1,
  totalPage: 1,
})

const items = ref([])

const expanded = ref([])

const findIndex = (item) => expanded.value.findIndex(exp => exp.id == item.id)

const getCompetencies = async (item) => {
  try {
    const { data: people } = await axios.get(`/api/peoples/${item.id}`)

    item.skills = people.skills

    item.required_skills = people.required_skills

    item.next_position = ''

    item['expanded'] = true

    return item

  } catch (error) {
    console.log(error);
  } finally {

  }
}

const onExpand = async (item) => {
  const index = findIndex(item)

  if (index < 0) {
    try {
      item.loadCompetencies = true

      const people = await getCompetencies(item)

      expanded.value.push(people)

    } catch (error) {
      console.log(error);
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

const getPeoples = async (page = 1) => {
  loading.value = true

  try {
    const { data: peoples } = await axios.get(`/api/peoples?page=${page}`)

    items.value = peoples.data

    pagination.value.page = peoples.current_page
    pagination.value.totalPage = peoples.total_page

  } catch (error) {
    console.log(error);
  } finally {
    nextTick(() => loading.value = false
    )
  }
}

onMounted(() => {
  getPeoples()
})
</script>

<route lang="yaml">
  name: peoples
  meta:
    title: Peoples
    requiresAuth: true
</route>