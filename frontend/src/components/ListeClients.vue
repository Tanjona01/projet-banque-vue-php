<template>
  <div>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <span class="navbar-brand">Banque SPA</span>
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

    <div class="container mt-4">

      
      <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-danger']" role="alert">
        {{ message }}
      </div>

      
      <div class="card">
        <div class="card-header">
          <b>Liste des clients</b>
        </div>
        <div class="card-body">

        
          <div class="row mb-3">
            <div class="col-md-10">
              <input
                type="text"
                v-model="recherche"
                class="form-control"
                placeholder="Rechercher par nom ou numéro de compte"
              />
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary w-100" @click="() => {}">Rechercher</button>
            </div>
          </div>


          <p v-if="chargement" class="text-muted fst-italic">Chargement…</p>
          <p v-if="!chargement && clientsFiltres.length === 0" class="text-muted fst-italic">Aucun client trouvé.</p>

     
          <table v-if="!chargement && clientsFiltres.length > 0" class="table table-bordered">
            <thead>
              <tr>
                <th>N° Compte</th>
                <th>Nom</th>
                <th>Solde (Ar)</th>
                <th>Statut</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in clientsFiltres" :key="client.numCompte">
                <td>{{ client.numCompte }}</td>
                <td>{{ client.nom }}</td>
                <td :class="client.solde < 0 ? 'text-danger fw-bold' : 'text-success fw-bold'">
                  {{ formatSolde(client.solde) }}
                </td>
                <td>
                  <span :class="badgeClass(client.statut)">{{ client.statut }}</span>
                </td>
                <td>
                  <button
                    class="btn btn-warning btn-sm me-1"
                    @click="ouvrirModal(client)"
                    data-bs-toggle="modal"
                    data-bs-target="#editModal"
                  >Modifier</button>
                  <button
                    class="btn btn-danger btn-sm"
                    @click="supprimerClient(client.numCompte, client.nom)"
                  >Supprimer</button>
                </td>
              </tr>
            </tbody>
          </table>

        </div>
      </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier client</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nom</label>
              <input type="text" v-model="editForm.nom" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Solde (€)</label>
              <input type="number" step="0.01" v-model="editForm.solde" class="form-control" required />
            </div>
            <div class="mb-2">
              <label class="form-label">Statut prévu</label><br/>
              <span :class="badgeClass(calculerStatut(editForm.solde))">
                {{ calculerStatut(editForm.solde) }}
              </span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-success" @click="sauvegarder">Mettre à jour</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>


<script setup>
const bootstrap = window.bootstrap

import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import API from '../api.js'

const router      = useRouter()
const clients     = ref([])
const chargement  = ref(false)
const message     = ref('')
const messageType = ref('success')
const recherche   = ref('')

const editForm = ref({ numCompte: null, nom: '', solde: 0 })


const clientsFiltres = computed(() => {
  const q = recherche.value.toLowerCase().trim()
  if (!q) return clients.value
  return clients.value.filter(c =>
    c.nom.toLowerCase().includes(q) ||
    String(c.numCompte).includes(q)
  )
})

function calculerStatut(solde) {
  const s = parseFloat(solde)
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
  return parseFloat(solde).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' Ar'
}

function afficherMessage(texte, type = 'success') {
  message.value     = texte
  messageType.value = type
  setTimeout(() => { message.value = '' }, 3500)
}

async function chargerClients() {
  chargement.value = true
  try {
    const res = await axios.get(API.getClients)
    if (res.data.success) clients.value = res.data.data
  } catch {
    afficherMessage('Erreur lors du chargement des clients.', 'error')
  } finally {
    chargement.value = false
  }
}

function ouvrirModal(client) {
  editForm.value = { numCompte: client.numCompte, nom: client.nom, solde: parseFloat(client.solde) }
}

async function sauvegarder() {
  if (!editForm.value.nom.trim()) {
    afficherMessage('Le nom ne peut pas être vide.', 'error')
    return
  }
  try {
    const res = await axios.put(API.updateClient, {
      numCompte: editForm.value.numCompte,
      nom:       editForm.value.nom.trim(),
      solde:     parseFloat(editForm.value.solde)
    })
    if (res.data.success) {
   
      const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'))
      modal.hide()
      afficherMessage('Client mis à jour avec succès.')
      await chargerClients()
    } else {
      afficherMessage(res.data.message || 'Erreur lors de la mise à jour.', 'error')
    }
  } catch (err) {
    afficherMessage(err.response?.data?.message || 'Erreur serveur.', 'error')
  }
}

async function supprimerClient(numCompte, nom) {
  if (!confirm(`Supprimer le client « ${nom} » (N° ${numCompte}) ?`)) return
  try {
    const res = await axios.delete(API.deleteClient, { data: { numCompte } })
    if (res.data.success) {
      afficherMessage('Client supprimé avec succès.')
      await chargerClients()
    } else {
      afficherMessage(res.data.message || 'Erreur lors de la suppression.', 'error')
    }
  } catch (err) {
    afficherMessage(err.response?.data?.message || 'Erreur serveur.', 'error')
  }
}

function deconnecter() {
  sessionStorage.removeItem('user')
  router.push('/')
}

onMounted(chargerClients)
</script>

<style scoped>
body { cursor: default; }

.navbar .text-white:hover {
  text-decoration: underline !important;
}

.navbar .router-link-active {
  font-weight: bold;
  text-decoration: underline !important;
}
</style>
