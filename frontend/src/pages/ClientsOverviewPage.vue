<template>
  <div class="row">
    <div class="col-4">
      <ClientDetails :client_id='this.client_id' />
    </div>
    <div class="col-8">
      <ClientAccounts :client_id='this.client_id' />
    </div>
  </div>
</template>



<script setup>
import { ref } from 'vue'

import ClientDetails from '../components/ClientDetails.vue'
import ClientAccounts from '../components/ClientAccounts.vue'
 
</script>

<script>

export default {
  components: {
    ClientDetails, ClientAccounts
  },

  props: ['client_id'],

  data() {
    return {
     client: ref(''),
    }
  },  
  
} 
</script>