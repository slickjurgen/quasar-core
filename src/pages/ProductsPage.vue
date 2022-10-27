<template>
  <div class="q-pa-md">
    <q-table
      title="Deposit products"
      flat
      dense
      :rows="products"
      :columns="columns"
      :filter="filter"
      row-key="id"
    >
      <template v-slot:top>
          <div class="q-pr-lg text-h6">Deposit products</div>
          <q-btn round color="primary" icon="group_add" @click="addRow" />
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
            <q-btn size="sm" color="accent" round dense @click="props.expand = !props.expand" :icon="props.expand ? 'expand_less' : 'expand_more'" />
          </q-td>
          <q-td
            v-for="col in props.cols"
            :key="col.name"
            :props="props"
          >
            {{ col.value }}
          </q-td>
        </q-tr>
        <q-tr v-show="props.expand" :props="props">
          <q-td colspan="100%">
            <div class="text-left">{{ props.row.description }}</div>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script setup>
import { ref } from 'vue'

</script>

<script>
// Table config
const columns = [
  {
    name: 'name',
    required: true,
    label: 'Product name',
    align: 'left',
    field: row => row.name,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'active', label: 'State', align: 'left', field: 'active', format: val => val ? "\u2611 activated" : "\u2610 deactivated", sortable: true },   
  { name: 'category', label: 'Category', align: 'left', field: 'category', sortable: true },
  { name: 'type', label: 'Type', align: 'left', field: 'type', sortable: true }

]


export default {

  data() {
    return {
     filter: ref(''),
     products: [
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

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: '{ product_deposits(order_by: {identifier: asc}) {id identifier name description active category type} }',
        }),
      })
      .then(function(response) {
        return response.json()
      })
      .then(data => {
        console.log(data);
        //data.data.allTask.forEach(function (task, index) {
        for (let product of data.data.product_deposits) {
          console.log(product);
          this.products.push({
            id: product.id,
            identifier: product.identifier,
            name: product.name,
            description: product.description,
            active: product.active,
            category: product.category,              
            type: product.type,
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