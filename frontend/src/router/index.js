import { createRouter, createWebHistory } from 'vue-router'

import Login from '../components/Login.vue'
import AjoutClient from '../components/AjoutClient.vue'
import ListeClients from '../components/ListeClients.vue'
import BilanGraphe from '../components/BilanGraphe.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: Login
    },
    {
      path: '/ajout',
      component: AjoutClient
    },
    {
      path: '/liste',
      component: ListeClients
    },
    {
      path: '/bilan',
      component: BilanGraphe
    }
  ]
})

export default router