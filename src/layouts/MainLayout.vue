<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Bank OS 
        </q-toolbar-title>
<!--
        <div>Bank OS v{{ $q.version }}</div>
-->
        <q-space />

        <q-btn-dropdown stretch flat label="Create">
          <q-list>
            <q-item clickable v-close-popup to="/newclient">
              <q-item-section avatar>
                <q-avatar icon="person_add" color="primary" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Client</q-item-label>
                <q-item-label caption>Create a new client</q-item-label>
              </q-item-section>
            </q-item>
            <q-item clickable v-close-popup>
              <q-item-section avatar>
                <q-avatar icon="group_add" color="primary" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Group</q-item-label>
                <q-item-label caption>Create a new group</q-item-label>
              </q-item-section>
            </q-item>

<!--
            <q-separator inset spaced />
            <q-item v-if="this.client_id" clickable v-close-popup :to="'/newaccount/' + this.client_id">
              <q-item-section avatar>
                <q-avatar icon="savings" color="primary" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Deposit Account</q-item-label>
                <q-item-label caption>Create a new deposit account</q-item-label>
              </q-item-section>
            </q-item>
-->
          </q-list>
        </q-btn-dropdown>

        <div>The smart and lightweight core banking operating system - Bank OS v0.0.1</div>
      </q-toolbar>
<!--
      <q-toolbar inset>
        <q-breadcrumbs active-color="white" style="font-size: 16px">
          <q-breadcrumbs-el label="Home" icon="home" to="/" />
          <q-breadcrumbs-el label="Clients" icon="person" to="/clients" />
        </q-breadcrumbs>
      </q-toolbar>
-->
    </q-header>

      <q-drawer
        v-model="leftDrawerOpen"
        show-if-above
        :width="250"
        :breakpoint="600"
      >
        <q-scroll-area style="height: calc(100% - 50px); margin-top: 50px; border-right: 1px solid #ddd">
          <q-list padding>
            <q-item
              to="/clients"
              exact 
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="person" />
              </q-item-section>

              <q-item-section>
                Clients
              </q-item-section>
            </q-item>

            <q-item 
              to="/groups"
              exact
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="group" />
              </q-item-section>

              <q-item-section>
                Groups
              </q-item-section>
            </q-item>            
            
            <q-item 
              to="/accounts"
              exact
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="savings" />
              </q-item-section>

              <q-item-section>
                Deposit Accounts
              </q-item-section>
            </q-item> 

            <q-item 
              to="/products"
              exact
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="show_chart" />
              </q-item-section>

              <q-item-section>
                Deposit Products
              </q-item-section>
            </q-item> 
            
            <q-separator inset />

            <q-item 
              to="/glaccounts"
              exact
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="account_balance" />
              </q-item-section>

              <q-item-section>
                General Ledger
              </q-item-section>
            </q-item> 

            <q-separator inset />

           
            
            <q-item 
              to="/settings"
              exact
              clickable 
              v-ripple>
              <q-item-section avatar>
                <q-icon name="settings" />
              </q-item-section>

              <q-item-section>
                Settings
              </q-item-section>
            </q-item>            

          </q-list>
        </q-scroll-area>
      </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
//import EssentialLink from 'components/EssentialLink.vue'

export default defineComponent({
  name: 'MainLayout',

  components: {
    //EssentialLink
  },

  setup () {
    const leftDrawerOpen = ref(false)

    return {
      leftDrawerOpen,
      toggleLeftDrawer () {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  }
})
</script>
