<template>
    <div>
        <spinner v-if="loading" />
        <transition name="fade">
            <div class="page" v-if="!loading && sharedLists != null">
                <div class="panel">
                    <label class="selectLabel">Wybierz listę</label>
                    <select
                        class="custom-select"
                        v-model="activeList"
                        :disabled="testRunning"
                        @change="getWords()"
                    >
                        <option
                            :value="list"
                            :key="list.id"
                            v-for="list in sharedLists"
                        >{{list.title}}</option>
                    </select>

                    <p class="header">Język odpowiedzi</p>
                    <div class="radios">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input
                                type="radio"
                                id="radio1"
                                name="answerLang"
                                class="custom-control-input"
                                value="1"
                                v-model="answerLang"
                                :disabled="testRunning"
                            />
                            <label
                                class="custom-control-label labellang"
                                for="radio1"
                            >{{activeList.lang1}}</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input
                                type="radio"
                                id="radio2"
                                name="answerLang"
                                class="custom-control-input"
                                value="2"
                                v-model="answerLang"
                                :disabled="testRunning"
                            />
                            <label
                                class="custom-control-label labellang"
                                for="radio2"
                            >{{activeList.lang2}}</label>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn btn-success"
                        @click="getWordsAndRun()"
                        :disabled="answerLang == null || activeList == null || testRunning"
                    >Rozpocznij</button>

                    <transition name="fade">
                        <div class="scoreboard" v-if="scoreboard != null">
                            <h3 class="header">Ranking</h3>
                            <ul class="list-group list-group-flush">
                                <li
                                    class="list-group-item score"
                                    :key="score.id"
                                    v-for="(score, index) in scoreboard"
                                >
                                    <p>
                                        <b>{{index+1}}. {{score.nick}}</b>
                                    </p>
                                    <p>{{score.correct}}/{{score.allCount}}.0</p>
                                    <p v-if="score.attempts >= 5">{{score.attempts}} podejść</p>
                                    <p
                                        v-if="score.attempts >= 2 && score.attempts <= 4"
                                    >{{score.attempts}} podejścia</p>
                                    <p v-if="score.attempts == 1">{{score.attempts}} podejcie</p>
                                </li>
                            </ul>
                        </div>
                    </transition>
                </div>

                <transition name="fade">
                    <div class="table" v-if="testRunning">
                        <div class="table-test">
                            <div class="activeWord" :style="getActiveWordFontSize()">
                                <p v-if="answerLang == 2">{{activeWord.lang1}}</p>
                                <p v-else>{{activeWord.lang2}}</p>
                                <p class="wordsToWrite" v-if="wordsToWrite > 1">({{wordsToWrite}})</p>
                            </div>

                            <md-field class="answer">
                                <label class="labellang" v-if="answerLang == 1">{{activeList.lang1}}</label>
                                <label class="labellang" v-else>{{activeList.lang2}}</label>
                                <md-input v-focus v-model="answer"></md-input>
                            </md-field>
                            <div class="progress time" v-if="timeForAnswer != 0">
                                <div
                                    class="progress-bar bg-info"
                                    role="progressbar"
                                    :style="setTime()"
                                    aria-valuemin="0"
                                    :aria-valuemax="timeForAnswer"
                                ></div>
                            </div>
                            <div class="hint">
                                <transition name="fade">
                                    <div v-if="hintVisible">
                                        <p class="help" :style="getHintFontSize()">{{hintText}}</p>
                                    </div>
                                </transition>
                            </div>
                            <button
                                type="button"
                                class="btn btn-primary accept"
                                @click="accept()"
                            >Zatwierdź</button>
                        </div>
                        <div class="progress remaining-progressbar">
                            <div
                                class="progress-bar-striped progress-bar-animated bg-info"
                                role="progressbar"
                                :style="setRemaining()"
                                aria-valuemin="0"
                                :aria-valuemax="result.all"
                            ></div>
                        </div>
                    </div>
                </transition>

                <transition name="fade">
                    <div class="rightPanel" v-if="!testRunning">
                        <transition name="fade">
                            <div class="words" v-if="!loadingNewWords">
                                <h3 class="header">Słówka w tej liście</h3>
                                <ul class="list-group">
                                    <li
                                        class="list-group-item word"
                                        :key="word.id"
                                        v-for="word in words"
                                    >
                                        <p class="wordContent">{{getWordTitle(word)}}</p>
                                    </li>
                                </ul>
                            </div>
                        </transition>
                        <spinner v-if="loadingNewWords" />
                        <spinner v-if="loadingScoreboard" />
                    </div>
                </transition>

                <md-dialog-alert
                    :md-active.sync="displayResults"
                    md-title="Koniec testu"
                    :md-content="displayResultsText"
                />
            </div>
            <div v-if="!loading && sharedLists == null">
                <p class="noLists">Nikt nie udostępnił jeszcze swojej listy...</p>
            </div>
        </transition>
    </div>
