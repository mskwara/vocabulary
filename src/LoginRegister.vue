<template>
  <div class="page">
    <div class="loginpanel">
      <h1>Logowanie</h1>
      <md-field>
        <label>Nick</label>
        <md-input v-model="loginData.nick"></md-input>
      </md-field>
      
      <md-field>
        <label>Hasło</label>
        <md-input v-model="loginData.password" type="password"></md-input>
      </md-field>
      <button type="button" class="btn btn-primary login" @click="login()">Zaloguj się</button>
      <spinner class="spinner" v-if="logging" />
    </div>
    
    <div class="registerpanel">
      <h1>Rejestracja</h1>
      <md-field>
        <label>Nick</label>
        <md-input v-model="registerData.nick"></md-input>
        <span class="md-helper-text">Co najmniej 4-literowy</span>
      </md-field>
      
      <md-field>
        <label>Hasło</label>
        <md-input v-model="registerData.password" type="password"></md-input>
        <span class="md-helper-text">Co najmniej 5-literowe</span>
      </md-field>
      
      <button type="button" class="btn btn-warning" @click="register()">Zarejestruj się</button>
    </div>

    <md-dialog-alert
      :md-active.sync="failed"
      md-title="Nie udało się zarejestrować!"
      md-content="Sprawdź poprawność wprowadzanych danych!" />
    
    <md-dialog-alert
      :md-active.sync="notUniqueNick"
      md-title="Nie udało się zarejestrować!"
      md-content="Podany nick jest już używany!" />
    
    <md-dialog-alert
      :md-active.sync="success"
      md-title="Zarejestrowano pomyślnie!"
      md-content="Twoje konto jest gotowe! Przejdź do sekcji logowania." />
    
  </div>
</template>

<script>
import Spinner from './Spinner.vue';
import service from './service.js';

export default {
  name: 'LoginRegister',
  components: {
     Spinner
  },
  data(){
    return {
      registerData: {
        nick: "",
        password: ""
      },
      loginData: {
        nick: "",
        password: ""
      },
      failed: false,
      notUniqueNick: false,
      success: false,
      logging: false,
    }
  },
  mounted(){
    window.addEventListener("keypress", e => {
      if(this.loginData.nick != "" && this.loginData.password != "" && e.keyCode == 13){
        this.login();
      }
    });
  },
  methods: {
    register(){
      if(this.registerData.nick != "" && this.registerData.password != "" && this.registerData.nick.length >= 4 && this.registerData.password.length >= 5){
        this.$http.post('validateUniqueNick', this.registerData).then(response => {
            if(response.body == "true"){
              this.$http.post('users/add', this.registerData).then(()=>{
                this.registerData.nick = "";
                this.registerData.password = "";
                this.success = true;
              });
            }
            else {
              this.notUniqueNick = true;
              this.registerData.nick = "";
              this.registerData.password = "";
            }
        });
      }
      else {
        this.failed = true;
      }
    },
    login(){
      if(this.loginData.nick != "" && this.loginData.password != "") {
        this.logging = true;
        this.$http.post('validateLogin', this.loginData).then(response => {
          if(response.body == "true"){
              this.$emit("authenticated", true);
              service.authenticated = true;
              
              this.$http.get('users/'+this.loginData.nick).then(response => {
                service.nick = this.loginData.nick;
                service.id = response.body[0].id;
                this.$router.replace({ name: "home" }).catch(() => {});
                this.loginData.nick = "";
                this.loginData.password = "";
                this.logging = false;
              });
          } else {
              this.failed = true;
              this.logging = false;
          }
        }).catch(error => {
          alert(error);
        });
      } else {
          this.failed = true;
      }
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
  flex-wrap: wrap;
  margin: 20px 30px;
}
.loginpanel {
  width: 40%;

  min-width: 300px;
  margin-bottom: 30px;
}
.registerpanel {
  margin-top: 15px;
  width: 25%;
  min-width: 300px;
}
.registerpanel h1 {
  font-size: 20pt;
}
.login {
  width: 100%;
}
button {
  margin-top: 10px;
}
</style>
