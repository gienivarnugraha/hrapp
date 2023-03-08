<template>
  <v-dialog max-width="500px" :key="id">
    <v-card  :loading="loading" >
      <v-card-title class="text-h6">Are you sure you want to delete {{item.name}}?</v-card-title>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn :loading="loading" variant="text" :rounded="false" @click="$emit('cancel')">Cancel</v-btn>
        <v-btn color="error" variant="tonal" :rounded="false" :loading="loading" @click="onDelete">OK</v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
const props = defineProps({
  id: String | Number,
  item: Object,
  name: String,
})

const loading = ref(false)

const emit = defineEmits(['cancel', 'success'])

const onDelete = async () => {
  loading.value = true
  try {
    await axios.delete(`/api/${props.name}/${props.item.id}`)

    emit('success', props.id)
  } catch (error) {
    console.error(error);
  }finally{
    loading.value = false;
  }
}
</script>