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
                  <v-card variant="outlined">
                    <v-card-title> {{ item.name }}</v-card-title>
                    <v-card-subtitle> Competencies: </v-card-subtitle>
                    <v-card-text>
                      <v-list lines="one" density="compact" disabled item-value="id" :items="item.skills"
                        item-title="name">
                        <template v-slot:item="{ title, value, index }">
                          <v-list-item>
                            {{ title }}
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
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  { title: "Name", class: "text-left", },
  { title: "Action", class: "text-center w-25", },
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
    const { data: job } = await axios.get('/api/job-title')
    items.value = job
  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
})
</script>


<route lang="yaml">
  name: job
  meta:
    requiresAuth: true
</route>