<template>
  <div>
    <span v-for="error in errors" v-bind:key="error.key">
      {{ error.message }}
    </span>
    <div class="q-pa-md">
      <q-table
        title="accounts"
        flat
        dense
        :rows="accounts"
        :columns="columns"
        :filter="filter"
        :pagination="pagination"
        row-key="id"
      >
          <template v-slot:top>
            <div class="q-pr-lg text-h6">Deposit accounts</div>
            <q-btn to="/newaccount" round size="sm" color="primary" icon="person_add" @click="addRow" />
              <q-space />
              <q-input dense debounce="300" color="primary" v-model="filter">
                  <template v-slot:append>
                  <q-icon name="search" />
                  </template>
              </q-input>
          </template>


          <template v-slot:header="props">
            <q-tr :props="props">
              <q-th auto-width />
              <q-th
                v-for="col in props.cols"
                :key="col.name"
                :props="props"
              >
                {{ col.label }}
              </q-th>
            </q-tr>
          </template>

          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td auto-width>
                <q-btn :to="'/accounts/' + props.key" size="sm" color="primary" round dense @click="props.expand = !props.expand" icon="loupe" />
              </q-td>
              <q-td
                v-for="col in props.cols"
                :key="col.name"
                :props="props"
              >
                {{ col.value }}
              </q-td>
            </q-tr>
          </template>
      </q-table>
    </div>
  </div>
</template>

<script setup>
import { format } from 'quasar'
import { ref } from 'vue'

</script>

<script>
// Table config
const columns = [
  {
    name: 'account_no',
    required: true,
    label: 'Account Number',
    align: 'left',
    field: row => row.account_no,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'iban', label: 'IBAN', align: 'left', field: 'iban', sortable: true },
  { name: 'currency', label: 'Currency', align: 'left', field: 'currency', sortable: true },
  { name: 'name', label: 'Name', align: 'left', field: 'name', sortable: true },
  { name: 'state', label: 'State', align: 'left', field: row => row.deposit_accounts[0].state, sortable: true },
  { name: 'owner', label: 'Owner', align: 'left', field: row => row.accounts_clients[0].client.firstname + " " + row.accounts_clients[0].client.lastname, sortable: true }
]


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
     pagination: { rowsPerPage: "20"},
     filter: ref(''),
     accounts: [
      ],
      errors: null
    }
  },  
  
  created() {
    // fetch on init
    this.fetchData()
  },

  methods: {
    fetchData() {
      const { pad } = format  

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          //query: '{ accounts(order_by: {external_id: asc}) {id, firstname, lastname, email, external_id} }',
          //query: '{ accounts(order_by: {account_no: asc}, where: { type: {_eq: D}}) { id currency name iban deposit_acounts { product_deposit { name } state interest_rate } accounts_clients { client { firstname lastname }}}}'
          query: '{ accounts(where: {type: {_eq: D}}, order_by: {account_no: asc}) { deposit_accounts { interest_rate state } id account_no currency iban name accounts_clients { client { firstname lastname }}}}'
        }),
      })
      .then(function(response) {
        return response.json()
      })
      .then(data => {
        console.log(data);
        //data.data.allTask.forEach(function (task, index) {
        /*
        this.accounts = data.data.deposit_accounts
        this.accounts.external_id = pad(this.accounts.external_id,10)
        console.log(this.accounts)
        */

        for (let account of data.data.accounts) {
          console.log(account);
          this.accounts.push(account);
        }
      })
      .then(errors => this.errors = errors)
    },

    addRow() {
        // needs to be filled out
    },
  } 
} 
</script>