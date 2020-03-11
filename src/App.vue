<template>
  <div class="page-container">
      <md-toolbar>
        <span class="md-title">Vocabulary Helper</span>
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
  color: black !important;
}
</style>
