// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import AjoutClient from '../components/AjoutClient.vue'
import ListeClients from '../components/ListeClients.vue'
import BilanGraphe from '../components/BilanGraphe.vue'

const routes = [
  { path: '/',        component: Login },
  { path: '/ajout',  component: AjoutClient,  meta: { requiresAuth: true } },
  { path: '/liste',  component: ListeClients,  meta: { requiresAuth: true } },
  { path: '/bilan',  component: BilanGraphe,   meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Protection des pages — redirige vers login si pas connecté
router.beforeEach((to, from, next) => {
  const connecte = sessionStorage.getItem('connecte')
  if (to.meta.requiresAuth && !connecte) {
    next('/')
  } else {
    next()
  }
})

export default router