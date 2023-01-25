<template>
  <v-container class="pa-4 d-flex justify-center align-center" style="height:100%;">
    <v-card class="pa-4" border align="center" min-width="640px" border-color="primary">
      <v-card-title>Login</v-card-title>

      <v-form ref="form" v-model="valid" lazy-validation>

        <v-text-field class="my-4" color="primary" v-model="email" :rules="emailRules" label="E-mail" required></v-text-field>

        <v-text-field class="my-4" color="primary"  v-model="password" :counter="10" label="Password" type="password" required></v-text-field>

        <!-- 
            <v-checkbox
              v-model="checkbox"
              :rules="[v => !!v || 'You must agree to continue!']"
              label="Do you agree?"
              required
            ></v-checkbox>
         -->
        <v-btn  color="primary" class="me-4" @click="submit">
          Submit
        </v-btn>

        <v-btn  color="error" class="me-4" @click="reset">
          Reset
        </v-btn>

      </v-form>
    </v-card>
  </v-container>
</template>
<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from '../store/user';

const router = useRouter()
const userStore = useUserStore()

const valid = ref(false)
const password = ref('')
const email = ref('')
const emailRules = [
  v => !!v || 'E-mail is required',
  v => /.+@.+/.test(v) || 'E-mail must be valid',
]
const form = ref(null)

async function submit() {
  try {
    const { valid } = await form.value.validate()

    if (valid) {

      await axios.get('sanctum/csrf-cookie')

      await axios.post('login', {
        email: email.value,
        password: password.value
      })

      const { data: user } = await axios.get('/api/user')

      userStore.login(user)

      await router.push({ name: 'dashboard' })

    }

  } catch (error) {
    console.log(error);

  }
}

function reset() {
  form.value.reset()
}

</script>

<route lang="yaml">
  name: login
  meta:
    requiresAuth: false
</route>