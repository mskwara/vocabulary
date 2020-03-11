<template>
  <div class="page">
    <div class="panel">
      <div class="md-layout-item">
        <md-field>
          <label for="language">Wybierz słownik</label>
          <md-select v-model="language" name="language" id="language">
            <md-option value="fight-club">Fight Club</md-option>
            <md-option value="godfather">Godfather</md-option>
          </md-select>
        </md-field>
      </div>
      <p class="header">Dodaj słówko</p>
      <span class="dual">
        <md-field class="leftinput">
          <label>Polski</label>
          <md-input v-model="word1"></md-input>
        </md-field>
        <md-field>
          <label>Angielski</label>
          <md-input v-model="word2"></md-input>
        </md-field>
      </span>
      <md-button class="md-raised md-primary">Wstaw słówko</md-button>


      <p class="header newdict">Nowy słownik</p>
      <span class="dual">
        <md-field class="leftinput">
          <label>Pierwszy język</label>
          <md-input v-model="lang1"></md-input>
        </md-field>
        <md-field>
          <label>Drugi język</label>
          <md-input v-model="lang2"></md-input>
        </md-field>
      </span>
      <md-button class="md-raised md-primary">Dodaj słownik</md-button>
    </div>
    <div class="table">
      table
    </div>
  </div>
</template>

<script>

export default {
  name: 'Vocabulary',
  components: {
  },
  data(){
    return {
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
.panel {
  width: 30%;
  height: auto;
  min-width: 300px;
  margin-right: 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.table {
  width: 60%;
  height: auto;
}
.dual {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
.md-button {
  width: 90% !important;
}
.leftinput {
  margin-right: 20px;
}
.header {
  margin: 0;
  font-size: 15pt;
}
.newdict {
  margin-top: 50px;
}
</style>
