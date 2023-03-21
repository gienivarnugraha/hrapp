<template>
  <v-container class="pa-4 d-flex justify-center align-center" style="height:100%;">
    <v-card class="pa-4 w-50" border align="center" border-color="primary">
      
      <img src="/storage/img/polban.png" alt="">

      <v-card-title prepend-icon="mdi-home">Login Human Resource Matrix Application</v-card-title>

      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-text-field class="my-4" color="primary" :error-messages="errors['email']"
            :error="errors['email'] ? true : false" v-model="email" :rules="emailRules" label="E-mail"
            required></v-text-field>

          <v-text-field class="my-4" color="primary" :error-messages="errors['password']"
            :error="errors['password'] ? true : false" v-model="password" :counter="12" label="Password"
            :type="showPassword ? 'input' : 'password'" :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append="showPassword = !showPassword" required></v-text-field>

          <v-btn color="primary" class="me-4" @click="submit">
            Submit
          </v-btn>

          <v-btn color="error" class="me-4" @click="reset">
            Reset
          </v-btn>

        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script setup>
import { useUserStore } from '../store/user';

const { login } = useUserStore()

const valid = ref(false)
const password = ref('')
const email = ref('')

const showPassword = ref(false)

const errors = ref({})

const emailRules = [
  v => !!v || 'E-mail is required',
  v => /.+@.+/.test(v) || 'E-mail must be valid',
]
const form = ref(null)

async function submit() {
  try {
    const { valid } = await form.value.validate()

    if (valid) {
      await login({ email, password })
    }

  } catch (error) {
    errors.value = error.errors
  }
}

function reset() {
  form.value.reset()
}

</script>

<route lang="yaml">
  name: login
  meta:
    title: Login
    requiresAuth: false
</route>