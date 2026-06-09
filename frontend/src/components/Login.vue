<template>
  <div class="login-container">
    <div class="login-card">
      <h1>Banque SPA</h1>
      <h2>Connexion</h2>

      <form @submit.prevent="handleLogin">
        <div class="field">
          <label for="login">Login</label>
          <input
            id="login"
            v-model="form.login"
            type="text"
            placeholder="Entrez votre login"
            required
            autocomplete="username"
          />
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Entrez votre mot de passe"
            required
            autocomplete="current-password"
          />
        </div>

        <p v-if="erreur" class="erreur">{{ erreur }}</p>

        <button type="submit" :disabled="chargement">
          {{ chargement ? 'Connexion…' : 'Se connecter' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import API from '../api.js'

const router = useRouter()

const form = ref({ login: '', password: '' })
const erreur = ref('')
const chargement = ref(false)

async function handleLogin() {
  erreur.value = ''
  chargement.value = true

  try {
    const response = await axios.post(API.auth, {
      login: form.value.login,
      password: form.value.password
    })

    if (response.data.success) {
      
      sessionStorage.setItem('user', JSON.stringify(response.data.user))
      router.push('/liste')
    } else {
      erreur.value = response.data.message || 'Identifiants incorrects.'
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      erreur.value = err.response.data.message
    } else {
      erreur.value = 'Impossible de contacter le serveur.'
    }
  } finally {
    chargement.value = false
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #f0f4f8;
}

.login-card {
  background: white;
  border-radius: 12px;
  padding: 2.5rem 2rem;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
  text-align: center;
}

h1 {
  font-size: 2rem;
  margin-bottom: 0.25rem;
  color: black;
}

h2 {
  font-size: 1.1rem;
  color: #555;
  margin-bottom: 1.5rem;
  font-weight: 400;
}

.field {
  text-align: left;
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.3rem;
  font-size: 0.9rem;
  color: #333;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 0.6rem 0.8rem;
  border: 1px solid #ccd;
  border-radius: 6px;
  font-size: 1rem;
  box-sizing: border-box;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: #1a3c5e;
}

button {
  width: 100%;
  margin-top: 1rem;
  padding: 0.75rem;
  background: rgba(0, 0, 0, 0.873);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

button:hover:not(:disabled) {
  background: #1e1e27cd;
}

button:disabled {
  background: #242527;
  cursor: not-allowed;
}

.erreur {
  color: #c0392b;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

body{
  cursor: default;
}
</style> 
