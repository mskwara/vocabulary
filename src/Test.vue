<template>
  <div>
    <spinner v-if="loading" />
    <transition name="fade">
    <div class="page" v-if="!loading">
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
        <div class="sliderPanel">
          <div class="slider">
            <label for="timeRange">Czas na odpowiedź</label>
            <input type="range" class="custom-range" min="0" max="30" step="5" id="timeRange" v-model="timeForAnswer" :disabled="testRunning">
          </div>
          <h3 v-if="timeForAnswer == 0">∞</h3>
          <h3 v-else>{{timeForAnswer}}s</h3>
        </div>

        <button type="button" class="btn btn-success" @click="getWordsAndRun()" 
        :disabled="answerLang == null || activeList == null || testRunning">Rozpocznij</button>
      </div>

      <transition name="fade">
        <div class="table" v-if="testRunning">
          <div class="table-test">
            <div class="activeWord">
              <p v-if="answerLang == 2">{{activeWord.lang1}}</p>
              <p v-else>{{activeWord.lang2}}</p>
              <p class="wordsToWrite" v-if="wordsToWrite > 1">({{wordsToWrite}})</p>
            </div>

            <md-field class="answer">
              <label class="labellang" v-if="answerLang == 1">{{activeDictionary.lang1}}</label>
              <label class="labellang" v-else>{{activeDictionary.lang2}}</label>
              <md-input v-focus v-model="answer"></md-input>
            </md-field>
            <div class="progress time" v-if="timeForAnswer != 0">
              <div class="progress-bar bg-info" role="progressbar" :style="setTime()" aria-valuemin="0" :aria-valuemax="timeForAnswer"></div>
            </div>
            <div class="hint">
              <transition name="fade">
                <div v-if="hintVisible">
                  <p class="help">{{hintText}}</p>
                </div>
              </transition>
            </div>
            <button type="button" class="btn btn-primary accept" @click="accept()">Zatwierdź</button>
          </div>
          <div class="progress remaining-progressbar">
            <div class="progress-bar-striped progress-bar-animated bg-info" role="progressbar" :style="setRemaining()" aria-valuemin="0" :aria-valuemax="result.all"></div>
          </div>
        </div>
      </transition>



      <md-dialog-alert
        :md-active.sync="displayResults"
        md-title="Koniec testu"
        :md-content="displayResultsText" />
      
      <md-dialog-alert
        :md-active.sync="noWords"
        md-title="Wystąpił błąd..."
        md-content="W tej liście nie ma żadnych słówek! Dodaj je w sekcji: Słownictwo" />
    </div>
    </transition>
  </div>
</template>

<script>
import Spinner from './Spinner.vue';
import service from './service.js';

