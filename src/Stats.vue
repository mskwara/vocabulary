<template>
  <div class="page">
    <div class="panel">
      <label class="selectLabel">Wybierz słownik</label>
      <select class="custom-select" v-model="activeDictionary" @change="dictChange()" v-if="dictionaries != null">
        <option :value="dict" :key="dict.id" v-for="dict in dictionaries">{{getDictTitle(dict)}}</option>
      </select>
      <select class="custom-select" v-else>
        <option disabled selected>Brak dostępnych słowników</option>
      </select>


      <label class="selectLabel">Wybierz listę</label>
      <select class="custom-select" v-model="activeList" @change="getStats()" v-if="lists != null">
        <option :value="list" :key="list.id" v-for="list in lists">{{list.title}}</option>
      </select>
      <select class="custom-select" v-else>
        <option disabled selected>Brak dostępnych list</option>
      </select>

      <graph-line
              :width="500"
              :height="300"
              :shape="'normal'"
              :axis-min="0"
              :axis-max="100"
              :axis-full-mode="true"
              :labels="labelsAll"
              :names="namesAll"
              :values="valuesAll">
          <note :text="'Wyniki procentowe w kolejnych testach'"></note>
          <tooltip :names="namesAll" :position="'right'"></tooltip>
          <legends :names="namesAll"></legends>
          <guideline :tooltip-y="true"></guideline>
      </graph-line>
    </div>

    <div class="table">
      <graph-line
              :width="700"
              :height="500"
              :shape="'normal'"
              :axis-min="0"
              :axis-max="100"
              :axis-full-mode="true"
              :colors="['#00b81c']"
              :labels="labels"
              :names="names"
              :values="values">
          <note :text="'Wyniki procentowe w kolejnych testach'"></note>
          <tooltip :names="names" :position="'right'"></tooltip>
          <legends :names="names"></legends>
          <guideline :tooltip-y="true"></guideline>
      </graph-line>
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
      activeDictionary: [],
      activeList: [],
      dictionaries: [],
      lists: [],
      names: [],
      values: [],
      labels: [],
      namesAll: [],
      valuesAll: [],
      labelsAll: [],
    }
  },
  mounted(){
    this.getDictionaries();
  },
  methods: {
    getDictionaries(){
      this.$http.get('dictionaries/0').then(response => {
        this.dictionaries = response.body;
        this.activeDictionary = this.dictionaries[0];
        this.$http.get('lists/'+this.activeDictionary.id).then(response => {
          this.lists = response.body;
          this.activeList = this.lists[0];
          this.getStats();
          this.getDictStats();
        });
      });
    },
    getLists(){
      this.$http.get('lists/'+this.activeDictionary.id).then(response => {
          this.lists = response.body;
          if(this.lists != null) {
            this.activeList = this.lists[0];
          }
          else {
            this.activeList = null;
          }
          this.getStats();
        });
    },
    getStats(){
      if(this.activeList != null){
        this.$http.get('stats/'+this.activeList.id).then(response => {
          this.values = response.body;
          this.names = [this.activeList.title];
          this.labels = [];
          for(var i = 1 ; i <= this.values.length ; i++){
            this.labels.push(i);
          }
        });
      }
      else {
        this.values = [];
        this.names = [];
        this.labels = [];
      }
    },
    getDictStats(){
      if(this.activeDictionary != null){
        this.$http.get('stats/allLists/'+this.activeDictionary.id).then(response => {
          this.valuesAll = response.body[0].values;
          this.namesAll = response.body[0].listsTitles;
          this.labelsAll = [];

          var maxLen = 0;
          for(var i = 0 ; i < this.valuesAll.length ; i++){
            if(this.valuesAll[i].length > maxLen){
              maxLen = this.valuesAll[i].length;
            }
          }
          for(var k = 1 ; k <= maxLen ; k++){
            this.labelsAll.push(k);
          }
        });
      }
      else {
        this.valuesAll = [];
        this.namesAll = [];
        this.labelsAll = [];
      }
    },
    getDictTitle(dict){
      return dict.lang1+" - "+dict.lang2;
    },
    dictChange(){
      this.getLists();
      this.getDictStats();
    }
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: flex-start;
}
.panel {
  width: 30%;
  height: auto;
  min-width: 430px;
  margin-right: 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-top: 30px;
  margin-left: 30px;
}
.table {
  width: 40%;
  min-width: 300px;
  height: auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.custom-select {
  width: 90%;
  margin-bottom: 20px;
}
.selectLabel {
  align-self: flex-start;
  margin-left: 20px;
}
</style>
