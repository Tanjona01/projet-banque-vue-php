<template>
  <div>
   
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <span class="navbar-brand"> Banque SPA</span>
      </div>
      <div class="container flex-grow-1 d-flex">
        <div class="d-flex gap-4">
          <router-link class="text-white text-decoration-none" to="/liste">Liste clients</router-link>
          <router-link class="text-white text-decoration-none" to="/ajout">Ajouter un client</router-link>
          <router-link class="text-white text-decoration-none" to="/bilan">Bilan</router-link>
        </div>
        <div class="ms-auto">
          <button class="btn btn-outline-danger btn-sm" @click="deconnecter">Déconnexion</button>
        </div>
      </div>
    </nav>

    <button class="btn btn-primary" onclick="history.back()" id="retour">&leftarrow; Retour</button>

    <div class="container mt-4">

 
      <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
        {{ message }}
      </div>

   
      <div class="card">
        <div class="card-header">
          <b>Ajouter un client</b>
        </div>
        <div class="card-body">
          <form @submit.prevent="ajouterClient">
            <div class="mb-3">
              <label class="form-label">Nom</label>
              <input
                type="text"
                v-model="form.nom"
                class="form-control"
                placeholder="ex. : Marie Dupont"
                required
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Solde initial (Ar)</label>
              <input
                type="number"
                step="0.01"
                v-model="form.solde"
                class="form-control"
                placeholder="ex. : 1500.00"
                required
              />
            </div>

          
            <div v-if="form.solde !== ''" class="mb-3">
              <label class="form-label">Statut prévu</label><br/>
              <span :class="badgeClass(calculerStatut(form.solde))">
                {{ calculerStatut(form.solde) }}
              </span>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success" :disabled="chargement">
                {{ chargement ? 'Ajout en cours…' : 'Ajouter' }}
              </button>
              <button type="button" class="btn btn-secondary" @click="reinitialiser">
                Réinitialiser
              </button>
            </div>
          </form>
        </div>
      </div>

      <br/>


      <div class="card" v-if="derniersAjouts.length > 0">
        <div class="card-header">
          <b>Clients ajoutés cette session</b>
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>N° Compte</th>
                <th>Nom</th>
                <th>Solde</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="c in derniersAjouts" :key="c.numCompte">
                <td>{{ c.numCompte }}</td>
                <td>{{ c.nom }}</td>
                <td>{{ formatSolde(c.solde) }}</td>
                <td><span :class="badgeClass(c.statut)">{{ c.statut }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import API from '../api.js'

const router = useRouter()
const form   = ref({ nom: '', solde: '' })
const chargement    = ref(false)
const message       = ref('')
const messageType   = ref('success')
const derniersAjouts = ref([])

function calculerStatut(solde) {
  const s = parseFloat(solde)
  if (isNaN(s))               return ''
  if (s < 1000)               return 'insuffisant'
  if (s >= 1000 && s <= 5000) return 'moyen'
  return 'élevé'
}

function badgeClass(statut) {
  if (statut === 'insuffisant') return 'badge bg-danger'
  if (statut === 'moyen')       return 'badge bg-warning text-dark'
  return 'badge bg-success'
}

function formatSolde(solde) {
  return parseFloat(solde).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' €'
}

function afficherMessage(texte, type = 'success') {
  message.value     = texte
  messageType.value = type
  setTimeout(() => { message.value = '' }, 3500)
}

function reinitialiser() {
  form.value = { nom: '', solde: '' }
}

async function ajouterClient() {
  if (!form.value.nom.trim()) {
    afficherMessage('Le nom est requis.', 'error')
    return
  }
  if (form.value.solde === '' || isNaN(parseFloat(form.value.solde))) {
    afficherMessage('Le solde doit être un nombre valide.', 'error')
    return
  }

  chargement.value = true
  try {
    const solde = parseFloat(form.value.solde)
    const res   = await axios.post(API.addClient, { nom: form.value.nom.trim(), solde })

    if (res.data.success) {
      afficherMessage(`Client ajouté avec succès (N° de compte : ${res.data.numCompte}).`)
      derniersAjouts.value.unshift({
        numCompte: res.data.numCompte,
        nom:       form.value.nom.trim(),
        solde,
        statut:    calculerStatut(solde)
      })
      reinitialiser()
    } else {
      afficherMessage(res.data.message || "Erreur lors de l'ajout.", 'error')
    }
  } catch (err) {
    afficherMessage(err.response?.data?.message || 'Erreur serveur.', 'error')
  } finally {
    chargement.value = false
  }
}

function deconnecter() {
  sessionStorage.removeItem('user')
  router.push('/')
}
</script>

<style scoped>
#retour {
  margin-top: 10px;
  margin-left: 5px;
  height: 40px;
  background: transparent;
  color: black;
  border-color: white;
}
#retour:hover {
  box-shadow: 0 5px 15px rgba(6, 6, 7, 0.2);
  transform: translateY(-3px);
  transition: 0.3s;
}
.navbar .router-link-active {
  font-weight: bold;
  text-decoration: underline !important;
  cursor: default;
}

</style>

<style>

body{
  cursor: default;
}
</style>
