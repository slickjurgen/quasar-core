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
          <q-tab name="interest" label="Interest" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="tab">
          <q-tab-panel name="details">
            <div class="row text-h6">
              <div class="col">Account details</div><div class="col"><DepositAccountState :id="deposit_account.id" :current_state="deposit_account.state" /></div>
            </div>
            <div class="row">
              <div class="col-6">Total Balance</div>
              <div class="col-6">{{ parseFloat(balance).toFixed(2) }} {{ account.currency }}</div> 
            </div>
            <div class="row">
              <div class="col-8 col-sm-6">Account type</div>
              <div class="col-4 col-sm-6">{{ account.type }}</div>
            </div>
            <div class="row">
              <div class="col-8 col-sm-6">Product type</div>
              <div class="col-4 col-sm-6">{{ deposit_account.product_deposit.type }}</div>
            </div>
            <div class="row">
              <div class="col-8 col-sm-6">Maturity</div>
              <div class="col-4 col-sm-6">{{ deposit_account.maturity }}</div>
            </div>
            <div class="row">
              <div class="col-8 col-sm-6">IBAN</div>
              <div class="col-4 col-sm-6">{{ account.iban }}</div>
            </div>
            <div class="row">
              <div class="col-8 col-sm-6">Interest rate</div>
              <div class="col-4 col-sm-6">{{ parseFloat(deposit_account.interest_rate).toFixed(2)+" %" }}</div>
            </div>
            <div class="q-pa-md q-gutter-sm"> 
                <q-btn no-caps to="" color="primary" label="Deposit" @click="make_deposit = true" />
                <q-btn v-if="deposit_account.state == 'pendingapproval' || deposit_account.state == 'approved' || deposit_account.product_deposit.type == 'Current Account'" no-caps to="" color="primary" label="Withdraw" @click="make_withdrawal = true" />
                <BeginMaturityPeriod :id="this.id" :state="deposit_account.state" :balance="balance" :interest_rate="deposit_account.interest_rate" v-if="!deposit_account.maturity && deposit_account.product_deposit.type != 'Current Account'" />
                <q-btn no-caps to="" color="primary" label="Transfer" />
            </div>
          </q-tab-panel>

          <q-tab-panel name="transactions">
                <TransactionList :id="this.id" />
          </q-tab-panel>

          <q-tab-panel name="interest">
                <DepositInterestList :id="this.id" />
          </q-tab-panel>
        </q-tab-panels>
      </q-card>
    </div>
    <q-dialog v-model="make_deposit" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Make deposit</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input dense v-model="deposit" autofocus @keyup.enter="prompt = false" />

          <q-select dense v-model="channel" :options="opt_channels" label="Transaction Channel" />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Save deposit" v-close-popup @click="setDeposit" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="make_withdrawal" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Make withdrawal</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input dense v-model="withdrawal" autofocus @keyup.enter="prompt = false" />

          <q-select dense v-model="channel" :options="opt_channels" label="Transaction Channel" />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Withdraw" v-close-popup @click="setWithdrawal" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import TransactionList from '../components/TransactionList.vue'
import DepositAccountState from '../components/DepositAccountState.vue'
import BeginMaturityPeriod from '../components/BeginMaturityPeriod.vue'
import DepositInterestList from '../components/DepositInterestList.vue'

import { ref } from 'vue'

export default {
    components: {
        TransactionList, DepositAccountState, BeginMaturityPeriod, DepositInterestList
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
        deposit_account: ref(''),
        type: ref(''),
        prompt: ref(false),
        make_deposit: ref(false),
        make_withdrawal: ref(false),
        approve: ref(false),
        deposit: ref(''),
        withdrawal: ref(''),
        channel: ref(''),
        balance: ref(''),
        tab: ref('details'),
        opt_channels: [{value: '2d6c9129-8ece-47bf-97c3-3e05a728f212', label: 'Cash - Default'}],
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
                    query: '{ accounts_by_pk (id: "' + this.id + '") { id type currency account_no name iban deposit_accounts { id interest_rate state maturity product_deposit { type }}}}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data);

                // where should be only one account
                this.account = JSON.parse(JSON.stringify(data.data.accounts_by_pk))
                this.deposit_account = JSON.parse(JSON.stringify(data.data.accounts_by_pk.deposit_accounts[0]))
                console.log(this.account)
                console.log(this.deposit_account)

            })
        },

        fetchBalance() {
            // booked balance
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
            // add open transactions
            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                    query: '{ transactions_aggregate(where: {debit_account_id: {_eq: "' + this.id + '"}, accounting_date: {_is_null: true}}) { aggregate { sum { amount }}}}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data)
                var amount = data.data.transactions_aggregate.aggregate.sum.amount === null ? 0 : data.data.transactions_aggregate.aggregate.sum.amount
                console.log(amount)
                this.balance = parseFloat(this.balance) + parseFloat(amount)
            })

        },

        setDeposit() {
          this.createTransaction(this.channel.value, this.account.id, this.deposit, this.account.currency, "deposit", "Deposit")
          this.balance = parseFloat(this.balance) + parseFloat(this.deposit)
        },

        setWithdrawal() {
          this.createTransaction(this.account.id, this.channel.value, this.withdrawal, this.account.currency, "deposit", "Withdrawal")
          this.balance = parseFloat(this.balance) - parseFloat(this.withdrawal)
        },

        createTransaction(debit_account, credit_account, amount, currency, key, descr) {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                query: 'mutation { insert_transactions_one(object: {debit_account_id: "' + debit_account +
                '", amount: "' + amount +
                '", currency: "' + currency +
                '", credit_account_id: "' + credit_account +
                '", descr: "' + descr + 
                '", key: "' + key +
                '", value_date: "now()", accounting_date: "now()"}) { id } }'  
                })                  
            })        
            .then(function(response) {
                return response.json()
            })
            .then(data => console.log(data))

            this.$q.notify('Transaction of ' + amount + ' ' + currency + ' saved')

            // reload
            this.fetchTransactions()
        },

        updateBalance(account_id, amount, accounting_date) {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                query: 'mutation { insert_account_balances_one(object: {account_id: "' + account_id +
                '", balance: "' + amount +
                '", reference_date: "' + accounting_date + '"}) { id } }'  
                })                  
            })        
            .then(function(response) {
                return response.json()
            })
            .then(data => console.log(data))

        }
         
    }    
} 

</script>