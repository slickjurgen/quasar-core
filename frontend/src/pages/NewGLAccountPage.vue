<template>
  <div class="q-pa-md">
    <div class="q-pr-lg text-h6">Create a new general ledger account</div>
    <p></p>
    <div class="q-gutter-y-md column" style="max-width: 350px">
      <q-input v-model="account.account_no" square outlined dense type="text" label="Account number" mask="##########"/>
      <q-select v-model="account.currency" square outlined dense :options="currency" label="Currency" />
      <q-input v-model="account.name" square outlined dense type="text" label="Name" />
      <q-input v-model="glaccount.gl_code" square outlined dense label="Code" />
      <q-select v-model="glaccount.type" square outlined dense :options="gl_type" label="Type" />
      <q-select v-model="glaccount.usage" square outlined dense :options="usage" label="Usage" />
      <q-toggle v-model="allow_manual_entries" label="Allow manual entries" />
      <q-input v-model="glaccount.descr" square outlined dense type="text" label="Description" />

      <div class="q-pa-md q-gutter-sm">
        <q-btn no-caps color="secondary" label="Clear" @click="clearAccount" /> 
        <q-btn no-caps to="/glaccounts" color="secondary" label="GL Accounts list" />
        <q-btn no-caps color="primary" label="Save" @click="saveAccount" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { format } from 'quasar'
import { ref } from 'vue'

</script>

<script>

export default {
/*
  setup () {
    return {
      filter: ref(''),
      columns,
    }
  }, */

  data() {
    return {
     account: {},
     glaccount: {},
      gl_type: [{ value: 'Liability', label: 'Liability'}, { value: 'Asset', label: 'Asset'}, { value: 'Income', label: 'Income'}, { value: 'Expense', label: 'Expense'}, { value: 'Equity', label: 'Equity'}],
      usage:[{ value: 'Detail', label: 'Detail'}],
      currency:[{ value: 'EUR', label: 'EUR'}],
      allow_manual_entries: ref(false),
      error: null,
    }
  },  

  methods: { 
    saveAccount() {

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_accounts_one(object: { currency: "' + this.account.currency.value +
          '", account_no: "' + this.account.account_no +
          '", name: "' + this.account.name +
          '", type: G }) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => {

        console.log(data) 
        // give info on success
        this.$q.notify('Account created')

        this.saveGLAccount(data.data.insert_accounts_one.id)
        // clear data
        //this.clearAccount()
      })
    },
    saveGLAccount(id) { 

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_gl_accounts_one(object: {gl_code: "' + this.glaccount.gl_code + 
          '", type: "' + this.glaccount.type.value +
          '", usage: "' + this.glaccount.usage.value +           
          '", allow_manual_entries: ' + this.allow_manual_entries +           
          ', account_id: "' + id + 
          '", descr: "' + this.glaccount.descr +
          '"}) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => console.log(data)) 
      // give info on success
      this.$q.notify('GL Account created')
      // clear data
    },
    clearAccount() {
      this.account = ref('')
    }
  }
} 
</script>