export default {
  name: 'Test',
  components: {
    Spinner
  },
  directives: {
    focus: {
      inserted: function (el) {
        el.focus()
      }
    }
  },
  data(){
    return {
      activeDictionary: null,
      answer: "",
      dictionaries: null,
      lists: null,
      words: null,
      answerLang: null,
      activeWord: {},
      testRunning: false,
      result: {
        correct: 0,
        all: 0,
        difficultWords: [],
        listId: null,
        userId: service.id,
      },
      displayResults: false,
      displayResultsText: "",
      remaining: 0,
      hintVisible: false,
      hintText: "",
      hintTimeout: null,
      loading: true,
      noWords: false,
      timeForAnswer: 0,
      remainingTime: 0,
      answerInterval: null,
      wasLate: false,
      wordsToWrite: 1,
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
      this.$http.get('dictionaries/'+service.id).then(response => {
        if(response.body != null){
          this.dictionaries = response.body;
          this.activeDictionary = this.dictionaries[0];
          this.$http.get('lists/'+this.activeDictionary.id).then(response => {
            if(response.body != null){
              this.lists = response.body;
              this.activeList = this.lists[0];
              this.loading = false;
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
        });
    },
    getWordsAndRun(){
      this.$http.get('words/'+this.activeList.id).then(response => {
        if(response.body != null){
          this.words = response.body;
          this.shuffleArray(this.words);
          this.testRunning = true;
          this.result.correct = 0;
          this.result.all = this.words.length;
          this.result.difficultWords = [];
          this.result.listId = this.activeList.id;
          this.remaining = this.words.length;
          this.displayNextWord();
        }
        else {
          this.words = null;
          this.noWords = true;
        }
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
    displayNextWord(){
      if(this.remaining == 0){
        clearTimeout(this.answerInterval);
        var numeral = require('numeral');
        var percent = numeral(this.result.correct*1.0/this.result.all*100).format('0.00');
        this.result.correct = numeral(this.result.correct).format('0.0');
        this.displayResultsText = "Twój wynik to "+this.result.correct+"/"+this.result.all+".0, co stanowi "+percent+"%";
        this.displayResults = true;
        this.testRunning = false;
        this.$http.post('stats/add', this.result);
        this.$http.post('words/setDifficulty', this.result.difficultWords);
        return;
      }
      this.activeWord = this.words[0];
      if(this.answerLang == 1){
        this.wordsToWrite = this.activeWord.lang1.split(", ").length;
      }
      else if(this.answerLang == 2){
        this.wordsToWrite = this.activeWord.lang2.split(", ").length;
      } //mamy odpowiedni answerList
      if(this.timeForAnswer != 0){  //jeśli jest ustawiony czas
        if(!this.wasLate){
          this.remainingTime = parseInt(this.timeForAnswer);
        }
        else {
          this.remainingTime = parseInt(this.timeForAnswer)+1;
        }
        this.wasLate = false;
        clearTimeout(this.answerInterval);
        this.answerInterval = setInterval(() => {
          if(this.remainingTime == 0){
            this.wasLate = true;
            this.accept();
          }
          this.remainingTime--;
        }, 1000);
      }
      
      
      
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
      
      const inputList = this.answer.split(",");
      inputList.forEach((word, index) => {
        inputList[index] = word.toLowerCase().trim();
      }); //mamy inputList czyli słowa które wpisał user

      let answerList = [];
      if(this.answerLang == 1){
        answerList = this.activeWord.lang1.toLowerCase().split(", ");
      }
      else if(this.answerLang == 2){
        answerList = this.activeWord.lang2.toLowerCase().split(", ");
      } //mamy odpowiedni answerList

      let correctCount = 0;
      answerList.forEach(correctWord => {
        if(inputList.includes(correctWord)){
          correctCount++;
        }
      })

      if(inputList.length > answerList.length){
        correctCount -= inputList.length - answerList.length;
        if(correctCount < 0){
          correctCount = 0;
        }
      }


      let points = correctCount/answerList.length;
      if(!this.wasIncorrect(this.activeWord)){
        this.result.correct += points;
      }
      if(points == 1){
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
    },
    setTime(){
      return "width: "+this.remainingTime/this.timeForAnswer*100+"%";
    },
    setRemaining(){
      return "height: "+this.result.correct/this.result.all*100+"% ; width: 100%";
    }
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  flex-wrap: wrap;
}
@media only screen and (max-width: 600px) {
  .page {
    justify-content: center;
  }
  .answer {
    min-width: 200px !important;
  }
  .remaining-progressbar {
    min-width: 40px !important;
  }
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
  flex: 1;
  min-width: 300px;
  height: auto;
  display: flex;
  flex-direction: row;
  justify-content: space-around;
}
.table-test {
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
  margin-bottom: 30px;
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
  display: flex;
  flex-direction: row;
}
.wordsToWrite {
  margin-left: .5rem;
}
.answer {
  width: 50%;
  min-width: 300px;
}
.accept {
  width: 90% !important;
}
.help {
  margin: 0;
}
.hint {
  height: 20px;
}
.custom-range {
  width: 90%;
}
.slider {
  display: flex;
  flex-direction: column;
  flex: 1;
}
.sliderPanel {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  width: 90%;
}
.sliderPanel h3 {
  margin: 0;
  width: 30px;
  text-align: center;
}
.time {
  width: 90%;
}
.remaining-progressbar {
  min-width: 80px;
  min-height: 250px;
  display: flex;
  align-items: flex-end;
  float: left;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