</template>

<script>
import Spinner from "./Spinner.vue";
import service from "./service.js";

export default {
    name: "Test",
    components: {
        Spinner
    },
    directives: {
        focus: {
            inserted: function(el) {
                el.focus();
            }
        }
    },
    data() {
        return {
            answer: "",
            sharedLists: null,
            words: null,
            answerLang: null,
            activeWord: {},
            testRunning: false,
            result: {
                correct: 0,
                all: 0,
                listId: null,
                nick: service.nick
            },
            displayResults: false,
            displayResultsText: "",
            remaining: 0,
            hintVisible: false,
            hintText: "",
            hintTimeout: null,
            loading: true,
            loadingNewWords: true,
            loadingScoreboard: true,
            noWords: false,
            timeForAnswer: 10,
            remainingTime: 0,
            answerInterval: null,
            wasLate: false,
            wordsToWrite: 1,
            scoreboard: null
        };
    },
    mounted() {
        this.getLists();
        window.addEventListener("keypress", e => {
            if (this.testRunning && e.keyCode == 13) {
                this.accept();
            }
        });
    },
    methods: {
        getLists() {
            this.$http.get("sharedLists").then(response => {
                this.sharedLists = response.body;
                if (this.sharedLists != null) {
                    this.activeList = this.sharedLists[0];
                    this.getWords();
                    this.getScoreboard();
                } else {
                    this.activeList = null;
                }
                this.loading = false;
            });
        },
        getWords() {
            if (this.activeList != null) {
                this.$http
                    .get("words/" + this.activeList.listId)
                    .then(response => {
                        this.words = response.body;
                        this.loadingNewWords = false;
                    });
            } else {
                this.words = null;
            }
        },
        getScoreboard() {
            if (this.activeList != null) {
                this.$http
                    .get("scoreboard/" + this.activeList.listId)
                    .then(response => {
                        this.scoreboard = response.body;
                        this.scoreboard.sort((a, b) => {
                            return (
                                b.correct / b.allCount - a.correct / a.allCount
                            );
                        });
                        this.loadingScoreboard = false;
                    });
            } else {
                this.scoreboard = null;
            }
        },
        getWordsAndRun() {
            this.$http.get("words/" + this.activeList.listId).then(response => {
                if (response.body != null) {
                    this.words = response.body;
                    this.shuffleArray(this.words);
                    this.testRunning = true;
                    this.result.correct = 0;
                    this.result.all = this.words.length;
                    this.result.listId = this.activeList.listId;
                    this.remaining = this.words.length;
                    this.displayNextWord();
                } else {
                    this.words = null;
                    this.noWords = true;
                }
            });
        },
        getWordTitle(word) {
            return word.lang1 + " - " + word.lang2;
        },
        shuffleArray(array) {
            var counter = array.length,
                temp,
                index;
            while (counter > 0) {
                index = Math.floor(Math.random() * counter);
                counter--;
                temp = array[counter];
                array[counter] = array[index];
                array[index] = temp;
            }
        },
        shuffleActiveWord(array) {
            var temp, index;
            index = Math.floor(Math.random() * array.length);
            temp = array[0];
            array[0] = array[index];
            array[index] = temp;
        },
        displayNextWord() {
            if (this.remaining == 0) {
                clearTimeout(this.answerInterval);
                var numeral = require("numeral");
                var percent = numeral(
                    ((this.result.correct * 1.0) / this.result.all) * 100
                ).format("0.00");
                this.result.correct = numeral(this.result.correct).format(
                    "0.0"
                );
                this.displayResultsText =
                    "Twój wynik to " +
                    this.result.correct +
                    "/" +
                    this.result.all +
                    ".0, co stanowi " +
                    percent +
                    "%";
                this.displayResults = true;
                this.testRunning = false;
                this.$http.post("sharedStats/add", this.result);
                this.getWords();
                this.getScoreboard();
                return;
            }
            this.activeWord = this.words[0];
            if (this.answerLang == 1) {
                this.wordsToWrite = this.activeWord.lang1.split(", ").length;
            } else if (this.answerLang == 2) {
                this.wordsToWrite = this.activeWord.lang2.split(", ").length;
            } //mamy odpowiedni answerList
            if (this.timeForAnswer != 0) {
                //jeśli jest ustawiony czas
                if (!this.wasLate) {
                    this.remainingTime = parseInt(this.timeForAnswer);
                } else {
                    this.remainingTime = parseInt(this.timeForAnswer) + 1;
                }
                this.wasLate = false;
                clearTimeout(this.answerInterval);
                this.answerInterval = setInterval(() => {
                    if (this.remainingTime == 0) {
                        this.wasLate = true;
                        this.accept();
                    }
                    this.remainingTime--;
                }, 1000);
            }
        },
        accept() {
            const inputList = this.answer.split(",");
            inputList.forEach((word, index) => {
                inputList[index] = word.toLowerCase().trim();
            }); //mamy inputList czyli słowa które wpisał user

            let answerList = [];
            if (this.answerLang == 1) {
                answerList = this.activeWord.lang1.toLowerCase().split(", ");
            } else if (this.answerLang == 2) {
                answerList = this.activeWord.lang2.toLowerCase().split(", ");
            } //mamy odpowiedni answerList

            let correctCount = 0;
            answerList.forEach(correctWord => {
                if (inputList.includes(correctWord)) {
                    correctCount++;
                }
            });

            if (inputList.length > answerList.length) {
                correctCount -= inputList.length - answerList.length;
                if (correctCount < 0) {
                    correctCount = 0;
                }
            }

            let points = correctCount / answerList.length;
            this.result.correct += points;

            this.remaining--;
            this.words.splice(0, 1);

            if (points < 1) {
                if (this.answerLang == 2) {
                    this.hintText =
                        this.activeWord.lang1 + " - " + this.activeWord.lang2;
                } else {
                    this.hintText =
                        this.activeWord.lang2 + " - " + this.activeWord.lang1;
                }
                clearTimeout(this.hintTimeout);
                this.hintVisible = true;
                this.hintTimeout = setTimeout(() => {
                    this.hintVisible = false;
                }, 5000);
            }
            this.displayNextWord();
            this.answer = "";
        },
        setTime() {
            return (
                "width: " +
                (this.remainingTime / this.timeForAnswer) * 100 +
                "%"
            );
        },
        setRemaining() {
            return (
                "height: " +
                (this.remainingTime / this.timeForAnswer / this.result.all) *
                    100 +
                "% ; width: 100%"
            );
        },
        getPercent(correct, all) {
            var numeral = require("numeral");
            var percent = numeral(((correct * 1.0) / all) * 100).format("0.00");
            return percent;
        },
        getActiveWordFontSize() {
            const len =
                this.answerLang == 2
                    ? this.activeWord.lang1.length
                    : this.activeWord.lang2.length;
            let size = 25;
            if (len >= 25) {
                size -= (len - 25) * 0.9;
            }
            return "font-size: " + size + "pt";
        },
        getHintFontSize() {
            const len = this.hintText.length;
            let size = 11;
            if (len >= 40) {
                size -= (len - 40) * 0.15;
            }
            return "font-size: " + size + "pt";
        }
    }
};
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
.words {
    width: 50%;
    min-width: 300px;
    height: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.rightPanel {
    flex: 1;
    min-width: 300px;
    height: auto;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    flex-wrap: wrap;
}
.header {
    text-align: center;
}
.list-group {
    width: 90%;
}
.word {
    height: 50px;
}
.scoreboard {
    min-width: 200px;
    width: 90%;
}
.score {
    text-align: center;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    padding-left: 0;
    padding-right: 0;
}
.table {
    flex: 1;
    min-width: 300px;
    height: auto;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    flex-wrap: wrap;
}
.table-test {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 400px;
}
button {
    width: 90% !important;
    margin-top: 30px;
    margin-bottom: 30px;
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
    margin-left: 0.5rem;
}
.answer {
    width: 50%;
    min-width: 300px;
}
.accept {
    width: 90% !important;
    max-width: 250px;
}
.help {
    margin: 0;
    font-size: 11pt;
}
.hint {
    height: 20px;
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
.noLists {
    text-align: center;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>
