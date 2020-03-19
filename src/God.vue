<template>
  <div id="god">
    <router-view @authenticated="setAuthenticated" />
  </div>
</template>

<script>
import service from "./service.js";

export default {
  name: 'god',
  components: {
  },
  data(){
    return {
    }
  },
  mounted(){
    if(!service.authenticated) {
      this.$router.replace({ name: "login" }).catch(() => {});
    }
    else {
      this.$router.replace({ name: "app" }).catch(() => {});
    }
  },
  methods: {
      setAuthenticated(status) {
          service.authenticated = status;
      },

  }
}
</script>

<style scoped>
body {
  overflow: hidden;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 0;
}

</style>
