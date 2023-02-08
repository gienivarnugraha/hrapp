<template>
  <v-container fluid>
    <v-card class="pa-4" :loading="loading" :disabled="loading">
      <v-card-item class="px-8">
        <v-row align="center" no-gutters>
          <v-col cols="1" class="text-right">  
            <v-icon class="mr-4">
              mdi-briefcase
            </v-icon>
          </v-col>
          <v-col cols="9">
            <v-card-item>
              <v-card-title>
                Competencies
              </v-card-title>
              <v-card-subtitle> Compentency Lists </v-card-subtitle>
            </v-card-item>

          </v-col>
          <v-col cols="2" class="text-right">

            <v-menu v-model="add" :close-on-content-click="false" location="end">
              <template v-slot:activator="{ props }">
                <v-btn size="small" class="w-75" :rounded="false" v-bind="props" prepend-icon="mdi-plus"
                  variant="flat">Add</v-btn>
              </template>

              <v-card min-width="300" title="Add Competency">
                <v-card-text>
                  <v-textarea v-model="addModel.name" label="Competency Name" density="compact"></v-textarea>
                  <v-select :items="types" v-model="addModel.type" label="Competency Type" density="compact"></v-select>

                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>

                  <v-btn variant="text" color="error" @click="add = false">
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
        <Loading v-if="loading"></Loading>
        <v-table fixed-header height="75vh" v-else>
          <thead>
            <tr>
              <th v-for="header in headers" :key="header.title" :class="header.class">
                {{ header.title }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>
                <v-text-field hide-details v-if="item.edit" class="w-100" v-model="item.name"
                  density="compact"></v-text-field>
                <span v-else> {{ item.name }} </span>

              </td>
              <td class="text-center">
                <v-select hide-details v-if="item.edit" class="w-75" :items="types" v-model="item.type"
                  density="compact"></v-select>
                <span v-else> {{ item.type }} Skills </span>
              </td>
              <td class="text-center">
                <v-btn v-if="item.edit" size="small" color="success" icon="mdi-check" variant="text"
                  @click="onSave(item)"></v-btn>
                <v-btn v-else size="small" color="error" icon="mdi-delete" variant="text"
                  @click="onDelete(item.id)"></v-btn>
                <v-btn size="small" color="info" :icon="item.edit ? 'mdi-close' : 'mdi-pencil'" variant="text"
                  @click="updateItem(item)"></v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>

const headers = [
  { title: "Name", class: "text-center w-50", },
  { title: "Type", class: "text-center", },
  { title: "Action", class: "text-center", },
]

const items = ref([])

const add = ref(false)

const addModel = reactive({
  name: "",
  type: null
})

const loading = ref(true)

const types = [
  { title: "Soft Skill", value: 'soft' },
  { title: "Hard Skill", value: 'hard' },
  { title: "DOA Skill", value: 'doa' },
]

const findIndex = (id) => items.value.findIndex((item) => item.id === id)

const updateItem = (item) => {
  item.edit = !item.edit
}


const onSave = async (item) => {
  loading.value = true

  try {
    if (item && item.edit) {
      await axios.put(`/api/competencies/${item.id}`, { item })

      const competency = items.value.find(i => i.id === item.id)

      competency.edit = false

    } else {
      const { data: competency } = await axios.post(`/api/competencies`, addModel)
      console.log(competency);
      items.value.push(competency)
      add.value = false
    }

  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
}


const onDelete = async (id) => {
  loading.value = true

  try {
    await axios.delete(`/api/competencies/${id}`)

    const index = findIndex(id)

    console.log(index);

    items.value.splice(index, 1)


  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  try {
    const { data: competencies } = await axios.get('/api/competencies')
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
    title: Competencies
    requiresAuth: true
</route>