<template>
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
            <div class="q-pr-lg text-h6">General Ledger accounts</div>
           <q-btn to="/newglaccount" round size="sm" color="primary" icon="person_add" @click="addRow" />
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
              <q-btn :to="'/glaccounts/' + props.key" size="sm" color="primary" round dense @click="props.expand = !props.expand" icon="loupe" />
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
  { name: 'gl_code', label: 'GL Code', align: 'left', field: row => row.gl_accounts[0].gl_code, sortable: true },
  { name: 'name', label: 'Name', align: 'left', field: 'name', sortable: true },
  { name: 'type', label: 'Type', align: 'left', field: row => row.gl_accounts[0].type, sortable: true },  
  { name: 'usage', label: 'Usage', align: 'left', field: row => row.gl_accounts[0].usage, sortable: true },
  { name: 'currency', label: 'Currency', align: 'left', field: 'currency', sortable: true },
  { name: 'allow_manual_entries', label: 'Allow Manual Entries', align: 'left', field: row => row.gl_accounts[0].allow_manual_entries, sortable: true },
  { name: 'descr', label: 'Description', align: 'left', field: row => row.gl_accounts[0].descr, sortable: true }
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
      error: null
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
          query: '{ accounts(where: {type: {_eq: G}}) { id account_no currency name gl_accounts { gl_code descr allow_manual_entries type usage }}}'
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
    },

    addRow() {
        // needs to be filled out
    },
  } 
} 
</script>