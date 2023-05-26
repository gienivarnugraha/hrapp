<template>
  <v-container class="pa-4 d-flex justify-center align-center" style="height:100%;">
    <v-card class="pa-2 w-full w-md-50" border align="center" :loading="loading" :disabled="loading" border-color="primary">

      <img src="/storage/img/polban.png" style="width: 100%;max-width: 100px;height: auto;" class="my-4" alt="">

      <h3 class="text-center px-4">Login Human Resource Matrix Application</h3>

      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-text-field class="my-4" color="primary" :error-messages="errors['email']"
            :error="errors['email'] ? true : false" v-model="email" :rules="emailRules" label="E-mail"
            required></v-text-field>

          <v-text-field class="my-4" color="primary" :error-messages="errors['password']"
            :error="errors['password'] ? true : false" v-model="password" :counter="12" label="Password"
            :type="showPassword ? 'input' : 'password'" :append-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            @keyup.enter="submit" @click:append="showPassword = !showPassword" required></v-text-field>

          <v-btn color="primary" :rounded="false" block class=" m-4" :disabled="!valid" @click="submit">
            Login
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script setup>
import { useUserStore } from '../store/user';
import { useRouter } from 'vue-router';

const router = useRouter()
const { login } = useUserStore()

const valid = ref(false)
const password = ref('')
const email = ref('')
const loading = ref(false)

const showPassword = ref(false)

const errors = ref({})

const emailRules = [
  v => !!v || 'E-mail is required',
  v => /.+@.+/.test(v) || 'E-mail must be valid',
]
const form = ref(null)

async function submit() {
  loading.value = true
  try {
    const { valid } = await form.value.validate()

    if (valid) {
      await login({ email, password })
    }

  } catch (error) {
    errors.value = error.errors

    setTimeout(() => {
      errors.value = {}
    }, 3000);
    
  } finally {
    loading.value = false
  }
}

</script>

<route lang="yaml">
  name: login
  meta:
    title: Login
    requiresAuth: false
</route>