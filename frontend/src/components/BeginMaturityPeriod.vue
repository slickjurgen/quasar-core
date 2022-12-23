<template>
    <div>
    <q-btn v-if="state == 'active'" 
        no-caps 
        to="" 
        color="primary" 
        label="Begin Maturity Period" 
        @click="prompt = true" 
        :disable="(balance === 0)  ? true : false" />


    <q-dialog v-model="prompt" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Enter term for maturity</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
<!--
          <q-input dense v-model="maturity" autofocus @keyup.enter="prompt = false" />
-->
            <q-select dense v-model="maturity" :options="opt_maturity" label="Terms" />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Set maturity" v-close-popup @click="setMaturity" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    </div>
</template>

<script>
import { ref } from 'vue'

export default {
    
    props: ['id', 'state', 'balance', 'interest_rate'],

    data() {
        return {
            prompt: ref(false),
            maturity: ref(''),
            maturity_date: ref(''),
            opt_maturity: [{ value: '12', label: "1 year"}, { value: '24', label: "2 years"}, { value: '36', label: "3 years"}]
        }
    },

    methods: {
        setMaturity() {

            fetch('http://localhost:8080/v1/graphql', {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                },
                body: JSON.stringify({
                    query: 'mutation { beginMaturityPeriodFixedDeposit(input: {accountId: "' + this.id + '", term: ' + this.maturity.value + '}) { success }}'
                })
            })        
            .then(function(response) {
                return response.json()
            })
            .then(data => console.log(data)) 
            // give info on success
            this.$q.notify('Maturity period has been started')
            // clear data
/*
            console.log(this.maturity.value)

            // first calculate the dates based on today
            var date = new Date()
            console.log(date)
            this.maturity_date = new Date(date.setMonth(date.getMonth() + parseInt(this.maturity.value)))

            console.log(this.maturity_date)

            console.log("=== Interest paid once at the end of maturity ===")

            // calc interest on end of maturity
            var interest = (parseFloat(this.balance) / 100) * parseFloat(this.interest_rate) / 12 * parseInt(this.maturity.value)

            console.log("Interest at the end of maturity: " + interest)

            // calc interest on a yearly basis
            var years = parseInt(this.maturity.value) / 12
            var calc_balance = this.balance

            console.log("=== Yearly paid interest ===")

            for(let i = 1; i <= years; i++ ) {
                var interest = (parseFloat(calc_balance) / 100) * parseFloat(this.interest_rate)
                // add interest for calculating in the next year maybe
                calc_balance += parseFloat(interest)

                console.log("Interest in year " + i + ": " + interest)
                console.log("New Balance: " + calc_balance)
            }

            //calc interest on monthly basis
            var months = parseInt(this.maturity.value)
            var calc_balance = this.balance

            console.log("=== Monthly paid interest ===")

            for(let i = 1; i <= months; i++) {
                var interest = (parseFloat(calc_balance) / 100) * parseFloat(this.interest_rate) / 12
                // add interest to balance for next month calculating
                calc_balance += parseFloat(interest)

                console.log("Interest in month " + i + ": " + interest)
                console.log("New Balance: " + calc_balance)                
            }
    
*/
        },
    }
}
</script>
