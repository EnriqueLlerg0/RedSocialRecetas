<script>
export default {
    name: "principalComponent",
    data() {
        return {
            pagination: {
                current_page: 1,
                last_page: null,
                total: null,
                per_page: null
            },
            filters: {
                text: '',
                country: '',
                type: '',
            },
            meGustaData: {
                idRecipe: '',
                idUser: '',
                estado: ''
            },
            recipes: [],
            isVisible: false,
            translations: {},
            currentLang: 'es',
            isArrowVisible1:false,
            isArrowVisible2:false,
            rolUser:''
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
        prevPage() {
            this.goToPage(this.pagination.current_page - 1);
        },
        nextPage() {
            this.goToPage(this.pagination.current_page + 1);
        },
        comprobarPais() {
            const countries = [
                "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia",
                "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin",
                "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi",
                "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia",
                "Comoros", "Congo, Democratic Republic of the", "Congo, Republic of the", "Costa Rica", "Croatia", "Cuba", "Cyprus",
                "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor-Leste)", "Ecuador",
                "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France",
                "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
                "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy",
                "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kosovo", "Kuwait",
                "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
                "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius",
                "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (Burma)",
                "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia",
                "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland",
                "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines",
                "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone",
                "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka",
                "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo",
                "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates",
                "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam",
                "Yemen", "Zambia", "Zimbabwe"
            ];

            if (countries.indexOf(this.filters.country) != -1) {
                this.getAllRecipe();
            } else {
                Swal.fire("ERROR, el pais seleccionado no existe", "", "error");
            }
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
            this.isVisible = true;
            await axios.get("/allRecipes", {
                params: {
                    page: page,
                    text: this.filters.text,
                    country: this.filters.country,
                    type: this.filters.type,
                    idUser: JSON.parse(localStorage.getItem('user'))['id']
                }
            }).then((response) => {
                this.isVisible = false;
                this.recipes = response.data.recipe.data;
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
            this.meGustaData.idUser = JSON.parse(localStorage.getItem('user'))['id'];
            this.meGustaData.idRecipe = id;
            this.meGustaData.estado = estado;

            if (this.meGustaData.estado) {
                this.isVisible = true;
                await axios.delete("/putLike", {
                    params: {
                        idUser: this.meGustaData.idUser,
                        idRecipe: this.meGustaData.idRecipe,
                        estado: this.meGustaData.estado
                    }
                }).then((response) => {
                    this.isVisible = false;
                    this.getAllRecipe();
                }).catch(error => {
                    if (error.response.status === 422) {
                        console.log(error.response.data.errors);
                        Swal.fire("ERROR, el usuario o el correo ya se encuentra en uso", "", "error");
                    } else {
                        console.error(error);
                    }
                });
            } else {
                this.isVisible = true;
                await axios.post("/putLike", {
                    idUser: this.meGustaData.idUser,
                    idRecipe: this.meGustaData.idRecipe,
                    estado: this.meGustaData.estado
                }).then((response) => {
                    this.isVisible = false;
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
        cerrarSesion() {
            localStorage.removeItem("user");
        }
    },
    created() {
        this.rolUser = JSON.parse(localStorage.getItem('user'))['roll'];
        console.log(this.rolUser);
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

                <button class="text-decoration-none border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/home')">
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

                <button class="text-decoration-none border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/home')">
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


        <div class="mobile-item"  v-bind:class="{ 'd-none': !isArrowVisible2}">
            <section class="col-12 border d-flex flex-column seccionFinMobil fondo1">
                <i class="fa-regular fa-xmark p-15 abs-pos l-10-px pointer" data-size="30" v-on:click="this.isArrowVisible2=false"></i>
                <div class="p-3 d-flex flex-column h-100 justify-content-center">

                    <div class="border rounded-3">
                        <div class="mb-3 mx-3 p-3">
                            <label for="exampleFormControlTextarea1" class="form-label">{{
                                    translate('InicioLabelPais')
                                }}</label>

                            <div class="d-flex">
                                <input class="form-control border-black" list="datalistOptions" v-model="filters.country"
                                       id="exampleDataList" :placeholder="translate('InicioInputPais')">
                                <button type="button" class="btn btn-primary" data-mdb-ripple-init
                                        v-on:click="comprobarPais()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </button>

                                <datalist id="datalistOptions">
                                    <option value="Afghanistan"></option>
                                    <option value="Albania"></option>
                                    <option value="Algeria"></option>
                                    <option value="Andorra"></option>
                                    <option value="Angola"></option>
                                    <option value="Antigua and Barbuda"></option>
                                    <option value="Argentina"></option>
                                    <option value="Armenia"></option>
                                    <option value="Australia"></option>
                                    <option value="Austria"></option>
                                    <option value="Azerbaijan"></option>
                                    <option value="Bahamas"></option>
                                    <option value="Bahrain"></option>
                                    <option value="Bangladesh"></option>
                                    <option value="Barbados"></option>
                                    <option value="Belarus"></option>
                                    <option value="Belgium"></option>
                                    <option value="Belize"></option>
                                    <option value="Benin"></option>
                                    <option value="Bhutan"></option>
                                    <option value="Bolivia"></option>
                                    <option value="Bosnia and Herzegovina"></option>
                                    <option value="Botswana"></option>
                                    <option value="Brazil"></option>
                                    <option value="Brunei"></option>
                                    <option value="Bulgaria"></option>
                                    <option value="Burkina Faso"></option>
                                    <option value="Burundi"></option>
                                    <option value="Cabo Verde"></option>
                                    <option value="Cambodia"></option>
                                    <option value="Cameroon"></option>
                                    <option value="Canada"></option>
                                    <option value="Central African Republic"></option>
                                    <option value="Chad"></option>
                                    <option value="Chile"></option>
                                    <option value="China"></option>
                                    <option value="Colombia"></option>
                                    <option value="Comoros"></option>
                                    <option value="Congo"></option>
                                    <option value="Costa Rica"></option>
                                    <option value="Croatia"></option>
                                    <option value="Cuba"></option>
                                    <option value="Cyprus"></option>
                                    <option value="Czechia"></option>
                                    <option value="Denmark"></option>
                                    <option value="Djibouti"></option>
                                    <option value="Dominica"></option>
                                    <option value="Dominican Republic"></option>
                                    <option value="Ecuador"></option>
                                    <option value="Egypt"></option>
                                    <option value="El Salvador"></option>
                                    <option value="Equatorial Guinea"></option>
                                    <option value="Eritrea"></option>
                                    <option value="Estonia"></option>
                                    <option value="Eswatini"></option>
                                    <option value="Ethiopia"></option>
                                    <option value="Fiji"></option>
                                    <option value="Finland"></option>
                                    <option value="France"></option>
                                    <option value="Gabon"></option>
                                    <option value="Gambia"></option>
                                    <option value="Georgia"></option>
                                    <option value="Germany"></option>
                                    <option value="Ghana"></option>
                                    <option value="Greece"></option>
                                    <option value="Grenada"></option>
                                    <option value="Guatemala"></option>
                                    <option value="Guinea"></option>
                                    <option value="Guinea-Bissau"></option>
                                    <option value="Guyana"></option>
                                    <option value="Haiti"></option>
                                    <option value="Honduras"></option>
                                    <option value="Hungary"></option>
                                    <option value="Iceland"></option>
                                    <option value="India"></option>
                                    <option value="Indonesia"></option>
                                    <option value="Iran"></option>
                                    <option value="Iraq"></option>
                                    <option value="Ireland"></option>
                                    <option value="Israel"></option>
                                    <option value="Italy"></option>
                                    <option value="Jamaica"></option>
                                    <option value="Japan"></option>
                                    <option value="Jordan"></option>
                                    <option value="Kazakhstan"></option>
                                    <option value="Kenya"></option>
                                    <option value="Kiribati"></option>
                                    <option value="Kosovo"></option>
                                    <option value="Kuwait"></option>
                                    <option value="Kyrgyzstan"></option>
                                    <option value="Laos"></option>
                                    <option value="Latvia"></option>
                                    <option value="Lebanon"></option>
                                    <option value="Lesotho"></option>
                                    <option value="Liberia"></option>
                                    <option value="Libya"></option>
                                    <option value="Liechtenstein"></option>
                                    <option value="Lithuania"></option>
                                    <option value="Luxembourg"></option>
                                    <option value="Madagascar"></option>
                                    <option value="Malawi"></option>
                                    <option value="Malaysia"></option>
                                    <option value="Maldives"></option>
                                    <option value="Mali"></option>
                                    <option value="Malta"></option>
                                    <option value="Marshall Islands"></option>
                                    <option value="Mauritania"></option>
                                    <option value="Mauritius"></option>
                                    <option value="Mexico"></option>
                                    <option value="Micronesia"></option>
                                    <option value="Moldova"></option>
                                    <option value="Monaco"></option>
                                    <option value="Mongolia"></option>
                                    <option value="Montenegro"></option>
                                    <option value="Morocco"></option>
                                    <option value="Mozambique"></option>
                                    <option value="Myanmar"></option>
                                    <option value="Namibia"></option>
                                    <option value="Nauru"></option>
                                    <option value="Nepal"></option>
                                    <option value="Netherlands"></option>
                                    <option value="New Zealand"></option>
                                    <option value="Nicaragua"></option>
                                    <option value="Niger"></option>
                                    <option value="Nigeria"></option>
                                    <option value="North Korea"></option>
                                    <option value="North Macedonia"></option>
                                    <option value="Norway"></option>
                                    <option value="Oman"></option>
                                    <option value="Pakistan"></option>
                                    <option value="Palau"></option>
                                    <option value="Palestine"></option>
                                    <option value="Panama"></option>
                                    <option value="Papua New Guinea"></option>
                                    <option value="Paraguay"></option>
                                    <option value="Peru"></option>
                                    <option value="Philippines"></option>
                                    <option value="Poland"></option>
                                    <option value="Portugal"></option>
                                    <option value="Qatar"></option>
                                    <option value="Romania"></option>
                                    <option value="Russia"></option>
                                    <option value="Rwanda"></option>
                                    <option value="Saint Kitts and Nevis"></option>
                                    <option value="Saint Lucia"></option>
                                    <option value="Saint Vincent and the Grenadines"></option>
                                    <option value="Samoa"></option>
                                    <option value="San Marino"></option>
                                    <option value="Sao Tome and Principe"></option>
                                    <option value="Saudi Arabia"></option>
                                    <option value="Senegal"></option>
                                    <option value="Serbia"></option>
                                    <option value="Seychelles"></option>
                                    <option value="Sierra Leone"></option>
                                    <option value="Singapore"></option>
                                    <option value="Slovakia"></option>
                                    <option value="Slovenia"></option>
                                    <option value="Solomon Islands"></option>
                                    <option value="Somalia"></option>
                                    <option value="South Africa"></option>
                                    <option value="South Korea"></option>
                                    <option value="South Sudan"></option>
                                    <option value="Spain"></option>
                                    <option value="Sri Lanka"></option>
                                    <option value="Sudan"></option>
                                    <option value="Suriname"></option>
                                    <option value="Sweden"></option>
                                    <option value="Switzerland"></option>
                                    <option value="Syria"></option>
                                    <option value="Taiwan"></option>
                                    <option value="Tajikistan"></option>
                                    <option value="Tanzania"></option>
                                    <option value="Thailand"></option>
                                    <option value="Timor-Leste"></option>
                                    <option value="Togo"></option>
                                    <option value="Tonga"></option>
                                    <option value="Trinidad and Tobago"></option>
                                    <option value="Tunisia"></option>
                                    <option value="Turkey"></option>
                                    <option value="Turkmenistan"></option>
                                    <option value="Tuvalu"></option>
                                    <option value="Uganda"></option>
                                    <option value="Ukraine"></option>
                                    <option value="United Arab Emirates"></option>
                                    <option value="United Kingdom"></option>
                                    <option value="United States"></option>
                                    <option value="Uruguay"></option>
                                    <option value="Uzbekistan"></option>
                                    <option value="Vanuatu"></option>
                                    <option value="Vatican City"></option>
                                    <option value="Venezuela"></option>
                                    <option value="Vietnam"></option>
                                    <option value="Yemen"></option>
                                    <option value="Zambia"></option>
                                    <option value="Zimbabwe"></option>
                                </datalist>
                            </div>

                        </div>
                    </div>

                    <div class="border rounded-3 h-50 overflow-y-scroll mt-5">
                        <div class="mt-3 izquierda custom-cursor" v-on:click="filters.type='', getAllRecipe()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-trash3" viewBox="0 0 16 16">
                                <path
                                    d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                            </svg>
                        </div>
                        <div class="accordion p-3" id="accordionExample">

                            <div class="accordion-item mt-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        {{ translate('AcordeonTitulo1') }}
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='1', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido1')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='2', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido2')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='3', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido3')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='4', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido4')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                        {{ translate('AcordeonTitulo2') }}
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='5',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido5')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='6',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido6')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='7',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido7')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='8',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido8')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseFour" aria-expanded="false"
                                            aria-controls="collapseFour">
                                        {{ translate('AcordeonTitulo3') }}
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='9',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido9')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='10',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido10')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='11',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido11')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='12',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido12')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseFive" aria-expanded="false"
                                            aria-controls="collapseFive">
                                        {{ translate('AcordeonTitulo4') }}
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='13',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido13')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='14',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido14')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='15',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido15')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='16',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido16')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                        {{ translate('AcordeonTitulo5') }}
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='17',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido17')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='18',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido18')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='19',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido19')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='20',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido20')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSeven" aria-expanded="false"
                                            aria-controls="collapseSeven">
                                        {{ translate('AcordeonTitulo6') }}
                                    </button>
                                </h2>
                                <div id="collapseSeven" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='21',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido21')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='22',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido22')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='23',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido23')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='24',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido24')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseEight" aria-expanded="false"
                                            aria-controls="collapseEight">
                                        {{ translate('AcordeonTitulo7') }}
                                    </button>
                                </h2>
                                <div id="collapseEight" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='25',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido25')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='26',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido26')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='27',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido27')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='28',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido28')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='29',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido29')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseNine" aria-expanded="false"
                                            aria-controls="collapseNine">
                                        {{ translate('AcordeonTitulo8') }}
                                    </button>
                                </h2>
                                <div id="collapseNine" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='30',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido30')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='31',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido31')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='32',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido32')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='33',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido33')
                                                }} de pasta</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                        {{ translate('AcordeonTitulo9') }}
                                    </button>
                                </h2>
                                <div id="collapseTen" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='34',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido34')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='35',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido35')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='36',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido36')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='37',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido37')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseEleven" aria-expanded="false"
                                            aria-controls="collapseEleven">
                                        {{ translate('AcordeonTitulo10') }}
                                    </button>
                                </h2>
                                <div id="collapseEleven" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='38',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido38')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='39',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido39')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='40',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido40')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='41',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido41')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwelve" aria-expanded="false"
                                            aria-controls="collapseTwelve">
                                        {{ translate('AcordeonTitulo11') }}
                                    </button>
                                </h2>
                                <div id="collapseTwelve" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='42',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido42')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='43',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido43')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='44',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido44')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='45',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido45')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="desktop-item">
            <section class="col-3 border d-flex flex-column seccionFin fondo1">
                <div class="p-3 d-flex flex-column h-100 justify-content-center">

                    <div class="border rounded-3">
                        <div class="mb-3 mx-3 p-3">
                            <label for="exampleFormControlTextarea1" class="form-label">{{
                                    translate('InicioLabelPais')
                                }}</label>

                            <div class="d-flex">
                                <input class="form-control border-black" list="datalistOptions" v-model="filters.country"
                                       id="exampleDataList" :placeholder="translate('InicioInputPais')">
                                <button type="button" class="btn btn-primary" data-mdb-ripple-init
                                        v-on:click="comprobarPais()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </button>

                                <datalist id="datalistOptions">
                                    <option value="Afghanistan"></option>
                                    <option value="Albania"></option>
                                    <option value="Algeria"></option>
                                    <option value="Andorra"></option>
                                    <option value="Angola"></option>
                                    <option value="Antigua and Barbuda"></option>
                                    <option value="Argentina"></option>
                                    <option value="Armenia"></option>
                                    <option value="Australia"></option>
                                    <option value="Austria"></option>
                                    <option value="Azerbaijan"></option>
                                    <option value="Bahamas"></option>
                                    <option value="Bahrain"></option>
                                    <option value="Bangladesh"></option>
                                    <option value="Barbados"></option>
                                    <option value="Belarus"></option>
                                    <option value="Belgium"></option>
                                    <option value="Belize"></option>
                                    <option value="Benin"></option>
                                    <option value="Bhutan"></option>
                                    <option value="Bolivia"></option>
                                    <option value="Bosnia and Herzegovina"></option>
                                    <option value="Botswana"></option>
                                    <option value="Brazil"></option>
                                    <option value="Brunei"></option>
                                    <option value="Bulgaria"></option>
                                    <option value="Burkina Faso"></option>
                                    <option value="Burundi"></option>
                                    <option value="Cabo Verde"></option>
                                    <option value="Cambodia"></option>
                                    <option value="Cameroon"></option>
                                    <option value="Canada"></option>
                                    <option value="Central African Republic"></option>
                                    <option value="Chad"></option>
                                    <option value="Chile"></option>
                                    <option value="China"></option>
                                    <option value="Colombia"></option>
                                    <option value="Comoros"></option>
                                    <option value="Congo"></option>
                                    <option value="Costa Rica"></option>
                                    <option value="Croatia"></option>
                                    <option value="Cuba"></option>
                                    <option value="Cyprus"></option>
                                    <option value="Czechia"></option>
                                    <option value="Denmark"></option>
                                    <option value="Djibouti"></option>
                                    <option value="Dominica"></option>
                                    <option value="Dominican Republic"></option>
                                    <option value="Ecuador"></option>
                                    <option value="Egypt"></option>
                                    <option value="El Salvador"></option>
                                    <option value="Equatorial Guinea"></option>
                                    <option value="Eritrea"></option>
                                    <option value="Estonia"></option>
                                    <option value="Eswatini"></option>
                                    <option value="Ethiopia"></option>
                                    <option value="Fiji"></option>
                                    <option value="Finland"></option>
                                    <option value="France"></option>
                                    <option value="Gabon"></option>
                                    <option value="Gambia"></option>
                                    <option value="Georgia"></option>
                                    <option value="Germany"></option>
                                    <option value="Ghana"></option>
                                    <option value="Greece"></option>
                                    <option value="Grenada"></option>
                                    <option value="Guatemala"></option>
                                    <option value="Guinea"></option>
                                    <option value="Guinea-Bissau"></option>
                                    <option value="Guyana"></option>
                                    <option value="Haiti"></option>
                                    <option value="Honduras"></option>
                                    <option value="Hungary"></option>
                                    <option value="Iceland"></option>
                                    <option value="India"></option>
                                    <option value="Indonesia"></option>
                                    <option value="Iran"></option>
                                    <option value="Iraq"></option>
                                    <option value="Ireland"></option>
                                    <option value="Israel"></option>
                                    <option value="Italy"></option>
                                    <option value="Jamaica"></option>
                                    <option value="Japan"></option>
                                    <option value="Jordan"></option>
                                    <option value="Kazakhstan"></option>
                                    <option value="Kenya"></option>
                                    <option value="Kiribati"></option>
                                    <option value="Kosovo"></option>
                                    <option value="Kuwait"></option>
                                    <option value="Kyrgyzstan"></option>
                                    <option value="Laos"></option>
                                    <option value="Latvia"></option>
                                    <option value="Lebanon"></option>
                                    <option value="Lesotho"></option>
                                    <option value="Liberia"></option>
                                    <option value="Libya"></option>
                                    <option value="Liechtenstein"></option>
                                    <option value="Lithuania"></option>
                                    <option value="Luxembourg"></option>
                                    <option value="Madagascar"></option>
                                    <option value="Malawi"></option>
                                    <option value="Malaysia"></option>
                                    <option value="Maldives"></option>
                                    <option value="Mali"></option>
                                    <option value="Malta"></option>
                                    <option value="Marshall Islands"></option>
                                    <option value="Mauritania"></option>
                                    <option value="Mauritius"></option>
                                    <option value="Mexico"></option>
                                    <option value="Micronesia"></option>
                                    <option value="Moldova"></option>
                                    <option value="Monaco"></option>
                                    <option value="Mongolia"></option>
                                    <option value="Montenegro"></option>
                                    <option value="Morocco"></option>
                                    <option value="Mozambique"></option>
                                    <option value="Myanmar"></option>
                                    <option value="Namibia"></option>
                                    <option value="Nauru"></option>
                                    <option value="Nepal"></option>
                                    <option value="Netherlands"></option>
                                    <option value="New Zealand"></option>
                                    <option value="Nicaragua"></option>
                                    <option value="Niger"></option>
                                    <option value="Nigeria"></option>
                                    <option value="North Korea"></option>
                                    <option value="North Macedonia"></option>
                                    <option value="Norway"></option>
                                    <option value="Oman"></option>
                                    <option value="Pakistan"></option>
                                    <option value="Palau"></option>
                                    <option value="Palestine"></option>
                                    <option value="Panama"></option>
                                    <option value="Papua New Guinea"></option>
                                    <option value="Paraguay"></option>
                                    <option value="Peru"></option>
                                    <option value="Philippines"></option>
                                    <option value="Poland"></option>
                                    <option value="Portugal"></option>
                                    <option value="Qatar"></option>
                                    <option value="Romania"></option>
                                    <option value="Russia"></option>
                                    <option value="Rwanda"></option>
                                    <option value="Saint Kitts and Nevis"></option>
                                    <option value="Saint Lucia"></option>
                                    <option value="Saint Vincent and the Grenadines"></option>
                                    <option value="Samoa"></option>
                                    <option value="San Marino"></option>
                                    <option value="Sao Tome and Principe"></option>
                                    <option value="Saudi Arabia"></option>
                                    <option value="Senegal"></option>
                                    <option value="Serbia"></option>
                                    <option value="Seychelles"></option>
                                    <option value="Sierra Leone"></option>
                                    <option value="Singapore"></option>
                                    <option value="Slovakia"></option>
                                    <option value="Slovenia"></option>
                                    <option value="Solomon Islands"></option>
                                    <option value="Somalia"></option>
                                    <option value="South Africa"></option>
                                    <option value="South Korea"></option>
                                    <option value="South Sudan"></option>
                                    <option value="Spain"></option>
                                    <option value="Sri Lanka"></option>
                                    <option value="Sudan"></option>
                                    <option value="Suriname"></option>
                                    <option value="Sweden"></option>
                                    <option value="Switzerland"></option>
                                    <option value="Syria"></option>
                                    <option value="Taiwan"></option>
                                    <option value="Tajikistan"></option>
                                    <option value="Tanzania"></option>
                                    <option value="Thailand"></option>
                                    <option value="Timor-Leste"></option>
                                    <option value="Togo"></option>
                                    <option value="Tonga"></option>
                                    <option value="Trinidad and Tobago"></option>
                                    <option value="Tunisia"></option>
                                    <option value="Turkey"></option>
                                    <option value="Turkmenistan"></option>
                                    <option value="Tuvalu"></option>
                                    <option value="Uganda"></option>
                                    <option value="Ukraine"></option>
                                    <option value="United Arab Emirates"></option>
                                    <option value="United Kingdom"></option>
                                    <option value="United States"></option>
                                    <option value="Uruguay"></option>
                                    <option value="Uzbekistan"></option>
                                    <option value="Vanuatu"></option>
                                    <option value="Vatican City"></option>
                                    <option value="Venezuela"></option>
                                    <option value="Vietnam"></option>
                                    <option value="Yemen"></option>
                                    <option value="Zambia"></option>
                                    <option value="Zimbabwe"></option>
                                </datalist>
                            </div>

                        </div>
                    </div>

                    <div class="border rounded-3 h-50 overflow-y-scroll mt-5">
                        <div class="mt-3 izquierda custom-cursor" v-on:click="filters.type='', getAllRecipe()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-trash3" viewBox="0 0 16 16">
                                <path
                                    d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                            </svg>
                        </div>
                        <div class="accordion p-3" id="accordionExample">

                            <div class="accordion-item mt-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        {{ translate('AcordeonTitulo1') }}
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='1', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido1')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='2', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido2')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='3', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido3')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='4', getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido4')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                        {{ translate('AcordeonTitulo2') }}
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='5',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido5')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='6',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido6')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='7',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido7')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='8',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido8')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseFour" aria-expanded="false"
                                            aria-controls="collapseFour">
                                        {{ translate('AcordeonTitulo3') }}
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='9',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido9')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='10',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido10')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='11',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido11')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='12',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido12')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseFive" aria-expanded="false"
                                            aria-controls="collapseFive">
                                        {{ translate('AcordeonTitulo4') }}
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='13',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido13')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='14',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido14')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='15',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido15')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='16',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido16')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                        {{ translate('AcordeonTitulo5') }}
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='17',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido17')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='18',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido18')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='19',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido19')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='20',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido20')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseSeven" aria-expanded="false"
                                            aria-controls="collapseSeven">
                                        {{ translate('AcordeonTitulo6') }}
                                    </button>
                                </h2>
                                <div id="collapseSeven" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='21',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido21')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='22',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido22')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='23',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido23')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='24',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido24')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseEight" aria-expanded="false"
                                            aria-controls="collapseEight">
                                        {{ translate('AcordeonTitulo7') }}
                                    </button>
                                </h2>
                                <div id="collapseEight" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='25',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido25')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='26',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido26')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='27',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido27')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='28',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido28')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='29',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido29')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseNine" aria-expanded="false"
                                            aria-controls="collapseNine">
                                        {{ translate('AcordeonTitulo8') }}
                                    </button>
                                </h2>
                                <div id="collapseNine" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='30',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido30')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='31',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido31')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='32',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido32')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='33',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido33')
                                                }} de pasta</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                        {{ translate('AcordeonTitulo9') }}
                                    </button>
                                </h2>
                                <div id="collapseTen" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='34',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido34')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='35',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido35')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='36',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido36')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='37',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido37')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseEleven" aria-expanded="false"
                                            aria-controls="collapseEleven">
                                        {{ translate('AcordeonTitulo10') }}
                                    </button>
                                </h2>
                                <div id="collapseEleven" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='38',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido38')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='39',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido39')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='40',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido40')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='41',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido41')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseTwelve" aria-expanded="false"
                                            aria-controls="collapseTwelve">
                                        {{ translate('AcordeonTitulo11') }}
                                    </button>
                                </h2>
                                <div id="collapseTwelve" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='42',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido42')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='43',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido43')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='44',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido44')
                                                }}</span>
                                            </li>
                                            <li class="list-group-item custom-cursor"
                                                v-on:click="filters.type='45',getAllRecipe()">
                                            <span class="text-decoration-none negro">{{
                                                    translate('AcordeonContenido45')
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <div class="mobile-item w-100">
            <section class="col-12 d-flex flex-column align-items-center fondo1">

                <div>
                    <svg class="flechasMenuDerecho" width="50" height="90" viewBox="-40 0 90 150" xmlns="http://www.w3.org/2000/svg">
                        <g transform="rotate(360, 45, 75)">
                            <!-- Semicírculo -->
                            <path v-on:click="this.isArrowVisible1=false, this.isArrowVisible2=true" d="M50,100 a47,49 0 1,1 0,-100" fill="#94DAFF" stroke="#0079FF" stroke-width="2"/>
                            <!-- Símbolo < -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" fill="#0079FF" viewBox="-140 187 320 512">
                                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                            </svg>
                        </g>
                    </svg>

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

                <div class="w-100 fijo d-flex flex-column align-items-center border-bottom pb-2">
                    <form action="" class="w-75 my-3">
                        <div class="d-flex flex-row mt-3">
                            <input type="search" id="form1" class="form-control" v-model="filters.text"
                                   :placeholder="translate('InicioInputBuscar')"/>
                            <button type="button" class="btn btn-primary" data-mdb-ripple-init v-on:click="getAllRecipe()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-search" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="w-100 d-flex justify-content-center mt-4" v-for="(reci, index) in recipes" :key="index">
                    <div class="card w-75 mt-mayor">
                        <img :src="reci.img" class="card-img-top" alt="...">
                        <div class="card-body">
                            <div class="d-flex flex-column">
                            <span class="card-title text-decoration-none h5 custom-cursor"
                                  v-on:click="actionLink(`/recipe?nameRecipe=${reci.name_recipe}`)">{{
                                    reci.name_recipe
                                }}</span>
                                <span class="texto-pequeño">{{ translate('AutorReceta') }} {{ reci.nick }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-5 justify-content-center mt-3">
<!--                                <button class="border-0 bg-transparent">-->
<!--                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"-->
<!--                                         class="bi bi-chat" viewBox="0 0 16 16">-->
<!--                                        <path-->
<!--                                            d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105"/>-->
<!--                                    </svg>-->
<!--                                </button>-->
                                <button class="border-0 bg-transparent" v-on:click="meGusta(reci.id, reci.favoriteState)">
                                    {{ reci.total }}
                                    <svg v-if="reci.favoriteState==false" xmlns="http://www.w3.org/2000/svg" width="20"
                                         height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                        <path
                                            d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                        <path
                                            d="m8 2.748-.717-.737C5.6.281 2.514.878 122222.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"
                                            fill="red"/>
                                    </svg>
                                </button>
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
                <p>{{ translate('ParrafoPagina') }} {{ pagination.current_page }} {{ translate('DeEntreNumeroPagina') }}
                    {{ pagination.last_page }}</p>

            </section>
        </div>

        <div class="desktop-item w-100 ">
            <div class="d-flex">
                <section class="col-3">

                </section>

                <section class="col-6 d-flex flex-column align-items-center fondo1">

                    <div class="w-100 fijo d-flex flex-column align-items-center border-bottom pb-2">
                        <form action="" class="w-25 my-3">
                            <div class="d-flex flex-row mt-3">
                                <input type="search" id="form1" class="form-control" v-model="filters.text"
                                       :placeholder="translate('InicioInputBuscar')"/>
                                <button type="button" class="btn btn-primary" data-mdb-ripple-init v-on:click="getAllRecipe()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="w-100 d-flex justify-content-center mt-4" v-for="(reci, index) in recipes" :key="index">
                        <div class="card w-50 mt-mayor">
                            <img :src="reci.img" class="card-img-top" alt="...">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                            <span class="card-title text-decoration-none h5 custom-cursor"
                                  v-on:click="actionLink(`/recipe?nameRecipe=${reci.name_recipe}`)">{{
                                    reci.name_recipe
                                }}</span>
                                    <span class="texto-pequeño">{{ translate('AutorReceta') }} {{ reci.nick }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-5 justify-content-center mt-3">
<!--                                    <button class="border-0 bg-transparent">-->
<!--                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"-->
<!--                                             class="bi bi-chat" viewBox="0 0 16 16">-->
<!--                                            <path-->
<!--                                                d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105"/>-->
<!--                                        </svg>-->
<!--                                    </button>-->
                                    <button class="border-0 bg-transparent" v-on:click="meGusta(reci.id, reci.favoriteState)">
                                        {{ reci.total }}
                                        <svg v-if="reci.favoriteState==false" xmlns="http://www.w3.org/2000/svg" width="20"
                                             height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path
                                                d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path
                                                d="m8 2.748-.717-.737C5.6.281 2.514.878 122222.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"
                                                fill="red"/>
                                        </svg>
                                    </button>
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
                    <p>{{ translate('ParrafoPagina') }} {{ pagination.current_page }} {{ translate('DeEntreNumeroPagina') }}
                        {{ pagination.last_page }}</p>

                </section>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
