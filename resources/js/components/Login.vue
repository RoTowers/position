<template>
  <v-app id="login">
    <v-main>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
           <v-col cols="12" sm="6" md="6">
              <v-card padless class="v-card-main">
                <v-row>
                  <!-- <v-col cols="12" md="4" class="main-theme accent-3">
                    <v-card-text class="white--text">
                      <h1 class="text-center display-1">Hola, Amigo!</h1>
                      <h5
                        class="text-center"
                      >Bienvenido al sitio AltSolutions MMA</h5>
                    </v-card-text>
                  </v-col> -->
                  <v-col cols="12" md="12">
                    <v-card-text class="mt-12">
                      <h1
                        class="text-center display-2 text-main-theme text--accent-3"
                      >Inicia Sesión</h1>
                      <v-form @submit.prevent="login">
                        <v-text-field
                          label="Email"
                          name="email"
                          prepend-icon="mdi-account"
                          type="text"
                          v-model="fields.email"
                          :error-messages="errors && errors.email ? errors.email[0] : ''"
                        />

                        <v-text-field
                          id="password"
                          label="Password"
                          name="password"
                          prepend-icon="mdi-lock"
                          :type="value ? 'password' : 'text'"
                          :append-icon="value ? 'mdi-eye' : 'mdi-eye-off'"
                          @click:append="() => (value = !value)"
                          v-model="fields.password"
                          :error-messages="errors && errors.password ? errors.password[0] : ''"
                        />
                        <h3 class="text-center mt-4"><a class="hl-text" href="/password/reset">¿Olvidaste tu contraseña?</a></h3>
                        <div class="text-center mt-3" style="margin-bottom: 2em;">
                      <v-btn rounded color="main-theme accent-3" dark type="submit">INGRESAR</v-btn>
                    </div>
                      </v-form>
                      
                    </v-card-text>
                    
                  </v-col>
                </v-row>
              </v-card>
           </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
  /* .main-theme {
    background: rgb(131,83,212);
    background: linear-gradient(126deg, rgba(131,83,212,1) 0%, rgba(194,98,159,1) 100%);
  }
  .text-main-theme {
    color: #9c27b0;
  } */

  .hl-text {
    text-decoration: none;
  }
  
  .v-card-main {
    border-radius: 0 !important;
  }
</style>

<script>

export default {
  data: () => ({
    step: 1,
    fields: {
    },
    value: String,
    errors: {},
    user: null
  }),
  props: {
    source: String,
    /* passwordRoute: ['passwordRoute'],
    loginRoute: ['loginRoute'], */
  },
  methods:{
      login:function(){
        this.errors = {};
        axios.post('/api/login', this.fields).then(response => {
          window.location.href = "/home";
        }).catch(error => {
            if (error.response.status === 422) {
              this.errors = error.response.data.errors || {};
            }
        });
      },
  },
};
</script>