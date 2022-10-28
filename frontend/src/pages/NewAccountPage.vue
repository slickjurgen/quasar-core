<template>
  <div class="q-pa-md">
    <div class="q-pr-lg text-h6">Create a new deposit account</div>
    <p>Please note, account id will be created automatically.</p>
    <div class="q-gutter-y-md column" style="max-width: 350px">
      <q-input v-model="account.account_no" square outlined dense type="text" label="Account number" mask="##########"/>
      <q-input v-model="account.iban" square outlined dense type="text" label="IBAN" mask="AA ## #### #### #### #### ##"/>
      <q-select v-model="account.currency" square outlined dense :options="currency" label="Currency" />
      <q-input v-model="account.name" square outlined dense type="text" label="Name" />
      <q-select v-model="deposit_account.product_id" square outlined dense :options="opt_products" label="Product" @change="account.interest_rate = 2" />
      <q-input v-model="deposit_account.interest_rate" square outlined dense type="text" label="Interest Rate" mask="#.##" fill-mask="0" reverse-fill-mask />

      <div class="q-pa-md q-gutter-sm">
        <q-btn no-caps color="secondary" label="Clear" @click="clearAccount" /> 
        <q-btn no-caps :to="'/clients/' + client_id" color="secondary" label="Back to client" />
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

  props: ['client_id'],
/*
  setup () {
    return {
      filter: ref(''),
      columns,
    }
  }, */

  created() {
    this.fetchProducts()
  },

  data() {
    return {
      products: [],
      opt_products: [],
      currency:[{ value: 'EUR', label: 'EUR'}],
      account: {},
      deposit_account: {},
      error: null
    }
  },  

  methods: {
    fetchProducts() {
      
      console.log("trying to fetch products...")

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: '{ product_deposits(where: {active: {_eq: true}, _and: {clients: {_eq: true}}}, order_by: {category: asc, type: asc, name: asc}) ' + 
            '{id account_balance_for_calc category interest_payed_into_account interest_days_in_year identifier interest_payed interest_rate_charged' +
            ' interest_rate_default interest_rate_max interest_rate_min interest_rate_terms name overdraft_allowed overdraft_interest_rate withhold_taxes type }}'        }),
      })
      .then(function(response) {
        return response.json()
      })
      .then(data => {
        console.log(data)

        for (let product of data.data.product_deposits) {
          console.log(product)
          this.products.push(product)
          this.opt_products.push({
              value: product.id,
              label: product.type + ' - ' + product.name
          }) 
        }
      })



    }, 
    saveAccount() {

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_accounts_one(object: { currency: "' + this.account.currency.value +
          '", iban: "' + this.account.iban +
          '", account_no: "' + this.account.account_no +
          '", name: "' + this.account.name +
          '", type: D }) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => {

        console.log(data) 
        // give info on success
        this.$q.notify('Account created')

        this.saveDepositAccount(data.data.insert_accounts_one.id)
        this.linkAccountToClient(data.data.insert_accounts_one.id)
        // clear data
        this.clearAccount()
      })
    },
    saveDepositAccount(id) {

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_deposit_accounts_one(object: { product_id: "' + this.deposit_account.product_id.value + 
          '", state: pendingapproval, interest_rate: "' + this.deposit_account.interest_rate +
          '", account_id: "' + id +
          '"}) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => {

        console.log(data) 
        // give info on success
        this.$q.notify('Deposit account created')
      })
    },
    linkAccountToClient(id) {

        fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_accounts_clients_one(object: { account_id: "' + id + 
          '", client_id: "' + this.client_id +
          '"}) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => console.log(data)) 
      // give info on success
      this.$q.notify('Account linked to client')  
    },
    clearAccount() {
      //this.account = ref('')
    }
  }
} 
</script>