<template>
  <div class="q-pa-md">
    <q-table
      title="Clients"
      flat
      dense
      :rows="clients"
      :columns="columns"
      :filter="filter"
      :pagination="pagination"
      row-key="id"
    >
        <template v-slot:top>
            <div class="q-pr-lg text-h6">Clients</div>
           <q-btn to="/newclient" round size="sm" color="primary" icon="person_add" @click="addRow" />
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
              <q-btn :to="'/clients/' + props.key" size="sm" color="primary" round dense @click="props.expand = !props.expand" icon="loupe" />
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
    name: 'external_id',
    required: true,
    label: 'Customer ID',
    align: 'left',
    field: row => row.external_id,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'firstname', label: 'Firstname', align: 'left', field: 'firstname', sortable: true },
  { name: 'lastname', label: 'Lastname', align: 'left', field: 'lastname', sortable: true },
  { name: 'email', label: 'E-Mail', align: 'left', field: 'email' }
 // { name: 'id', label: 'UUID', align: 'left', field: 'id' }
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
     clients: [
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
          query: '{ clients(order_by: {external_id: asc}) {id, firstname, lastname, email, external_id} }',
        }),
      })
      .then(function(response) {
        return response.json()
      })
      .then(data => {
        console.log(data);
        //data.data.allTask.forEach(function (task, index) {
        for (let client of data.data.clients) {
          console.log(client);
          this.clients.push({
            id: client.id,
            firstname: client.firstname,              
            lastname: client.lastname,
            email: client.email,
            external_id: pad(client.external_id,7),
          });
        }
      })
    },

    addRow() {
        // needs to be filled out
    },
  } 
} 
</script>