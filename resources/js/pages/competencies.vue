<template>
  <v-container fluid  >
    <v-card class="pa-4" :loading="loading" :disabled="loading"  >
      <v-card-item class="px-8">
        <v-row align="center">
          <v-col cols="10">
            <v-card-title>
              <v-icon class="mr-4">
                mdi-briefcase
              </v-icon>
              Competencies
            </v-card-title>
            <v-card-subtitle> Compentency Lists </v-card-subtitle>
            
          </v-col>
          <v-col cols="2" class="text-right">
            <v-btn size="small" class="w-75" :rounded="false" prepend-icon="mdi-plus" variant="flat">Add</v-btn>
          </v-col>
        </v-row>
      </v-card-item>
      

      <v-card-text>
        <v-table fixed-header height="75vh">
          <thead>
            <tr>
              <th v-for="header in headers" :key="header.title" :class="header.class">
                {{header.title}}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td class="text-center">{{ item.type }}</td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  {title: "Name", class: "text-center w-75",},
  {title: "Type", class: "text-center",},
]

const items = ref([])

const loading = ref(true)

onMounted(async()=>{
  try {
    const {data: competencies} = await axios.get('/api/competencies')
    items.value = competencies
  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  } 
})
</script>


<route lang="yaml">
  name: competencies
  meta:
    requiresAuth: true
</route>