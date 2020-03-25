<template>
  <div class="page-container">
      <md-toolbar>
        <span class="md-title" @click="setRoute('home')">Vocabulary Helper</span>
        <a class="logout" @click="logout()">Wyloguj</a>
      </md-toolbar>

      
      <router-view></router-view>
  </div>
</template>

<script>
import service from "./service.js";

export default {
  name: 'App',
  components: {
  },
  data(){
    return {
    }
  },
  mounted() {
    if(!service.authenticated) {
        this.$router.replace({ name: "login" });
    }
    else {
      this.$router.push({ name: "home" });
    }
  },
  methods: {
    setRoute(key){
      this.$router.push({ name: key});
    },
    setAuthenticated(status){
      service.authenticated = status;
    },
    logout(){
      service.authenticated = false;
      service.id = null;
      service.nick = "";
      this.$router.replace({ name: "login" });
    },
  }
}
</script>

<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;

}
.md-toolbar {
  background-color: white !important;
  
  justify-content: center;
  margin-bottom: 30px;
}
.md-title {
  color: rgb(56, 56, 56) !important;
  cursor: pointer;
  font-family: Sui Generis;
  transition: 0.5s;
}
.md-title:hover {
  color: rgb(180, 43, 2) !important;
  transform: scale(0.95);
}
.logout {
  position: absolute;
  right: 20px;
  cursor: pointer;
}
</style>
