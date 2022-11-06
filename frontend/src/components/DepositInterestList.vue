<template>
    <div class="text-h6">Interest</div>
    <div class="row text-weight-bold">

        <div class="col">Value date</div>
        <div class="col">Booked</div>
        <div align="right" class="col">Amount</div>
    </div>
    <div class="row" v-for="interest_item in interest" v-bind:key="interest_item.id">
        <div class="col">{{ interest_item.value_date }}</div>        
        <div class="col">{{ interest_item.booked }}</div>
        <div align="right" class="col" :class="{ 'text-negative': parseFloat(interest_item.amount) < 0 }">{{ parseFloat(interest_item.amount).toFixed(2) }} {{ interest_item.currency }}</div>
    </div>
</template>

<script>

export default {
    props: ['id'],

    created() {
    // fetch on init
        this.fetchInterest()
    },

    data() {
        return {
            interest: [],
        }
    },

    methods: {
        fetchInterest() {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                    query: '{ deposit_interest(where: {account_id: {_eq: "' + this.id + '"}}, order_by: {value_date: desc}) { amount value_date booked id }}'
                }),
            })
            .then(function(response) {
                return response.json()
            })
            .then(data => {
                console.log(data)
                for(let interest_item of data.data.deposit_interest) {
                    this.interest.push(interest_item)
                } 
            })
        },
    }
}
</script>
