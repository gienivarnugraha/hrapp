<template>
  <v-container fluid>
    <v-card class="pa-4" prepend-icon="mdi-account-group" title="Peoples" :loading="loading" :disabled="loading">
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
                      <v-card-title> {{ item.name }}</v-card-title>
                      <v-card-subtitle> Competencies: </v-card-subtitle>
  
                      <v-card-text>
                        <v-list lines="one" density="compact" disabled :items="item.skills" item-value="id"
                          item-title="name"> </v-list>
    
                        <v-select class="w-25" :items="positions" v-model="expanded[findIndex(item)]['next_position']" label="Promote to" density="comfortable"></v-select>
    
                        <v-card-subtitle v-if="item['next_position']" > Required Competencies to Promote into {{ item['next_position'] }} </v-card-subtitle>

                        <v-slide-y-transition group v-if="item['next_position']">
    
                          <v-list v-if="item['next_position']" lines="one" density="compact" disabled :items="item.required_skills[item['next_position']]" item-value="id"
                            item-title="name">
                            <template v-slot:item="{title, value, index}">
                              <v-list-item :class="item.skills.find((i)=>i.id === value) ? 'text-green': 'text-red'">
                                {{ title }} - <v-icon :icon="item.skills.find((i)=>i.id === value) ? 'mdi-check' : 'mdi-close'"> </v-icon>
                              </v-list-item> 
                            </template>
                           </v-list>
    
                        </v-slide-y-transition>
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
  { title: "Junior", value: 'junior'},
  { title: "Medior", value: 'medior'},
  { title: "Senior", value: 'senior'},
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