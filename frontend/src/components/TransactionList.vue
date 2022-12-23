<template>
    <div>
    <div class="text-h6">Transactions</div>
    <div class="row text-weight-bold">
        <div class="col">Accounting date</div>
        <div class="col">Value date</div>
        <div class="col">Key</div>
        <div class="col">Description</div>
        <div align="right" class="col">Amount</div>
    </div>
    <div class="row" v-for="transaction in transactions" v-bind:key="transaction.id">
        <div class="col">{{ transaction.accounting_date }}</div>
        <div class="col">{{ transaction.value_date }}</div>
        <div class="col">{{ transaction.key }}</div>
        <div class="col">{{ transaction.descr }}</div>
        <div align="right" class="col" :class="{ 'text-negative': parseFloat(transaction.amount) < 0 }">{{ parseFloat(transaction.amount).toFixed(2) }} {{ transaction.currency }}</div>
    </div>
    </div>
</template>

<script>

export default {
    props: ['id'],

    created() {
    // fetch on init
        this.fetchTransactions()
    },

    data() {
        return {
            transactions: [],
        }
    },

    methods: {
        fetchTransactions() {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                    //query: '{ deposit_account_balances(where: {account_id: {_eq: "' + this.id + '"}, reference_date: {}}, order_by: {reference_date: desc}, limit: 1) { balance }}'
                    query: '{ transactions(where: {_or: [{debit_account_id: {_eq: "' + this.id + '"}}, {credit_account_id: {_eq: "' + this.id + '"}}]}, order_by: {accounting_date: desc, value_date: desc}) { amount accounting_date currency descr key value_date id credit_account_id debit_account_id }}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data)
                for(let transaction of data.data.transactions) {
                    if(transaction.debit_account_id == this.id) {
                      // debit or credit?
                      transaction.amount = - parseFloat(transaction.amount)
                    }
                    this.transactions.push(transaction)
                } 
            })
        },
    }
}
</script>
