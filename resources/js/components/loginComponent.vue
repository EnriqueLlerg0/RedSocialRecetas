<script>
    export default {
        name:"loginComponent",
        data(){
          return{
              email:"",
              contra:"",
              translations: {},
              currentLang: 'es',
              form: {
                  num:'',
                  emailUser:''
              },
              successMessage: '',
              Contrasena:''
          }
        },
        methods:{
            loadTranslations(lang) {
                axios.get(`/translations/${lang}`)
                    .then(response => {
                        this.translations = response.data;
                        this.currentLang = lang;
                    })
                    .catch(error => {
                        console.error('Error loading translations:', error);
                    });
            },
            translate(key) {
                return this.translations[key] || key;
            },
            changeLanguage(lang) {
                this.loadTranslations(lang);
            },
            getTranslations() {
                axios.get(`/getTranslations`)
                    .then(response => {
                        this.currentLang = response.data;
                    })
                    .catch(error => {
                        console.error('Error loading translations:', error);
                    });
            },
            actionLink(route){
                this.$router.push(route);
            },
            comprobarUsuario(){
                axios.post("/checkUsu", {email:this.email, password:this.contra}).then(response =>
                {
                    if(response.data.status != false)
                    {
                        localStorage.setItem('user', JSON.stringify(response.data.status));
                        this.actionLink('/home');
                        Swal.fire({
                            position: "bottom-end",
                            icon: "success",
                            title: "Welcome "+response.data.status.user,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    else
                    {
                        Swal.fire({
                            title: "email o contraseña incorrectos",
                            icon: "error"
                        });
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        console.log(error.response.data.errors);
                        Swal.fire({
                            icon: "error",
                            text: "Error, el usuario no se a podido logear"
                        });
                    } else if(error.response.status === 500) {
                        console.error(error);
                    } else {
                        console.error(error);
                    }
                });
            },
            modalShow1() {
                Swal.fire({
                    title: 'Ingresa el correo',
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    confirmButtonText: 'Confirmar',
                    preConfirm: (nombre) => {
                        return nombre;
                    }
                }).then((result) => {
                    if (result.value!=""&&result.value!=undefined)
                    {
                        this.form.emailUser=result.value;
                        this.sendContactEmail();
                    }
                    else
                    {
                        Swal.fire("ERROR, tienes que introducir un correo", "", "error");
                    }
                });
            },
            sendContactEmail() {
                this.form.num = Math.floor(1000 + Math.random()*9000);
                axios.post('/contact', this.form)
                    .then(response => {
                        this.successMessage = response.data.success;
                        this.modalShow2();
                    })
                    .catch(error => {
                        console.error('Error sending email:', error);
                    });
            },
            modalShow2() {
                Swal.fire({
                    title: 'Ingresa el token',
                    input: 'text',
                    showCancelButton: true,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    confirmButtonText: 'Confirmar',
                    preConfirm: (nombre) => {
                        return nombre;
                    }
                }).then((result) => {
                    if (result.value==this.form.num)
                    {
                        this.obtenerDatoContrasena();
                    }
                    else
                    {
                        Swal.fire("ERROR, el token es incorrecto", "", "error");
                    }
                });
            },
            async obtenerDatoContrasena() {
                Swal.fire({
                    title: 'Ingresa la contraseña',
                    input: 'password',
                    showCancelButton: true,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    confirmButtonText: 'Confirmar',
                    preConfirm: (nombre) => {
                        return nombre;
                    }
                }).then((result) => {

                    if (result.isConfirmed) {
                        if (result.value != "") {
                            this.Contrasena = result.value;
                            this.actualizarContrasena();
                        } else {
                            Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                        }
                    }
                });
            },
            async actualizarContrasena(){
                await axios.put("/actualizarContraseñaNoLogin", {email: this.form.emailUser, contra:this.Contrasena}).then((response) => {

                }).catch(error => {
                    console.error(error);
                });
            }
        },
        created() {
            this.getTranslations();
            this.loadTranslations(this.currentLang);
        }
    }
</script>

<template class="antialiased" style="background-color: #9A616D;">

    <section class="vh-100" style="background-color: #9A616D;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 1rem;">
                        <div class="row g-0">
                            <div class="col-md-6 col-lg-5 d-none d-md-block">
                                <img src="img/bg_1.jpg"
                                     alt="login form" class="img-fluid recortar h-100" style="border-radius: 1rem 0 0 1rem;" />
                            </div>
                            <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                <div class="card-body p-4 p-lg-5 text-black">

                                    <form>

                                        <div class="d-flex justify-content-center align-items-center">
                                            <img class="pequeño" src="img/icono.png" alt="">
                                            <h2 class="col-9 text-center">{{ translate('Tituloh2') }}</h2>
                                        </div>

                                        <h5 class="fw-normal" style="letter-spacing: 1px;">{{ translate('Descripcionh5') }}</h5>

                                        <div class="form-outline">
                                            <input type="email" id="form2Example17" v-model="email" class="form-control form-control-lg font-size-input" />
                                            <label class="form-label" for="form2Example17">{{ translate('EmailLabel') }}</label>
                                        </div>

                                        <div class="form-outline">
                                            <input type="password" id="form2Example27" v-model="contra" class="form-control form-control-lg font-size-input" />
                                            <label class="form-label" for="form2Example27">{{ translate('PasswordLabel') }}</label>
                                        </div>

                                        <div class="pt-1">
                                            <button class="btn btn-dark btn-lg btn-block" type="button" v-on:click.prevent="comprobarUsuario()">
                                                {{ translate('LoginButton') }}</button>
                                        </div>

                                        <span class="small text-muted pointer" v-on:click="modalShow1()"><u>{{ translate('ForgotA') }}</u></span>
                                        <div class="d-flex" data-justify-content="space-between">
                                            <p class="" style="color: #393f81;">{{ translate('RegisterP') }} <a href="/register" style="color: #393f81;">{{ translate('RegisterA') }}</a></p>
                                            <div>
                                                <img v-if="currentLang==='en'" src="../../../public/img/en-color.png" class="w-30-px">
                                                <img v-else src="../../../public/img/en-negro.png" v-on:click="changeLanguage('en')" class="w-30-px pointer">
                                                <b> - </b>
                                                <img v-if="currentLang==='es'" src="../../../public/img/es-color.png" class="w-30-px">
                                                <img v-else src="../../../public/img/es-negro.png" v-on:click="changeLanguage('es')" class="w-30-px pointer">
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
