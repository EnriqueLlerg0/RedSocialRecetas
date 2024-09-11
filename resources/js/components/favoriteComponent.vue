<script>
export default {
    name:"favoriteComponent",
    data() {
        return{
            isVisible:false,
            pagination: {
                current_page: 1,
                last_page: null,
                total: null,
                per_page: null
            },
            filters: {
                text: ''
            },
            meGustaData:{
                idRecipe:'',
                idUser:'',
                estado:''
            },
            recipes: [],
            translations: {},
            currentLang: 'es',
            isArrowVisible1:false,
            isArrowVisible2:false,
            rolUser:''
        }
    },
    methods:{
        actionLink(route){
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
        prevPage() {
            this.goToPage(this.pagination.current_page - 1);
        },
        nextPage() {
            this.goToPage(this.pagination.current_page + 1);
        },
        setPages() {
            const pageCount = 5;
            const currentPage = this.pagination.current_page;
            const totalPages = this.pagination.last_page;

            let startPage = currentPage - Math.floor(pageCount / 2);


            startPage = Math.max(1, startPage);

            let endPage = startPage + pageCount - 1;

            endPage = Math.min(totalPages, endPage);


            if (endPage - startPage + 1 < pageCount) {
                startPage = Math.max(1, endPage - pageCount + 1);
            }

            this.visiblePages = [];
            for (let i = startPage; i <= endPage; i++) {
                this.visiblePages.push(i);
            }
        },
        goToPage(page) {
            if (page < 1 || page > this.pagination.last_page) {
                return;
            }
            this.getAllRecipe(page);
        },
        async getAllRecipe(page = 1) {
            this.isVisible=true;
            await axios.get("/favoriteRecipes", {
                params: {
                    page: page,
                    text: this.filters.text,
                    idUser: JSON.parse(localStorage.getItem('user'))['id']
                }
            }).then((response) => {
                this.isVisible=false;
                this.recipes = response.data.recipe.data;

                console.log(this.recipes);

                this.pagination = {
                    current_page: response.data.recipe.current_page,
                    last_page: response.data.recipe.last_page,
                    total: response.data.recipe.total,
                    per_page: response.data.recipe.per_page
                }
                this.setPages();
            }).catch(error => {
                if (error.response.status === 422) {
                    console.log(error.response.data.errors);
                    Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                } else {
                    console.error(error);
                }
            });
        },
        async meGusta(id, estado) {
            this.meGustaData.idUser=JSON.parse(localStorage.getItem('user'))['id'];
            this.meGustaData.idRecipe=id;
            this.meGustaData.estado=estado;

            if(this.meGustaData.estado)
            {
                this.isVisible=true;
                await axios.delete("/putLike", {
                    params: {
                        idUser: this.meGustaData.idUser,
                        idRecipe: this.meGustaData.idRecipe,
                        estado: this.meGustaData.estado
                    }
                }).then((response) => {
                    this.isVisible=false;
                    this.getAllRecipe();
                }).catch(error => {
                    if (error.response.status === 422) {
                        console.log(error.response.data.errors);
                        Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                    } else {
                        console.error(error);
                    }
                });
            }
            else
            {
                this.isVisible=true;
                await axios.post("/putLike", {idUser:this.meGustaData.idUser,idRecipe:this.meGustaData.idRecipe, estado:this.meGustaData.estado}).then((response) => {
                    this.isVisible=false;
                    this.getAllRecipe();
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
        cerrarSesion()
        {
            localStorage.removeItem("user");
        }
    },
    created() {
        this.rolUser = JSON.parse(localStorage.getItem('user'))['roll'];
        this.getTranslations();
        this.loadTranslations(this.currentLang);

        this.getAllRecipe();
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

                <button class="text-decoration-none border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/favorite')">
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

                <button class="text-decoration-none border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/favorite')">
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


        <div class="mobile-item w-100">
            <section class="col-12 d-flex flex-column align-items-center fondo1">

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

                <div class="w-100 fijos d-flex flex-column align-items-center border-bottom pb-2">
                    <form action="" class="w-75 my-3">
                        <div class="d-flex flex-row">
                            <input type="search" id="form1" class="form-control" v-model="this.filters.text" :placeholder="translate('FavoritoInput')"/>
                            <button type="button" class="btn btn-primary" data-mdb-ripple-init v-on:click="getAllRecipe()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="w-100 ">
                    <div class="d-flex justify-content-center" v-for="(reci, index) in recipes" :key="index">
                        <div class="card w-75 mt-mayor">
                            <img :src="reci.img" class="card-img-top" alt="...">
                            <div class="card-body">
                                <a href="" class="card-title text-decoration-none h5">{{ reci.name_recipe }}</a>
                                <div class="d-flex flex-wrap gap-5 justify-content-center mt-3">
                                    <button class="border-0 bg-transparent" v-on:click="meGusta(reci.id, reci.favoriteState)">
                                        <svg v-if="reci.favoriteState==false" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 122222.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" fill="red"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pagination mt-mayor" data-align-text="center">
                    <button v-on:click="goToPage(1)" data-size="16"><i class="fa-solid fa-chevrons-left"></i></button>
                    <button v-on:click="prevPage" data-size="16"><i class="fa-solid fa-chevron-left"></i></button>
                    <button v-for="num in visiblePages" :key="num" v-on:click="goToPage(num)" data-size="16"
                    >
                        <span v-if="num==this.pagination.current_page" class="actived">{{ num }}</span>
                        <span v-else>{{ num }}</span>
                    </button>
                    <button v-on:click="nextPage" data-size="16"
                    ><i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <button v-on:click="goToPage(pagination.last_page)" data-size="16"
                    ><i class="fa-solid fa-chevrons-right"></i>
                    </button>
                </div>
                <p>{{ translate('ParrafoPagina') }} {{ pagination.current_page }} {{ translate('DeEntreNumeroPagina') }} {{ pagination.last_page }}</p>

            </section>
        </div>

        <div class="desktop-item w-100">
            <div class=" d-flex">
                <section class="col-3">

                </section>

                <section class="col-9 d-flex flex-column align-items-center fondo1">

                    <div class="w-100 fijos d-flex flex-column align-items-center border-bottom pb-2">
                        <form action="" class="w-50 my-3">
                            <div class="d-flex flex-row">
                                <input type="search" id="form1" class="form-control" v-model="this.filters.text" :placeholder="translate('FavoritoInput')"/>
                                <button type="button" class="btn btn-primary" data-mdb-ripple-init v-on:click="getAllRecipe()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="w-100 dividir3">
                        <div class="d-flex justify-content-center" v-for="(reci, index) in recipes" :key="index">
                            <div class="card w-75 mt-mayor">
                                <img :src="reci.img" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <a href="" class="card-title text-decoration-none h5">{{ reci.name_recipe }}</a>
                                    <div class="d-flex flex-wrap gap-5 justify-content-center mt-3">
                                        <button class="border-0 bg-transparent" v-on:click="meGusta(reci.id, reci.favoriteState)">
                                            <svg v-if="reci.favoriteState==false" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 122222.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" fill="red"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pagination mt-mayor" data-align-text="center">
                        <button v-on:click="goToPage(1)" data-size="16"><i class="fa-solid fa-chevrons-left"></i></button>
                        <button v-on:click="prevPage" data-size="16"><i class="fa-solid fa-chevron-left"></i></button>
                        <button v-for="num in visiblePages" :key="num" v-on:click="goToPage(num)" data-size="16"
                        >
                            <span v-if="num==this.pagination.current_page" class="actived">{{ num }}</span>
                            <span v-else>{{ num }}</span>
                        </button>
                        <button v-on:click="nextPage" data-size="16"
                        ><i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button v-on:click="goToPage(pagination.last_page)" data-size="16"
                        ><i class="fa-solid fa-chevrons-right"></i>
                        </button>
                    </div>
                    <p>{{ translate('ParrafoPagina') }} {{ pagination.current_page }} {{ translate('DeEntreNumeroPagina') }} {{ pagination.last_page }}</p>

                </section>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
