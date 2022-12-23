<template>

  <div class="q-pa-md">
    <div class="q-pr-lg text-h6">{{ account.account_no }} - {{ account.name }}</div>
    <div class="q-gutter-y-md" style="max-width: 600px">
      <q-card flat>
        <q-tabs
          v-model="tab"
          dense
          class="text-grey"
          active-color="primary"
          indicator-color="primary"
          align="justify"
          narrow-indicator
        >
          <q-tab name="details" label="Account details" />
          <q-tab name="transactions" label="Transactions" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="tab">
          <q-tab-panel name="details">
            <div class="text-h6">Account details</div>
                <div class="row">
                <div class="col-sm-9">
                    <p>Total Balance {{ parseFloat(balance).toFixed(2) }} {{ account.currency }}</p>
                    <div class="row">
                    <div class="col-8 col-sm-6">
                        Name
                    </div>
                    <div class="col-4 col-sm-6">
                        {{ account.name }}
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-8 col-sm-6">
                        Type
                    </div>
                    <div class="col-4 col-sm-6">
                        {{ account.type }}
                    </div>
                    </div>
                </div>
            </div>
          </q-tab-panel>
            <q-tab-panel name="transactions">
                <TransactionList :id="this.id" />
            </q-tab-panel>  
        </q-tab-panels>

      </q-card>
    </div>
  </div>

</template>

<script>
import TransactionList from '../components/TransactionList.vue'

import { ref } from 'vue'

export default {
    components: {
        TransactionList,
    },
    props: ['id'],

    created() {
    // fetch on init
        this.fetchData()
        this.fetchBalance()
    },

    data() {
        return {
        account: ref(''),
        balance: ref(''),
        tab: ref('details'),
        }
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
                    query: '{ accounts_by_pk (id: "' + this.id + '") { id type currency account_no name iban deposit_accounts { interest_rate state }}}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data);

                // where should be only one account
                this.account = JSON.parse(JSON.stringify(data.data.accounts_by_pk))
                console.log(this.account);

            })
        },

        fetchBalance() {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                    query: '{ account_balances(where: {account_id: {_eq: "' + this.id + '"}}) { balance }}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data);

                // where should be only one balance
                if(data.data.account_balances.length == 0) {
                  this.balance = 0
                } else {
                  console.log(data.data.account_balances[0].balance)
                  this.balance = data.data.account_balances[0].balance
                }
                console.log(this.balance)
            })

        },
    }    
} 

</script>