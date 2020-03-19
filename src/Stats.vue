<template>
  <div>
    <spinner v-if="loading1 || loading2" />
    <div class="page" v-if="!loading1 && !loading2">
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

        <div v-if="displayDictStats == true">
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
        <div v-else>
          Brak statystyk do pokazania dla tego słownika!
        </div>
      </div>

      <div class="table" v-if="displayListStats == true">
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
      <div v-else>
        Brak statystyk do pokazania dla tej listy!
      </div>
    </div>
  </div>
</template>

<script>
import Spinner from './Spinner.vue';
import service from './service.js';

export default {
  name: 'Vocabulary',
  components: {
    Spinner
  },
  data(){
    return {
      activeDictionary: null,
      activeList: null,
      dictionaries: null,
      lists: null,
      names: null,
      values: null,
      labels: null,
      namesAll: null,
      valuesAll: null,
      labelsAll: null,
      loading1: true,
      loading2: true,
      displayDictStats: true,
      displayListStats: true,

    }
  },
  mounted(){
    this.getDictionaries();
  },
  methods: {
    getDictionaries(){
      this.$http.get('dictionaries/'+service.id).then(response => {
        if(response.body != null){
          this.dictionaries = response.body;
          this.activeDictionary = this.dictionaries[0];
          this.$http.get('lists/'+this.activeDictionary.id).then(response => {
            if(response.body != null){
              this.lists = response.body;
              this.activeList = this.lists[0];
              this.getStats();
              this.getDictStats();
            }
            else {
              this.lists = null;
              this.activeList = null;
              this.displayDictStats = false;
              this.displayListStats = false;
              this.loading1 = false;
              this.loading2 = false;
            }
          });
        }
        else {
          this.dictionaries = null;
          this.activeDictionary = null;
          this.lists = null;
          this.activeList = null;
          this.displayDictStats = false;
          this.displayListStats = false;
          this.loading1 = false;
          this.loading2 = false;
        }
      });
    },
    getLists(){
      this.loading1 = true;
      this.loading2 = true;
      this.$http.get('lists/'+this.activeDictionary.id).then(response => {
          if(response.body != null) {
            this.lists = response.body;
            this.activeList = this.lists[0];
          }
          else {
            this.lists = null;
            this.activeList = null;
          }
          this.getStats();
        });
    },
    getStats(){
      if(this.activeList != null){
        this.$http.get('stats/'+this.activeList.id).then(response => {
          if(response.body != null){
            this.values = response.body;
            this.names = [this.activeList.title];
            this.labels = [];
            for(var i = 1 ; i <= this.values.length ; i++){
              this.labels.push(i);
            }
            this.loading1 = false;
          }
          else {
            this.displayListStats = false;
            this.values = null;
            this.names = null;
            this.labels = null;
            this.loading1 = false;
          }
        });
      }
      else {
        this.displayListStats = false;
        this.values = null;
        this.names = null;
        this.labels = null;
        this.loading1 = false;
      }
    },
    getDictStats(){
      if(this.activeDictionary != null){
        this.$http.get('stats/allLists/'+this.activeDictionary.id).then(response => {
          if(response.body != null && response.body[0].values[0].length > 0){
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
            this.loading2 = false;
          }
          else {
            this.displayDictStats = false;
            this.valuesAll = null;
            this.namesAll = null;
            this.labelsAll = null;
            this.loading2 = false;
          }
        });
      }
      else {
        this.displayDictStats = false;
        this.valuesAll = null;
        this.namesAll = null;
        this.labelsAll = null;
        this.loading2 = false;
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
