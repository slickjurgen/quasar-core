
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      { path: '/newclient', component: () => import('pages/NewClientPage.vue') },
      { path: '/clients', component: () => import('pages/ClientsPage.vue') },  
      { path: '/clients/:client_id', component: () => import('pages/ClientsOverviewPage.vue'), props: true },
      { path: '/groups', component: () => import('pages/GroupsPage.vue') },
      { path: '/newaccount/:client_id', component: () => import('pages/NewAccountPage.vue'), props: true },
      { path: '/accounts', component: () => import('pages/DepositAccountsPage.vue') },      
      { path: '/accounts/:id', component: () => import('pages/AccountsOverviewPage.vue'), props: true },
      { path: '/newglaccount', component: () => import('pages/NewGLAccountPage.vue') },      
      { path: '/glaccounts', component: () => import('pages/GLAccountsPage.vue') },      
      { path: '/glaccounts/:id', component: () => import('pages/GLAccountsOverviewPage.vue'), props: true },      
      { path: '/products', component: () => import('pages/ProductsPage.vue') },
      { path: '/settings', component: () => import('pages/SettingsPage.vue') }
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
