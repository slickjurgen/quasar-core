<template>
  <div class="q-pa-md">
    <div class="q-pr-lg text-h6">Create a new client</div>
    <p>Please note, customer id will be created automatically.</p>
    <div class="q-gutter-y-md column" style="max-width: 350px">
      <q-select v-model="client.title" square outlined dense :options="title" label="Title" />
      <q-input v-model="client.firstname" square outlined dense type="text" label="Firstname" />
      <q-input v-model="client.lastname" square outlined dense type="text" label="Lastname" />
      <q-select v-model="client.gender" square outlined dense :options="gender" label="Gender" />
      <q-input v-model="client.email" square outlined dense type="email" label="Email" />
      <q-input v-model="client.mobile" square outlined dense type="text" label="Mobile Phone" />

      <div class="q-pa-md q-gutter-sm">
        <q-btn no-caps color="secondary" label="Clear" @click="clearClient" /> 
        <q-btn no-caps to="/clients" color="secondary" label="Clients list" />
        <q-btn no-caps color="primary" label="Save" @click="saveClient" />
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
     client: [
      ],
      gender: ['male', 'female', 'divers', 'unknown'],
      title:['', 'dr', 'prof', 'profdr'],
      error: null
    }
  },  

  methods: { 
    saveClient() { 

      fetch('http://localhost:8080/v1/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          query: 'mutation { insert_clients_one(object: {email: "' + this.client.email + 
          '", firstname: "' + this.client.firstname +
          '", gender: ' + this.client.gender +
          ', lastname: "' + this.client.lastname + 
          '", mobile: "' + this.client.mobile +
          '"}) { id  }}'
        })
      })        
      .then(function(response) {
        return response.json()
      })
      .then(data => console.log(data)) 
      // give info on success
      this.$q.notify('Client created')
      // clear data
      this.clearClient()
    },
    clearClient() {
      this.client = ref('')
    }
  }
} 
</script>