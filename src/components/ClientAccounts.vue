<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
<!--
        <div class="q-pr-lg text-h6">Client accounts</div>

        <q-btn to="/newaccount" round size="xs" color="primary" icon="add" @click="addRow" />
-->
        <div class="q-pa-md q-gutter-md">
            <q-list padding class="rounded-borders" style="max-width: 350px">
            <q-item-label header>Client accounts</q-item-label>


            <q-item v-for="account in accounts" v-bind:key="account.id" clickable v-ripple :to="'/accounts/' + account.id">
                <q-item-section avatar top>
                <q-avatar icon="folder" color="primary" text-color="white" />
                </q-item-section>

                <q-item-section>
                <q-item-label lines="1">{{ account.iban }}</q-item-label>
                <q-item-label caption>{{ account.name }}</q-item-label>
                </q-item-section>

                <q-item-section side top>
                    <q-item-label caption>{{ account.currency }}</q-item-label>
                    <q-icon name="info" color="green" />
                </q-item-section>
            </q-item>

            </q-list>
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
        accounts: [],
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
                    // (where: {deposit_accounts_clients: {client_id: {_eq: "c7df75c7-f858-41d3-b400-d5da4ae8653d"}}})
                //query: '{ clients(where: {id: {_eq: "' + this.client_id + '"}} ) {id, firstname, lastname, email, mobile, external_id, gender, title} }',
                //query: '{ clients(where: {id: {_eq: "' + this.client_id + '"}}) { deposit_accounts_clients { deposit_account { product_deposit { name } interest_rate external_id active }}} deposit_accounts_clients { id }}',
                query: '{ accounts (where: {accounts_clients: {client_id: {_eq: "' + this.client_id + '"}}}) { id currency name iban }}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data);

                for (let account of data.data.accounts) {
                    console.log(account);
                    this.accounts.push(account);
                }   
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
        addRow() {},
    }
        
}
</script>
