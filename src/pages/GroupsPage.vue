<template>
  <div class="q-pa-md">
    <q-table
      title="Groups"
      flat
      dense
      :rows="groups"
      :columns="columns"
      :filter="filter"
      row-key="id"
    >
      <template v-slot:top>
          <div class="q-pr-lg text-h6">Groups</div>
          <q-btn round size="sm" color="primary" icon="group_add" @click="addRow" />
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
            <div class="text-left">List of the clients associated with group {{ props.row.name }}</div>
            <ul>
              <li v-for="member in props.row.members" v-bind:key="member.clients.id">
                  {{ member.clients.firstname }} {{ member.clients.lastname }} - Status "{{ member.role }}"
              </li>
            </ul>
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
    label: 'Group Name',
    align: 'left',
    field: row => row.name,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'type', label: 'Type',align: 'left', field: 'type', sortable: true }
]


export default {

  data() {
    return {
     filter: ref(''),
     groups: [
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
          query: '{ groups(order_by: {name: asc}) {group_members {clients {firstname lastname id} role} id name type} }',
        }),
      })
      .then(function(response) {
        return response.json()
      })
      .then(data => {
        console.log(data);
        //data.data.allTask.forEach(function (task, index) {
        for (let group of data.data.groups) {
          console.log(group);
          this.groups.push({
            id: group.id,
            name: group.name,              
            type: group.type,
            members: group.group_members,
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