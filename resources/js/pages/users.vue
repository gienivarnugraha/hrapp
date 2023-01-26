<template>
  <v-container fluid  >
    <v-card class="pa-4" prepend-icon="mdi-account-group" title="Users" :loading="loading" :disabled="loading" >
      <v-card-text>
        <v-table fixed-header height="75vh" >
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
              <td>{{ item.nik }}</td>
              <td>{{ item.org }}</td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  {title: "Name", class: "text-left",},
  {title: "NIK", class: "text-left",},
  {title: "ORG", class: "text-left",},
]

const items = ref([])

const loading = ref(true)

onMounted(async()=>{
  try {
    const {data: users} = await axios.get('/api/users')
    items.value = users
  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  } 
})
</script>


<route lang="yaml">
  name: users
  meta:
    requiresAuth: true
</route>