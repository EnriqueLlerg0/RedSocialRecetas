<script>
export default {
    name: "upComponent",
    data() {
        return {
            recipeId:'',
            isOpen1:true,
            isopen2:false,
            isopen2Mobile:false,
            isOpen3:false,
            cont: 0,
            imagen: "",
            titulo: "",
            nombreIngredientes: [],
            cantidadIngredientes: [],
            filtroIngredientes: [],
            descripcion: "",
            pais: "",
            Username:"",
            Email:"",
            allRecipe:[],
            allDataUser:[],
            isVisible:false,
            recipeData:[],
            idRecipe:'',
            nameRecipe:'',
            idUser:'',
            imagenRecetaActualizada:"",
            selectedFile: null,
            imageUrl: null,
            translations: {},
            currentLang: 'es',
            isArrowVisible1:false,
            isArrowVisible2:false,
            rolUser:'',
            dataAllUser:[]
        }
    },
    methods: {
        actionLink(route) {
            this.$router.push(route);
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
                })
                .catch(error => {
                    console.error('Error loading translations:', error);
                });
        },
        async eliminarReceta() {
            this.isVisible = true;
            await axios.delete("/deleteRecipe", {
                params: {
                    idRecipe: this.idRecipe
                }
            }).then((response) => {
                this.isVisible = false;
                this.obtenerRecetasUsuario();
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        async pedirDatosReceta() {
            this.isVisible = true;
            await axios.get("/getRecipeData", {
                params: {
                    nameRecipe: this.nameRecipe
                }
            }).then((response) => {
                this.isVisible = false;
                this.recipeData = response.data.recipe;
                this.imagenRecetaActualizada = this.recipeData.img;
                this.titulo = this.recipeData.name;
                this.descripcion = this.recipeData.descripcion;
                this.pais = this.recipeData.nacionalidad;
                this.nombreIngredientes = this.recipeData.ingrediente;
                this.cantidadIngredientes = this.recipeData.cantidad;
                this.filtroIngredientes = this.recipeData.cate;

                this.isOpen1 = false;
                this.isopen2 = 'visible';
                this.isopen2Mobile=true;
                this.rellenarTabla();
            }).catch(error => {
                console.error(error);

            });
        },
        async obtenerRecetasUsuario(){
            this.isVisible=true
            await axios.get("/allRecipesUser", {
                params: {
                    idUser: this.idUser
                }
            }).then((response) => {
                this.isVisible=false;

                this.isOpen1=false;
                this.isopen2=true;

                this.allRecipe = response.data.recipe;
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        cerrarSesion() {
            localStorage.removeItem("user");
        },
        async obtenerTodosLosUsuarios() {
            this.isVisible=true;
            await axios.get("/allUser").then((response) => {
                this.dataAllUser=response.data.users;
                this.isVisible=false;
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
    mounted() {
        this.rolUser = JSON.parse(localStorage.getItem('user'))['roll'];
        if(this.rolUser!="admin")
        {
            this.actionLink("/home")
        }

        this.getTranslations();
        this.loadTranslations(this.currentLang);

        this.idUser=JSON.parse(localStorage.getItem('user'))['id'];
        this.obtenerTodosLosUsuarios();
    }
}
</script>

<template>
    <div class="d-flex flex-wrap">
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

                <button class="text-decoration-none text-center border-0 menuPrincipal" v-on:click="actionLink('/profile')">
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

                <button v-if="this.rolUser=='admin'" class="text-decoration-none  text-center border-0 menuPrincipalSeleccionado"
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

                <button class="text-decoration-none  text-center border-0 menuPrincipal " v-on:click="actionLink('/up')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-newspaper" viewBox="0 0 16 16">
                        <path
                            d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"/>
                        <path
                            d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"/>
                    </svg>
                    {{ translate('RecetasMenu') }}
                </button>

                <button class="text-decoration-none text-center border-0 menuPrincipal" v-on:click="actionLink('/profile')">
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

                <button v-if="this.rolUser=='admin'" class="text-decoration-none  text-center border-0 menuPrincipalSeleccionado"
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


        <div class="mobile-item w-100 h-100 abs-pos d-flex align-items-center justify-content-center">
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

            <section class="w-100 h-450-px mt-40 d-flex justify-content-center align-items-center" v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center w-75 h-100 border border-black border-1 rounded-4">
                    <div class="w-100 h-100 mt-30 d-flex flex-column align-items-center gap-4">
                        <h3>{{ translate('UsuariosAdmin') }}</h3>
                        <div class="w-75 h-75 d-flex flex-column gap-3 overflow-y-scroll">
                            <div class="d-flex mr-20" data-justify-content="space-between" v-for="(user, index) in dataAllUser" :key="index">
                                <span class="w-50 custom-cursor">{{ user.user }}</span>
                                <div class="d-flex align-items-center gap-3">
                                    <svg v-on:click="this.idUser=user.id , obtenerRecetasUsuario()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-100 h-450-px mt-40 d-flex justify-content-center align-items-center" v-bind:class="{ 'd-none': !isopen2}" v-if="isopen2">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center w-75 h-100 border border-black border-1 rounded-4">
                    <div class="w-100 h-100 mt-30 d-flex flex-column gap-4">
                        <div class="d-flex align-items-center ml-20">
                            <i class="fa-solid fa-arrow-left" data-size="20" v-on:click="isOpen1=true, isopen2=false"></i>
                            <div class="w-100 d-flex justify-content-center">
                                <h3>{{ translate('RecetasMenu') }}</h3>
                            </div>
                        </div>
                        <div class="w-100 h-75 d-flex flex-column align-items-center gap-3 overflow-y-scroll">
                            <div class="w-75 d-flex mr-20" data-justify-content="space-between" v-for="(recipe, index) in allRecipe" :key="index">
                                <span class="w-50 custom-cursor ml-30" v-on:click="actionLink(`/recipe?nameRecipe=${recipe.name_recipe}`)">{{ recipe.name_recipe }}</span>
                                <div class="d-flex align-items-center gap-3">
                                    <svg v-on:click="this.idRecipe=recipe.id, eliminarReceta()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3 custom-cursor" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="desktop-item">
            <section v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1" class="col-12 d-flex flex-column justify-content-center fondo1 h-100 position-absolute">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center col-7 seccionSubirReceta border border-black border-1 rounded-4">
                    <div class="w-75 izquierda ml-1 mt-5 h-65 d-flex flex-column align-items-center gap-4">
                        <h3>{{ translate('UsuariosAdmin') }}</h3>
                        <div class="col-6 h-75 d-flex flex-column gap-3 overflow-y-scroll">
                            <div class="d-flex gap-3" v-for="(user, index) in dataAllUser" :key="index">
                                <span class="w-50">{{ user.user }}</span>
                                <div class="d-flex gap-3">
                                    <svg v-on:click="this.idUser=user.id , obtenerRecetasUsuario()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill pointer" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section v-bind:class="{ 'd-none': !isopen2}" v-if="isopen2" class="col-12 d-flex flex-column justify-content-center fondo1 h-100 position-absolute">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center col-7 seccionSubirReceta border border-black border-1 rounded-4">
                    <div class="w-75 izquierda ml-1 mt-5 h-65 d-flex flex-column gap-4">
                        <div class="d-flex ml-20">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-arrow-left pointer" data-size="20" v-on:click="isOpen1=true, isopen2=false"></i>
                            </div>
                            <div class="w-100 d-flex justify-content-center">
                                <h3>{{ translate('RecetasMenu') }}</h3>
                            </div>
                        </div>
                        <div class="w-100 d-flex align-items-center justify-content-center">
                            <div class="col-6 h-100 d-flex flex-column align-items-center gap-3 overflow-y-scroll">
                                <div class="w-100 d-flex gap-3" data-justify-content="space-between" v-for="(recipe, index) in allRecipe" :key="index">
                                    <span class="w-50 custom-cursor" v-on:click="actionLink(`/recipe?nameRecipe=${recipe.name_recipe}`)">{{ recipe.name_recipe }}</span>
                                    <div class="d-flex gap-3 mr-20">
                                        <svg v-on:click="this.idRecipe=recipe.id, eliminarReceta()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3 custom-cursor" viewBox="0 0 16 16">
                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                        </svg>
                                    </div>
                                </div>
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
