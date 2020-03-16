<template>
  <div class="page">
    <div class="panel">
      <label class="selectLabel">Wybierz słownik</label>
      <select class="custom-select" v-model="activeDictionary" @change="getLists()" :disabled="testRunning">
        <option :value="dict" :key="dict.id" v-for="dict in dictionaries">{{getDictTitle(dict)}}</option>
      </select>

      <label class="selectLabel">Wybierz listę</label>
      <select class="custom-select" v-model="activeList" :disabled="testRunning">
        <option :value="list" :key="list.id" v-for="list in lists">{{list.title}}</option>
      </select>

      <p class="header">Język odpowiedzi</p>
      <div class="radios">
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="radio1" name="answerLang" class="custom-control-input" value="1" v-model="answerLang" :disabled="testRunning">
          <label class="custom-control-label labellang" for="radio1">{{activeDictionary.lang1}}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="radio2" name="answerLang" class="custom-control-input" value="2" v-model="answerLang" :disabled="testRunning">
          <label class="custom-control-label labellang" for="radio2">{{activeDictionary.lang2}}</label>
        </div>
      </div>
      <button type="button" class="btn btn-success" @click="start()" 
      :disabled="answerLang == null || activeList == null || testRunning">Rozpocznij</button>
    </div>


    <div class="table" v-if="testRunning">
      <p class="activeWord" v-if="answerLang == 2">{{activeWord.lang1}}</p>
      <p class="activeWord" v-else>{{activeWord.lang2}}</p>

      <md-field class="answer">
        <label class="labellang" v-if="answerLang == 1">{{activeDictionary.lang1}}</label>
        <label class="labellang" v-else>{{activeDictionary.lang2}}</label>
        <md-input ref="answer" v-model="answer"></md-input>
      </md-field>
      <div class="hint">
        <transition name="fade">
          <div v-if="hintVisible">
            <p class="help">{{hintText}}</p>
          </div>
        </transition>
      </div>
      <button type="button" class="btn btn-primary accept" @click="accept()">Zatwierdź</button>
    </div>


    <md-dialog-alert
      :md-active.sync="displayResults"
      md-title="Koniec testu"
      :md-content="displayResultsText" />
  </div>
</template>

<script>

export default {
  name: 'Test',
  components: {
  },
  data(){
    return {
      activeDictionary: [],
      answer: "",
      dictionaries: [],
      lists: [],
      words: [],
      answerLang: null,
      activeWord: {},
      testRunning: false,
      result: {
        correct: 0,
        all: 0,
        difficultWords: [],
        listId: null,
        userId: 0,
      },
      displayResults: false,
      displayResultsText: "",
      remaining: 0,
      hintVisible: false,
      hintText: "",
      hintTimeout: null,
    }
  },
  mounted(){
    this.getDictionaries();
    window.addEventListener("keypress", e => {
      if(this.testRunning && e.keyCode == 13){
        this.accept();
      }
    });
  },
  methods: {
    getDictionaries(){
      this.$http.get('dictionaries/0').then(response => {
        this.dictionaries = response.body;
        this.activeDictionary = this.dictionaries[0];
        this.$http.get('lists/'+this.activeDictionary.id).then(response => {
          this.lists = response.body;
          this.activeList = this.lists[0];
        });
      });
    },
    getLists(){
      this.$http.get('lists/'+this.activeDictionary.id).then(response => {
          this.lists = response.body;
          this.activeList = this.lists[0];
        });
    },
    getWordsAndRun(){
      this.$http.get('words/'+this.activeList.id).then(response => {
        this.words = response.body;
        this.shuffleArray(this.words);
        this.testRunning = true;
        this.result.correct = 0;
        this.result.all = this.words.length;
        this.result.difficultWords = [];
        this.result.listId = this.activeList.id;
        this.remaining = this.words.length;
        this.displayNextWord();
      });
    },
    getDictTitle(dict){
      return dict.lang1+" - "+dict.lang2;
    },
    getWordTitle(word){
      return word.lang1+" - "+word.lang2;
    },
    shuffleArray(array) {
      var counter = array.length, temp, index;
      while ( counter > 0 ) {
        index = Math.floor( Math.random() * counter );
        counter--;
        temp = array[counter];
        array[counter] = array[index];
        array[index] = temp;
      }
    },
    shuffleActiveWord(array) {
      var temp, index;
      index = Math.floor( Math.random() * array.length);
      temp = array[0];
      array[0] = array[index];
      array[index] = temp;
    },
    start(){
      this.getWordsAndRun();
    },
    displayNextWord(){
      if(this.remaining == 0){
        var numeral = require('numeral');
        var percent = numeral(this.result.correct*1.0/this.result.all*100).format('0.00');
        this.displayResultsText = "Twój wynik to "+this.result.correct+"/"+this.result.all+", co stanowi "+percent+"%";
        this.displayResults = true;
        this.testRunning = false;
        this.$http.post('stats/add', this.result);
        this.$http.post('words/setDifficulty', this.result.difficultWords);
        return;
      }
      this.activeWord = this.words[0];
      this.$nextTick(() => this.$refs.answer.$el.focus());
      
    },
    addToDifficultWords(word){
      for(var i = 0 ; i < this.result.difficultWords.length ; i++){
        if(this.result.difficultWords[i].id == word.id){
          this.result.difficultWords[i].count++;
          return;
        }
      }
      var diffWord = {};
      diffWord.id = word.id;
      diffWord.count = 1;
      this.result.difficultWords.push(diffWord);
    },
    wasIncorrect(word){
      for(var i = 0 ; i < this.result.difficultWords.length ; i++){
        if(this.result.difficultWords[i].id == word.id){
          return true;
        }
      }
      return false;
    },
    accept(){
        if((this.answerLang == 1 && this.answer.trim() == this.activeWord.lang1.trim()) || (this.answerLang == 2 && this.answer.trim() == this.activeWord.lang2.trim())){
          if(!this.wasIncorrect(this.activeWord)){
            this.result.correct++;
          }
          this.remaining--;
          this.words.splice(0, 1);
          this.displayNextWord();
        }
        else {
          if(this.answerLang == 2){
            this.hintText = this.activeWord.lang1+" - "+this.activeWord.lang2;
          }
          else {
            this.hintText = this.activeWord.lang2+" - "+this.activeWord.lang1;
          }
          clearTimeout(this.hintTimeout);
          this.hintVisible = true;
          this.hintTimeout = setTimeout(() => {
            this.hintVisible = false;
          }, 5000);
          this.addToDifficultWords(this.activeWord);
          this.shuffleActiveWord(this.words);
          this.displayNextWord();
        }
        this.answer = "";
    }
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
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
  margin-top: 30px;
}
.leftinput {
  margin-right: 20px;
}
.header {
  margin: 10px 0;
  font-size: 15pt;
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
}
.wordContent {
  margin: 0;
}
.radios {
  display: flex;
  flex-direction: row;
  align-items: center;
}
.activeWord {
  font-size: 25pt;
  font-family: Calibri;
  text-align: center;
}
.answer {
  width: 50%;
  min-width: 300px;

}
.accept {
  width: 50% !important;
}
.help {
  margin: 0;
}
.hint {
  height: 20px;
}



.fade-enter-active, .fade-leave-active {
  transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
