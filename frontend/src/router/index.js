import { createRouter, createWebHistory } from 'vue-router'

import Login        from '../components/Login.vue'
import AjoutClient  from '../components/AjoutClient.vue'
import ListeClients from '../components/ListeClients.vue'
import BilanGraphe  from '../components/BilanGraphe.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: Login,
      meta: { public: true }
    },
    {
      path: '/ajout',
      component: AjoutClient,
      meta: { requiresAuth: true }
    },
    {
      path: '/liste',
      component: ListeClients,
      meta: { requiresAuth: true }
    },
    {
      path: '/bilan',
      component: BilanGraphe,
      meta: { requiresAuth: true }
    },
    // Redirection fallback
    { path: '/:pathMatch(.*)*', redirect: '/' }
  ]
})

// Garde de navigation : protéger les routes authentifiées
router.beforeEach((to, _from, next) => {
  const user = sessionStorage.getItem('user')
  if (to.meta.requiresAuth && !user) {
    next('/')
  } else if (to.path === '/' && user) {
    // Déjà connecté → aller directement à la liste
    next('/liste')
  } else {
    next()
  }
})

export default router
