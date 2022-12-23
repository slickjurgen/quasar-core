<template>
    <q-btn-dropdown no-caps dense outline size="sm" color="secondary" :label="state ? state : current_state">
      <q-list>
        <q-item clickable v-close-popup @click="setState('approved')">
          <q-item-section>
            <q-item-label>Approve</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable v-close-popup @click="setState('active')">
          <q-item-section>
            <q-item-label>Activate</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable v-close-popup @click="setState">
          <q-item-section>
            <q-item-label>Another state</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
</template>

<script>
import { ref } from 'vue'

export default {
    props: ['id', 'current_state'],

    setup() {

    },

    created() {
        this.state = this.current_state
    },

    data() {
        return {
            state: ref(''),
        }
    },

    methods: {
        setState(new_state) {

            console.log(this.id)

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                query: 'mutation { update_deposit_accounts_by_pk(pk_columns: {id: "' + this.id + '"}, _set: {state: ' + new_state + '}) { id }}'
                })
            })        
            .then(function(response) {
                return response.json()
            })
            .then(data => {

                console.log(data) 
                // give info on success
                this.$q.notify('State set to ' + new_state)
                this.state = new_state
            }) 
        }
    }
}
</script>
