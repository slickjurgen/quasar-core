<template>
  <div class="q-pa-md">
    <div class="q-pr-lg text-h6">Client details</div>
    <div class="q-gutter-y-md column" style="max-width: 350px">
      <q-select v-model="client.title" square outlined dense :options="title" label="Title" />
      <q-input v-model="client.firstname" square outlined dense type="text" label="Firstname" />
      <q-input v-model="client.lastname" square outlined dense type="text" label="Lastname" />
      <q-select v-model="client.gender" square outlined dense :options="gender" label="Gender" />
      <q-input v-model="client.email" square outlined dense type="email" label="Email" />
      <q-input v-model="client.mobile" square outlined dense type="text" label="Mobile Phone" />
      <div class="q-pa-md q-gutter-sm"> 
        <q-btn no-caps to="/clients" color="secondary" label="Clients list" />
        <q-btn no-caps color="primary" label="Update" @click="updateClient" />
        <q-btn no-caps :to="'/newaccount/' + client.id" color="primary" label="Create account" />
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'quasar'
import { ref } from 'vue'

export default {
    props: ['client_id'],

    data() {
        return {
        client: ref(''),
        gender: [{value: 'male', label: 'Male'}, {value: 'female', label: 'Female'}, {value: 'divers', label: 'Divers'}, {value: 'unknown', label: 'Unknown'}],
        //gender: ['male', 'female', 'divers', 'unknown'],
        title: ['', 'dr', 'prof', 'profdr'],
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

            console.log(this.client_id)

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                query: '{ clients(where: {id: {_eq: "' + this.client_id + '"}} ) {id, firstname, lastname, email, mobile, external_id, gender, title} }',
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data);

                this.client = data.data.clients[0]
                // where should be only one result...
            })
        },        
        updateClient() {  
            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                query: 'mutation { update_clients(where: {id: {_eq: "' + this.client_id + '"}}, _set: {email: "' + this.client.email + 
                '", firstname: "' + this.client.firstname +
                '", gender: ' + this.client.gender.value +
                ', lastname: "' + this.client.lastname + 
                '", mobile: "' + this.client.mobile +
                '"}) { returning { id } affected_rows }}'
                })
            })        
            .then(function(response) {
                return response.json()
            })
            .then(data => console.log(data)) 
            // give info on success

            this.$q.notify('Client updated')

        },
    }
        
}
</script>
