<script>
    export default {
        name:"registerComponent",
        data(){
            return{
                usuario:"",
                email:"",
                contraseña:"",
                contraseña2:"",
                translations: {},
                currentLang: 'es',
                form: {
                    num:'',
                    emailUser:''
                },
                successMessage: '',
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
            newUser(){
                if(this.usuario!=""&&this.email!=""&&this.contraseña!=""&&this.contraseña2!="")
                {
                    if(this.contraseña!=this.contraseña2)
                    {
                        Swal.fire({
                            icon: "error",
                            text: "Las contraseñas deben de ser iguales"
                        });
                    }
                    else
                    {
                        this.form.emailUser=this.email;
                        this.sendContactEmail();
                    }
                }
                else
                {
                    Swal.fire({
                        icon: "error",
                        text: "Error, debes de rellenar todos los campos"
                    });
                }
            },
            sendContactEmail() {
                this.form.num = Math.floor(1000 + Math.random()*9000);
                axios.post('/contact', this.form)
                    .then(response => {
                        this.successMessage = response.data.success;
                        this.modalShow3();
                    })
                    .catch(error => {
                        console.error('Error sending email:', error);
                    });
            },
            modalShow3() {
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
                        this.createUser();
                    }
                    else
                    {
                        Swal.fire("ERROR, el token es incorrecto", "", "error");
                    }
                });
            },
            createUser(){
                axios.post("/subir", {usu:this.usuario, ema:this.email, contra:this.contraseña}).then((response) => {
                    Swal.fire({
                        position: "bottom-end",
                        icon: "success",
                        title: "Usuario creado",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }).catch(error => {
                    if (error.response.status === 422) {
                        console.log(error.response.data.errors);
                        Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                    } else {
                        console.error(error);
                    }
                });
            }
        },
        created() {
            this.getTranslations();
            this.loadTranslations(this.currentLang);
        }
    }
</script>

<template>
    <section class="vh-100 d-flex justify-content-center align-items-center" style="background-color: #9A616D;">
        <div class="bg-white rounded-4">
            <form class="p-5 d-flex flex-column">

                <div class="d-flex justify-content-center">
                    <img class="pequeño" src="img/icono.png" alt="">
                </div>

                <h5 class="fw-normal text-center" style="letter-spacing: 1px;">{{ translate('Tituloh5') }}</h5>

                <div class="form-outline mt-3">
                    <input type="text" id="form2Example17" class="form-control form-control-lg font-size-input" v-model="usuario" :placeholder="translate('UsuarioInput')" />
                </div>

                <div class="form-outline mt-3">
                    <input type="email" id="form2Example17" class="form-control form-control-lg font-size-input" v-model="email" :placeholder="translate('EmailInput')" />
                </div>

                <div class="form-outline mt-3">
                    <input type="password" id="form2Example27" class="form-control form-control-lg font-size-input" v-model="contraseña" :placeholder="translate('ContraseñaInput')" />
                </div>

                <div class="form-outline mt-3">
                    <input type="password" id="form2Example27" class="form-control form-control-lg font-size-input" v-model="contraseña2" :placeholder="translate('RepiteInput')" />
                </div>

                <div class="pt-1 mb-3 mt-3">
                    <button class="btn btn-dark btn-lg btn-block" type="button" v-on:click.prevent="newUser()">{{ translate('SignButton') }}</button>
                </div>

                <div class="d-flex" data-justify-content="space-between">
                    <a href="/" style="color: #393f81;">{{ translate('VolverA') }}</a>
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
    </section>
</template>

<style scoped>

</style>
