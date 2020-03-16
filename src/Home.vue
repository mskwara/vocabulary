<template>
  <div class="page">
    <div class="field vocabulary" @click="setRoute('vocabulary')">
      Słownictwo
    </div>
    <div class="field test" @click="setRoute('test')">
      Test
    </div>
    <div class="field stats" @click="setRoute('stats')">
      Statystyki
    </div>
  </div>
</template>

<script>

export default {
  name: 'Home',
  components: {
  },
  data(){
    return {
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
.page {
  display: flex;
  flex-direction: row;
  justify-content: center;
}
.field {
  width: 300px;
  height: 300px;
  color: white;
  font-size: 30pt;
  text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
  font-family: Sui Generis;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  border-radius: 5px;
  background-repeat: no-repeat;
  background-position: center; 
  position: relative;
  margin: 0 10px;

  transition: 0.3s;
}
.test {
  background-image: url('./assets/test.png');
}
.vocabulary {
  background-image: url('./assets/vocabulary.jpg');
}
.stats {
  background-image: url('./assets/stats.jpg');
}
.field:hover {
  transform: scale(0.95);
  cursor: pointer;
}
</style>
