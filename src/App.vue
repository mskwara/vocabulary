<template>
  <div class="page-container">
      <md-toolbar>
        <span class="md-title" @click="setRoute('home')">Vocabulary Helper</span>
      </md-toolbar>

      
      <router-view></router-view>
  </div>
</template>

<script>

export default {
  name: 'App',
  components: {
  },
  data(){
    return {
      menuVisible: false,
    }
  },
  methods: {
    setRoute(key){
      this.$router.push({ name: key});
    }
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
</style>
