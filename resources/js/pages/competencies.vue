<template>
  <Teleport to="#page-header">
    <div class="d-flex justify-space-between align-center px-4">
      <div class="w-25">
        <v-icon class="mr-4" icon="mdi-star" size="large" />
        <span class="text-h6"> Competencies </span>
      </div>
      <v-spacer></v-spacer>
      <v-menu v-model="add" :close-on-content-click="false" location="end">
        <template v-slot:activator="{ props }">
          <v-btn size="small" :rounded="false" v-bind="props" prepend-icon="mdi-plus"
            variant="flat">Add Competency</v-btn>
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
    </div>
  </Teleport>

  <v-container fluid>
    <v-card class="px-4 py-2" :disabled="loading">
      <v-card-text>
        <v-data-table-server :headers="headers" :items="items" :items-length="total" :loading="loading"
          :items-per-page="15" item-value="id" item-title="name" @update:options="options = $event">

          <template v-slot:item.name="{ item }">
            <v-text-field v-if="item.raw.edit" class="w-100" v-model="item.raw.name" density="compact"
              hide-details></v-text-field>
            <span v-else> {{ item.raw.name }} </span>
          </template>
          <template v-slot:item.type="{ item }">
            <v-select v-if="item.raw.edit" class="w-75" :items="types" v-model="item.raw.type" density="compact"
              hide-details></v-select>
            <span class="text-capitalize" v-else> {{ item.raw.type }} Skills </span>
          </template>

          <template v-slot:item.actions="{ item }">
            <v-btn v-if="item.raw.edit" size="small" color="success" icon="mdi-check" variant="text"
              @click="onSave(item.raw)"></v-btn>
            <v-btn v-else size="small" color="error" icon="mdi-delete" variant="text"
              @click="item.raw.modalDelete = true"></v-btn>
            <v-btn size="small" color="info" :icon="item.raw.edit ? 'mdi-close' : 'mdi-pencil'" variant="text"
              @click="item.raw.edit = !item.raw.edit"></v-btn>
            <DialogDelete v-model="item.raw.modalDelete" name="competencies" :id="item.raw.id" :item="item.raw"
              @success="onDelete" @cancel="item.raw.modalDelete = false" />
          </template>
        </v-data-table-server>

      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { get } from '../composables/api'

const headers = [
  { title: "Name", key: 'name', },
  { title: "Type", key: 'type', align: 'center', width: '20%' },
  { title: 'Actions', key: 'actions', align: 'center', sortable: false, width: '15%' },
]

const items = ref([])

const total = ref(0)

const options = ref({})

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

const onSave = async (item) => {
  loading.value = true

  try {
    if (item && item.edit) {
      await axios.put(`/api/competencies/${item.id}`, item)

      const competency = items.value.find(i => i.id === item.id)

      competency.edit = false

    } else {
      await axios.post(`/api/competencies`, addModel)

      getCompetencies()

      add.value = false
    }

  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
}

const onDelete = async (id) => {
  const index = items.value.findIndex((item) => item.id === id)
  items.value.splice(index, 1)
}

const getCompetencies = async () => {
  loading.value = true
  try {
    const { items: competencies, totalItems } = await get('/api/competencies', options)
    items.value = competencies
    total.value = totalItems
  } catch (error) {
    console.log(error);
  } finally {
    loading.value = false;
  }
}

const unwatchQuery = watch(
  () => options,
  () => getCompetencies(),
  { deep: true, flush: 'sync' }
)

onUnmounted(() => {
  console.log('unwatch query')
  unwatchQuery()
})
</script>


<route lang="yaml">
  name: competencies
  meta:
    title: Competencies
    requiresAuth: true
</route>