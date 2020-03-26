<template>
  <div>
    <spinner v-if="loading" />
    <transition name="fade">
      <div class="page" v-if="!loading">
        <div class="panel">
          <label class="selectLabel">Wybierz słownik</label>
          <div class="dualWithDelete">
            <select class="custom-select" v-model="activeDictionary" @change="getLists()" v-if="dictionaries != null">
              <option :value="dict" :key="dict.id" v-for="dict in dictionaries">{{getDictTitle(dict)}}</option>
            </select>
            <select class="custom-select" v-else>
              <option disabled selected>Brak dostępnych słowników</option>
            </select>
            <transition name="fade">
              <img class="iconSelect" src="./assets/stop.png" @click="deleteDictionary()" v-if="activeDictionary != null" />
            </transition>
          </div>

          <label class="selectLabel" v-if="activeDictionary != null">Wybierz listę</label>
          <div class="dualWithDelete">
            <select class="custom-select" v-model="activeList" @change="getWords()" v-if="lists != null && activeDictionary != null">
              <option :value="list" :key="list.id" v-for="list in lists">{{list.title}} ({{words.length}})</option>
            </select>
            <select class="custom-select" v-if="lists == null && activeDictionary != null">
              <option disabled selected>Brak dostępnych list</option>
            </select>
            <transition name="fade">
              <div style="display: flex ; flex-direction: row" v-if="activeList != null">
                <!-- <img class="iconSelect scale" src="./assets/share.svg" @click="shareList()" /> -->
                <img class="iconSelect rotate" src="./assets/stop.png" @click="deleteList()" />
              </div>
            </transition>
          </div>

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
          <transition name="fade">
            <spinner v-if="loadingAddedDict" />
          </transition>
          <transition name="fade">
            <div class="wpisywarka" v-if="activeDictionary != null">
              <p class="header newdict">Nowa lista w słowniku</p>
              <md-field class="listinput">
                <label>Nazwa listy</label>
                <md-input v-model="newList.title"></md-input>
              </md-field>
              <button type="button" class="btn btn-info" @click="addList()" :disabled="activeDictionary == null || newList.title == ''">Dodaj listę</button>
            </div>
          </transition>
          <transition name="fade">
            <spinner v-if="loadingAddedList" />
          </transition>
        </div>

        <transition name="fade">
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
            <p class="info">Synonimy możesz dodać, wpisując je po przecinku.</p>
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
        </transition>
      </div>
    </transition>


    <md-snackbar md-position="center" :md-duration="duration" :md-active.sync="snackbarActive" md-persistent>
      {{snackbarText}}
    </md-snackbar>
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
      loadingAddedDict: false,
      loadingAddedList: false,
      snackbarActive: false,
      duration: 3000,
      snackbarText: "",
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
      this.loadingAddedDict = true;
      this.$http.post('dictionary/add', this.newDict).then(()=>{
        this.getDictionaries();
      });
      this.newDict.lang1 = "";
      this.newDict.lang2 = "";
    },
    addList(){
      this.loadingAddedList = true;
      this.newList.dictId = this.activeDictionary.id;
      this.$http.post('list/add', this.newList).then(()=>{
        this.getDictionaries();
      });
      this.newList.title = "";
    },
    getDictionaries(){
      this.$http.get('dictionaries/'+service.id).then(response => {
        if(response.body != null){
          this.dictionaries = response.body;
          this.activeDictionary = this.dictionaries[0];
          this.loadingAddedDict = false;
          this.$http.get('lists/'+this.activeDictionary.id).then(response => {
            if(response.body != null){
              this.lists = response.body;
              this.activeList = this.lists[0];
              this.loadingAddedList = false;
              this.getWords();
            }
            else {
              this.lists = null;
              this.activeList = null;
              this.loadingAddedList = false;
              this.loading = false;
            }
          });
        }
        else {
          this.dictionaries = null;
          this.activeDictionary = null;
          this.activeList = null;
          this.loading = false;
          this.loadingFirstDictionary = false;
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
    areArraysEqual(a, b){
      if(a.length != b.length){
        return false;
      }
      else {
        const elNotExists = a.some(el => !b.includes(el));
        if(elNotExists){
          return false;
        }
        return true;
      }
    },
    isWordAlreadyAdded(){
      let result = -2;
      const newList1 = this.newWord.lang1.split(", ");
      const newList2 = this.newWord.lang2.split(", ");
      if(this.words == null){
        return result;
      }
      for(let i = 0 ; i < this.words.length ; i++){
        let el = this.words[i];
        const oldList1 = el.lang1.split(", ");
        const oldList2 = el.lang2.split(", ");
        let equal1 = false;
        let equal2 = false;
        if(this.areArraysEqual(newList1, oldList1)) equal1 = true;
        else equal1 = false;
        if(this.areArraysEqual(newList2, oldList2)) equal2 = true;
        else equal2 = false;


        if(equal1 == true && equal2 == false){  //znaleziono odpowiadający język1 ale inny język2
          let list = oldList2.concat(newList2);
          list = [...new Set(list)];  //remove duplicates
          this.newWord.lang2 = list.join(", ");
          result = el.id;
          break;
        }
        else if(equal1 == false && equal2 == true){  //znaleziono odpowiadający język2 ale inny język1
          let list = oldList1.concat(newList1);
          list = [...new Set(list)];  //remove duplicates
          this.newWord.lang1 = list.join(", ");
          result = el.id;
          break;
        }
        else if(equal1 == true && equal2 == true){    //jest identyczne słówko już wpisane
          result = -1;
          break;
        }
      }
      return result;
    },
    justifyWord(text){
      const list = text.split(",");
      list.forEach((word, index) => {
        list[index] = word.trim();
      });
      return list.join(", ");
    },
    addWord(){
      this.loadingNewWord = true;
      this.newWord.listId = this.activeList.id;
      this.newWord.lang1 = this.justifyWord(this.newWord.lang1);
      this.newWord.lang2 = this.justifyWord(this.newWord.lang2);
      const whatToDo = this.isWordAlreadyAdded();
      if(whatToDo == -1){ //nie trzeba nic robić
        this.snackbarText = "Takie słowo już istnieje na tej liście!";
        this.snackbarActive = true;
        this.loadingNewWord = false;
      }
      else if(whatToDo == -2){
        this.$http.post('word/add', this.newWord).then(()=>{
          this.getWords();
        });
      }
      else {
        const updatedWord = {};
        updatedWord.id = whatToDo;
        updatedWord.lang1 = this.newWord.lang1;
        updatedWord.lang2 = this.newWord.lang2;
        this.snackbarText = "Zaktualizowano istniejące słowo!";
        this.snackbarActive = true;
        this.$http.post('word/update', updatedWord).then(()=>{
          this.getWords();
        });
      }
      
      this.newWord.lang1 = "";
      this.newWord.lang2 = "";
      this.$nextTick(() => this.$refs.lang1.$el.focus())
    },
    deleteWord(word){
      this.$http.post('word/delete', word).then(()=>{
        this.getWords();
      });
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
    },
    deleteDictionary(){
      if(this.activeDictionary != null){
        this.$http.post('dictionary/delete', this.activeDictionary).then(()=>{
          this.getDictionaries();
        });
      }
    },
    deleteList(){
      if(this.activeList != null){
        this.$http.post('list/delete', this.activeList).then(()=>{
          this.getLists();
        });
      }
    },
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: baseline;
  flex-wrap: wrap;
}
.panel {
  width: 40%;
  height: auto;
  min-width: 300px;

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
.md-input {
  width: 40% !important;
}
.dual {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  width: 90%;
}
.dualWithDelete {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  justify-content: flex-start;
  width: 90%;
}
button {
  width: 90% !important;
  margin-bottom: 30px;
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
  margin-left: 5px;
  transition: 0.3s;
}
.delete:hover {
  transform: rotate(90deg);
}
.iconSelect {
  width: 15px;
  
  margin-left: 10px;
}
.rotate {
  transition: 0.3s;
}
.rotate:hover {
  transform: rotate(90deg);
}
.scale {
  transition: 0.3s;
}
.scale:hover {
  transform: scale(1.1);
}
.difficultyDot {
  width: 7px;
  min-width: 7px;
  height: 7px;
  min-height: 7px;
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
.wpisywarka {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.info {
  font-size: 9pt;
  margin-top: 0;
  padding-top: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
