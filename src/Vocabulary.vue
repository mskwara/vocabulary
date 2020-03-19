<template>
  <div>
    <spinner v-if="loading" />
    <div class="page" v-if="!loading">
      <div class="panel">
        <label class="selectLabel">Wybierz słownik</label>
        <select class="custom-select" v-model="activeDictionary" @change="getLists()" v-if="dictionaries != null">
          <option :value="dict" :key="dict.id" v-for="dict in dictionaries">{{getDictTitle(dict)}}</option>
        </select>
        <select class="custom-select" v-else>
          <option disabled selected>Brak dostępnych słowników</option>
        </select>

        <label class="selectLabel" v-if="activeDictionary != null">Wybierz listę</label>
        <select class="custom-select" v-model="activeList" @change="getWords()" v-if="lists != null && activeDictionary != null">
          <option :value="list" :key="list.id" v-for="list in lists">{{list.title}}</option>
        </select>
        <select class="custom-select" v-if="lists == null && activeDictionary != null">
          <option disabled selected>Brak dostępnych list</option>
        </select>

        <p class="header newdict">Nowy słownik</p>
        <span class="dual">
          <md-field class="leftinput">
            <label>Pierwszy język</label>
            <md-input v-model="newDict.lang1"></md-input>
          </md-field>
          <md-field>
            <label>Drugi język</label>
            <md-input v-model="newDict.lang2"></md-input>
          </md-field>
        </span>
        <button type="button" class="btn btn-warning" @click="addDictionary()" :disabled="newDict.lang1 == '' || newDict.lang2 == ''">Dodaj słownik</button>
        
        <p class="header newdict" v-if="activeDictionary != null">Nowa lista w słowniku</p>
        <md-field class="listinput" v-if="activeDictionary != null">
          <label>Nazwa listy</label>
          <md-input v-model="newList.title"></md-input>
        </md-field>
        <button v-if="activeDictionary != null" type="button" class="btn btn-info" @click="addList()" :disabled="activeDictionary == null || newList.title == ''">Dodaj listę</button>
      </div>

      <div class="table" v-if="activeList != null">
        <p class="header">Dodaj słówko</p>
        <span class="dual">
          <md-field class="leftinput wordinput">
            <label class="labellang">{{activeDictionary.lang1}}</label>
            <md-input ref="lang1" v-model="newWord.lang1"></md-input>
          </md-field>
          <md-field class="wordinput">
            <label class="labellang">{{activeDictionary.lang2}}</label>
            <md-input v-model="newWord.lang2"></md-input>
          </md-field>
        </span>
        <button type="button" class="btn btn-primary" @click="addWord()" :disabled="newWord.lang1 == '' || newWord.lang2 == ''">Wstaw słówko</button>
        
        <div class="spinner-grow text-primary loadingnewword" role="status" v-if="loadingNewWord">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="words" v-if="words != null && words.length > 0">
          <ul class="list-group">
            <li class="list-group-item word" :key="word.id" v-for="word in words">
              <div class="wordAndDiff">
                <p class="wordContent">{{getWordTitle(word)}}</p>
                <div class="difficultyDot" v-if="word.testsCount != 0" :style="styleDifficulty(word)"></div>
              </div>
              <img class="delete" src="./assets/cancel.svg" @click="deleteWord(word)" />
            </li>
          </ul>
        </div>
        <div class="words" v-else>
          Brak słówek w tej liście!
        </div>
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
      newDict: {
        userId: service.id,
        lang1: "",
        lang2: ""
      },
      newWord: {
        listId: null,
        lang1: "",
        lang2: ""
      },
      newList: {
        dictId: null,
        title: "",
      },
      dictionaries: null,
      lists: null,
      words: null,
      loading: true,
      loadingNewWord: false,
    }
  },
  mounted(){
    this.getDictionaries();
    window.addEventListener("keypress", e => {
      if(this.newWord.lang1 != '' && this.newWord.lang2 != '' && e.keyCode == 13){
        this.addWord();
      }
    });
  },
  methods: {
    addDictionary(){
      this.$http.post('dictionary/add', this.newDict);
      this.newDict.lang1 = "";
      this.newDict.lang2 = "";
      this.getDictionaries();
    },
    addList(){
      this.newList.dictId = this.activeDictionary.id;
      this.$http.post('list/add', this.newList);
      this.newList.title = "";
      this.getDictionaries();
    },
    getDictionaries(){
      this.$http.get('dictionaries/'+service.id).then(response => {
        if(response.body != null){
          this.dictionaries = response.body;
          this.activeDictionary = this.dictionaries[0];
          this.$http.get('lists/'+this.activeDictionary.id).then(response => {
            if(response.body != null){
              this.lists = response.body;
              this.activeList = this.lists[0];
              this.getWords();
            }
            else {
              this.lists = null;
              this.activeList = null;
              this.loading = false;
            }
          });
        }
        else {
          this.dictionaries = null;
          this.activeDictionary = null;
          this.loading = false;
        }
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
          this.getWords();
        });
    },
    getWords(){
      if(this.activeList != null){
        this.$http.get('words/'+this.activeList.id).then(response => {
          this.words = response.body;
          this.loading = false;
          this.loadingNewWord = false;
        });
      }
      else {
        this.words = null;
      }
      
    },
    getDictTitle(dict){
      return dict.lang1+" - "+dict.lang2;
    },
    getWordTitle(word){
      return word.lang1+" - "+word.lang2;
    },
    addWord(){
      this.loadingNewWord = true;
      this.newWord.listId = this.activeList.id;
      this.$http.post('word/add', this.newWord);
      this.newWord.lang1 = "";
      this.newWord.lang2 = "";
      this.$nextTick(() => this.$refs.lang1.$el.focus())
      this.getWords();
    },
    deleteWord(word){
      this.$http.post('word/delete', word);
      this.getWords();
    },
    styleDifficulty(word){
      var style = "";
      var factor = word.difficulty/word.testsCount;
      if(factor == 0){
        style = "background-color: #03fc03"
      }
      else if(factor <= 1){
        style = "background-color: #f9ff4f"
      }
      else if(factor > 1 && factor < 2){
        style = "background-color: #ffc400"
      }
      else if(factor >= 2){
        style = "background-color: #fc0303"
      }
      return style;
    }
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: baseline;
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
.dual {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
button {
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
  margin-top: 20px;
}
.custom-select {
  width: 90%;
  margin-bottom: 20px;
}
.labellang {
  text-transform: capitalize;
}
.word {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}
.wordContent {
  margin: 0;
}
.delete {
  width: 10px;
  transition: 0.3s;
}
.delete:hover {
  transform: rotate(90deg);
}
.difficultyDot {
  width: 7px;
  height: 7px;
  border-radius: 4px;
  border: 1px solid black;
  margin-left: 20px;
}
.wordAndDiff {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.selectLabel {
  align-self: flex-start;
  margin-left: 20px;
}
.words {
  width: 90% !important;
  margin-top: 30px;
  text-align: center;
}
.listinput {
  width: 90%;
}
.loadingnewword {
  margin-top: 10px;
}
</style>
