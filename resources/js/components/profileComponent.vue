<script>
export default {
    name:"profileComponent",
    data(){
        return{
            recipeId:'',
            isOpen1:true,
            isopen2:'hidden',
            idUser:"",
            imagen:"",
            Username:"",
            Email:"",
            Contrasena:"",
            allRecipe:[],
            allDataUser:[],
            isVisible:false,
            recipeData:[],
            idRecipe:'',
            nameRecipe:'',
            imagenRecetaActualizada:"",
            titulo:"",
            nombreIngredientes:[],
            cantidadIngredientes:[],
            filtroIngredientes:[],
            descripcion:"",
            selectedFile: null,
            imageUrl:null,
            pais:"",
            bandera:1,
            translations: {},
            currentLang: 'es',
            form: {
                num:'',
                emailUser:''
            },
            successMessage: '',
            isArrowVisible1:false,
            isArrowVisible2:false,
            rolUser:''
        }
    },
    methods:{
        actionLink(route){
            this.$router.push(route);
        },
        previewImage(event) {
            this.selectedFile = event.target.files[0];
            this.actualizarImagen();
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagen = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagen = '';
            }
        },
        previewImage2(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagenRecetaActualizada = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagenRecetaActualizada = '';
            }
        },
        async actualizarImagen(){

            const formData = new FormData();
            formData.append('image', this.selectedFile);

            try {
                const response = await axios.post('/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                this.imageUrl = response.data.url;

                await axios.put("/actualizarImagen", {idUser: this.idUser, img:this.imageUrl}).then((response) => {
                    this.bandera=1;
                    this.obtenerDatosUsuario();
                }).catch(error => {
                    if (error.response.status === 422) {
                        console.log(error.response.data.errors);
                        Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                    } else {
                        console.error(error);
                    }
                });
            } catch (error) {
                console.error('There was an error uploading the file!', error);
            }
        },
        async actualizarUsuario(){
            await axios.put("/actualizarUsuario", {idUser: this.idUser, Username:this.Username}).then((response) => {
                this.obtenerDatosUsuario();
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        async actualizarEmail(){
            await axios.put("/actualizarEmail", {idUser: this.idUser, email:this.Email}).then((response) => {
                this.obtenerDatosUsuario();
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        async actualizarContrasena(){
            await axios.put("/actualizarContraseña", {idUser: this.idUser, contra:this.Contrasena}).then((response) => {
            }).catch(error => {
                console.error(error);
            });
        },
        obtenerDatoUsuario(){
            Swal.fire({
                title: 'Ingresa el usuario',
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

                if (result.isConfirmed)
                {
                    if(result.value!="")
                    {
                        this.Username = result.value;
                        this.actualizarUsuario();
                    }
                    else
                    {
                        Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                    }
                }
            });
        },
        obtenerDatoEmail(){
            Swal.fire({
                title: 'Ingresa el email',
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

                if (result.isConfirmed)
                {
                    if(result.value!="")
                    {
                        this.Email = result.value;
                        this.actualizarEmail();
                    }
                    else
                    {
                        Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                    }
                }
            });
        },
        async obtenerDatosUsuario(){
            this.isVisible=true;
            await axios.get("/allDataUser", {
                params: {
                    idUser: this.idUser
                }
            }).then((response) => {
                this.allDataUser = response.data.dataUser;
                this.imagen=this.allDataUser.img;
                this.isVisible=false;
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        async obtenerDatoContrasena(){
            Swal.fire({
                title: 'Ingresa la contraseña',
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

                if (result.isConfirmed)
                {
                    if(result.value!="")
                    {
                        this.Contrasena = result.value;
                        this.actualizarContrasena();
                    }
                    else
                    {
                        Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                    }
                }
            });
        },
        cerrarSesion()
        {
            localStorage.removeItem("user");
        },
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
                    console.log(this.currentLang);
                })
                .catch(error => {
                    console.error('Error loading translations:', error);
                });
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
                    this.obtenerDatoContrasena();
                }
                else
                {
                    Swal.fire("ERROR, el token es incorrecto", "", "error");
                }
            });
        }
    },
    mounted() {
        this.rolUser = JSON.parse(localStorage.getItem('user'))['roll'];
        this.getTranslations();
        this.loadTranslations(this.currentLang);

        this.idUser=JSON.parse(localStorage.getItem('user'))['id'];
        this.obtenerDatosUsuario();

        this.form.emailUser=JSON.parse(localStorage.getItem('user'))['email'];
    },
    watch: {
        imagen(newVal, oldVal) {
            if (newVal !== oldVal && this.bandera==0) {
                console.log("entro");
                this.actualizarImagen();
            }
            else
            {
                this.bandera=0
            }
        }
    }
}
</script>

<template>
    <div class="d-flex flex-wrap">

        <section>
            <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                <div class="loader"></div>
            </div>
        </section>

        <div class="mobile-item"  v-bind:class="{ 'd-none': !isArrowVisible1}">
            <section class="col-12 border d-flex flex-column seccionInicioMobile fondo2">

                <div class="d-flex justify-content-center mb-5">
                    <i class="fa-regular fa-xmark p-15 abs-pos l-10-px pointer" data-size="30" v-on:click="this.isArrowVisible1=false"></i>
                    <img class="pequeño" src="img/icono.png" alt="">
                </div>

                <button class="text-decoration-none border-0 menuPrincipal" v-on:click="actionLink('/home')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house"
                         viewBox="0 0 16 16">
                        <path
                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
                    </svg>
                    {{ translate('InicioMenu') }}
                </button>

                <button class="text-decoration-none border-0 menuPrincipal" v-on:click="actionLink('/favorite')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-bookmark-star" viewBox="0 0 16 16">
                        <path
                            d="M7.84 4.1a.178.178 0 0 1 .32 0l.634 1.285a.18.18 0 0 0 .134.098l1.42.206c.145.021.204.2.098.303L9.42 6.993a.18.18 0 0 0-.051.158l.242 1.414a.178.178 0 0 1-.258.187l-1.27-.668a.18.18 0 0 0-.165 0l-1.27.668a.178.178 0 0 1-.257-.187l.242-1.414a.18.18 0 0 0-.05-.158l-1.03-1.001a.178.178 0 0 1 .098-.303l1.42-.206a.18.18 0 0 0 .134-.098z"/>
                        <path
                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z"/>
                    </svg>
                    {{ translate('FavoritosMenu') }}
                </button>

                <button class="text-decoration-none  text-center border-0 menuPrincipal" v-on:click="actionLink('/up')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-newspaper" viewBox="0 0 16 16">
                        <path
                            d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"/>
                        <path
                            d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"/>
                    </svg>
                    {{ translate('RecetasMenu') }}
                </button>

                <button class="text-decoration-none text-center border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/profile')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd"
                              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    {{ translate('PerfilMenu') }}
                </button>

                <button class="text-decoration-none  text-center border-0 menuPrincipal"
                        v-on:click="cerrarSesion(), actionLink('/')">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    {{ translate('CerrarSesionMenu') }}
                </button>

                <button v-if="this.rolUser=='admin'" class="text-decoration-none  text-center border-0 menuPrincipal"
                        v-on:click="actionLink('/admin')">
                    <i class="fa-solid fa-user-tie"></i>
                    {{ translate('AdminMenu') }}
                </button>

                <div class="text-decoration-none text-center border-0 menuPrincipalIidiomas d-flex justify-content-center" data-gap="50">
                    <div>
                        <img v-if="currentLang==='en'" src="../../../public/img/en-color.png" class="w-30-px">
                        <img v-else src="../../../public/img/en-negro.png" v-on:click="changeLanguage('en')"
                             class="w-30-px pointer">
                        <b> - </b>
                        <img v-if="currentLang==='es'" src="../../../public/img/es-color.png" class="w-30-px">
                        <img v-else src="../../../public/img/es-negro.png" v-on:click="changeLanguage('es')"
                             class="w-30-px pointer">
                    </div>

                    <div class="hover-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                        <span class="tooltip-text" id="bottom">Enrique Llergo Maya <hr> I.E.S. Trassierra</span>
                    </div>
                </div>

            </section>
        </div>

        <div class="desktop-item">
            <section class="col-3 border d-flex flex-column seccionInicio fondo2">

                <div class="d-flex justify-content-center mb-5">
                    <img class="pequeño" src="img/icono.png" alt="">
                </div>

                <button class="text-decoration-none border-0 menuPrincipal" v-on:click="actionLink('/home')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house"
                         viewBox="0 0 16 16">
                        <path
                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
                    </svg>
                    {{ translate('InicioMenu') }}
                </button>

                <button class="text-decoration-none border-0 menuPrincipal" v-on:click="actionLink('/favorite')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-bookmark-star" viewBox="0 0 16 16">
                        <path
                            d="M7.84 4.1a.178.178 0 0 1 .32 0l.634 1.285a.18.18 0 0 0 .134.098l1.42.206c.145.021.204.2.098.303L9.42 6.993a.18.18 0 0 0-.051.158l.242 1.414a.178.178 0 0 1-.258.187l-1.27-.668a.18.18 0 0 0-.165 0l-1.27.668a.178.178 0 0 1-.257-.187l.242-1.414a.18.18 0 0 0-.05-.158l-1.03-1.001a.178.178 0 0 1 .098-.303l1.42-.206a.18.18 0 0 0 .134-.098z"/>
                        <path
                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z"/>
                    </svg>
                    {{ translate('FavoritosMenu') }}
                </button>

                <button class="text-decoration-none  text-center border-0 menuPrincipal" v-on:click="actionLink('/up')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-newspaper" viewBox="0 0 16 16">
                        <path
                            d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"/>
                        <path
                            d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"/>
                    </svg>
                    {{ translate('RecetasMenu') }}
                </button>

                <button class="text-decoration-none text-center border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/profile')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd"
                              d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                    </svg>
                    {{ translate('PerfilMenu') }}
                </button>

                <button class="text-decoration-none  text-center border-0 menuPrincipal"
                        v-on:click="cerrarSesion(), actionLink('/')">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    {{ translate('CerrarSesionMenu') }}
                </button>

                <button v-if="this.rolUser=='admin'" class="text-decoration-none  text-center border-0 menuPrincipal"
                        v-on:click="actionLink('/admin')">
                    <i class="fa-solid fa-user-tie"></i>
                    {{ translate('AdminMenu') }}
                </button>

                <div class="text-decoration-none text-center border-0 menuPrincipalIidiomas d-flex justify-content-center" data-gap="50">
                    <div>
                        <img v-if="currentLang==='en'" src="../../../public/img/en-color.png" class="w-30-px">
                        <img v-else src="../../../public/img/en-negro.png" v-on:click="changeLanguage('en')"
                             class="w-30-px pointer">
                        <b> - </b>
                        <img v-if="currentLang==='es'" src="../../../public/img/es-color.png" class="w-30-px">
                        <img v-else src="../../../public/img/es-negro.png" v-on:click="changeLanguage('es')"
                             class="w-30-px pointer">
                    </div>

                    <div class="hover-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                        <span class="tooltip-text" id="top">Enrique Llergo Maya <hr> I.E.S. Trassierra</span>
                    </div>
                </div>

            </section>
        </div>

        <div class="mobile-item">
            <div>
                    <svg class="flechasMenuIzquierdo" width="50" height="90" viewBox="40 50 90 150" xmlns="http://www.w3.org/2000/svg">
                    <g transform="rotate(180, 45, 75)">
                        <!-- Semicírculo -->
                        <path v-on:click="this.isArrowVisible1=true, this.isArrowVisible2=false" d="M50,100 a47,49 0 1,1 0,-100" fill="#94DAFF" stroke="#0079FF" stroke-width="2"/>
                        <!-- Símbolo < -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" fill="#0079FF" viewBox="-140 187 320 512">
                            <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                        </svg>
                    </g>
                </svg>
            </div>

            <section v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1">
                <div class="">
                    <div class="d-flex gap-5 mx-10 mt-10">
                        <img v-if=" imagen=='' " class="fotoPerfil2 border border-1 rounded-5 rounded-circle" src="https://static.vecteezy.com/system/resources/previews/022/014/184/non_2x/user-icon-member-login-isolated-vector.jpg" alt="">
                        <img v-else class="fotoPerfil2 border border-1 rounded-5 rounded-circle" :src="imagen" alt="">
                        <h1 class="d-flex align-items-center">{{ this.allDataUser.user }}</h1>
                    </div>
                    <div class="w-75 izquierda  mx-25 mt-5 h-65 d-flex align-items-center gap-4">
                        <div class="">
                            <h4>{{ translate('Usuarioh4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">{{ this.allDataUser.user }}</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="obtenerDatoUsuario()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Emailh4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">{{ this.allDataUser.email }}</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="obtenerDatoEmail()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Contraseñah4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">•••••••••••••••••</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="sendContactEmail()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Imagenh4') }}</h4>
                            <div>
                                <input type="file" v-on:change="previewImage" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>

        <div class="desktop-item">
            <section v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1">
                <div class=" mt-4 p-4 col-8 seccionSubirReceta2 border border-black border-1 rounded-4">
                    <div class="d-flex gap-5">
                        <img v-if=" imagen=='' " class="fotoPerfil2 border border-1 rounded-5 rounded-circle" src="https://static.vecteezy.com/system/resources/previews/022/014/184/non_2x/user-icon-member-login-isolated-vector.jpg" alt="">
                        <img v-else class="fotoPerfil2 border border-1 rounded-5 rounded-circle" :src="imagen" alt="">
                        <h1 class="d-flex align-items-center">{{ this.allDataUser.user }}</h1>
                    </div>
                    <div class="w-75 izquierda ml-1 mt-5 h-65 d-flex align-items-center gap-4">
                        <div class="col-6">
                            <h4>{{ translate('Usuarioh4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">{{ this.allDataUser.user }}</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="obtenerDatoUsuario()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Emailh4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">{{ this.allDataUser.email }}</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="obtenerDatoEmail()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Contraseñah4') }}</h4>
                            <div class="d-flex gap-4">
                                <span class="w-75" type="text">•••••••••••••••••</span>
                                <i class="fa-solid fa-pencil custom-cursor" v-on:click="sendContactEmail()"></i>
                            </div>
                            <h4 class="mt-4">{{ translate('Imagenh4') }}</h4>
                            <div>
                                <input type="file" v-on:change="previewImage" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>

            </section>
        </div>


    </div>
</template>

<style scoped>

</style>
