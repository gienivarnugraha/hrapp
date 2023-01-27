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
                <td :colspan="headers.length" class="py-4">
                  <v-card variant="outlined" color="indigo">
                    <v-card-title> {{ item.name }}</v-card-title>
                    <v-card-subtitle> Competencies: </v-card-subtitle>
                    <v-list density="compact" v-if="item.skills.hard">
                      <v-list-item-title> Hard Skills </v-list-item-title>
                      <v-list-item v-for="(competency, index) in item.skills.hard" :key="competency.id">
                        <template v-slot:prepend> {{ index+ 1 }} </template>
                        <p class="ml-4"> {{ competency.name }} </p>
                      </v-list-item>
                    </v-list>

                    <v-list lines="one" :items="item.competencies" item-value="id" item-title="name"> 

                      </v-list>

                  </v-card>

                </td>
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

onMounted(async () => {
  try {
    const { data: peoples } = await axios.get('/api/peoples')
    peoples.map(people=>{
      let skills = people.competencies

      let hardIndex = skills.findIndex(skill => skill.type === 'hard' )
      let softIndex = skills.findIndex(skill => skill.type === 'soft' )
      let doaIndex = skills.findIndex(skill => skill.type === 'doa' )

      skills.splice(hardIndex, 0,{type:'subheader',name: 'hard'})
      skills.splice(softIndex, 0, {type:'divider'}, {type:'subheader',name:'soft'})
      skills.splice(doaIndex, 0, {type:'divider'}, {type:'subheader',name:'doa'})

      
      people['skills'] = skills
      
    })
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