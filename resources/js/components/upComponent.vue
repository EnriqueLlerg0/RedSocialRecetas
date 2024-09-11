<script>
export default {
    name: "upComponent",
    data() {
        return {
            recipeId:'',
            isOpen1:true,
            isopen2:'hidden',
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
            arrayDatosIngredientes:[]
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
        modalShow() {
            Swal.fire({
                title: 'Pulsa Ok para añadir un filtro',
                html: '<input class="form-control border-black" list="datalistOptions2" id="exampleDataList2" placeholder="Type to search...">\n' +
                    '                        <datalist id="datalistOptions2">\n' +
                    '                            <option value="Carne de res"></option>\n' +
                    '                            <option value="Carne de cerdo"></option>\n' +
                    '                            <option value="Carne de aves de corral (pollo, pavo, pato)"></option>\n' +
                    '                            <option value="Carne de caza (venado, conejo, faisán)"></option>\n' +
                    '                            <option value="Pescado blanco (merluza, lenguado)"></option>\n' +
                    '                            <option value="Pescado azul (salmón, atún)"></option>\n' +
                    '                            <option value="Crustáceos (langosta, camarones)"></option>\n' +
                    '                            <option value="Moluscos (calamares, mejillones)"></option>\n' +
                    '                            <option value="Verduras de hoja verde (espinacas, lechuga)"></option>\n' +
                    '                            <option value="Tubérculos (patatas, zanahorias)"></option>\n' +
                    '                            <option value="Legumbres (judías, lentejas)"></option>\n' +
                    '                            <option value="Vegetales crucíferos (brócoli, coliflor)"></option>\n' +
                    '                            <option value="Frutas tropicales (mango, piña)"></option>\n' +
                    '                            <option value="Frutas de clima templado (manzanas, peras)"></option>\n' +
                    '                            <option value="Frutas cítricas (naranjas, limones)"></option>\n' +
                    '                            <option value="Bayas (fresas, arándanos)"></option>\n' +
                    '                            <option value="Arroz"></option>\n' +
                    '                            <option value="Trigo"></option>\n' +
                    '                            <option value="Maíz"></option>\n' +
                    '                            <option value="Avena"></option>\n' +
                    '                            <option value="Leche"></option>\n' +
                    '                            <option value="Quesos (cheddar, mozzarella)"></option>\n' +
                    '                            <option value="Yogur"></option>\n' +
                    '                            <option value="Mantequilla"></option>\n' +
                    '                            <option value="Huevos revueltos"></option>\n' +
                    '                            <option value="Huevos fritos"></option>\n' +
                    '                            <option value="Tortillas"></option>\n' +
                    '                            <option value="Mayonesa"></option>\n' +
                    '                            <option value="Salsas"></option>\n' +
                    '                            <option value="Pizzas"></option>\n' +
                    '                            <option value="Hamburguesas"></option>\n' +
                    '                            <option value="Sándwiches"></option>\n' +
                    '                            <option value="Platos de pasta"></option>\n' +
                    '                            <option value="Embutidos (salchichas, jamón)"></option>\n' +
                    '                            <option value="Conservas (atún en lata, verduras en conserva)"></option>\n' +
                    '                            <option value="Snacks (patatas fritas, galletas)"></option>\n' +
                    '                            <option value="Comida rápida"></option>\n' +
                    '                            <option value="Pasteles y tartas"></option>\n' +
                    '                            <option value="Helados"></option>\n' +
                    '                            <option value="Chocolate"></option>\n' +
                    '                            <option value="Galletas y dulces"></option>\n' +
                    '                            <option value="Agua"></option>\n' +
                    '                            <option value="Refrescos"></option>\n' +
                    '                            <option value="Jugos de frutas"></option>\n' +
                    '                            <option value="Café y té"></option>' +
                    '                        </datalist>'
            }).then(() => {
                let dato = document.querySelector("#exampleDataList2").value;

                let p = document.createElement("p");
                p.textContent = dato;

                let contenedor = document.querySelector("#modal2");
                contenedor.appendChild(p);
            });
        },
        modalShow2() {
            let dato1 = "";
            let dato2 = "";
            let cuerpo = document.querySelectorAll("#tbodyIngredientesSubir");

            Swal.fire({
                title: 'Ingresa el ingrediente',
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

                if (result.isConfirmed) {
                    if (result.isConfirmed) {
                        dato1 = result.value;
                    }

                    if (dato1 != "") {
                        Swal.fire({
                            title: 'Ingresa la cantidad',
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

                            if (result.isConfirmed) {
                                if (result.isConfirmed) {
                                    dato2 = result.value;
                                }

                                if (dato2 != "") {
                                    Swal.fire({
                                        title: 'Pulsa Ok para añadir un filtro',
                                        showCancelButton: true,
                                        inputAttributes: {
                                            autocapitalize: 'off'
                                        },
                                        confirmButtonText: 'Confirmar',
                                        html:
                                        // '<input class="form-control border-black" list="datalistOptions2" id="exampleDataList2" placeholder="Type to search...">\n' +
                                            '                        <select class="w-100 form-select" id="datalistOptions2">\n' +
                                            '                            <option value="Carne de res">Carne de res</option>\n' +
                                            '                            <option value="Carne de cerdo">Carne de cerdo</option>\n' +
                                            '                            <option value="Carne de aves de corral (pollo, pavo, pato)">Carne de aves de corral (pollo, pavo, pato)</option>\n' +
                                            '                            <option value="Carne de caza (venado, conejo, faisán)">Carne de caza (venado, conejo, faisán)</option>\n' +
                                            '                            <option value="Pescado blanco (merluza, lenguado)">Pescado blanco (merluza, lenguado)</option>\n' +
                                            '                            <option value="Pescado azul (salmón, atún)">Pescado azul (salmón, atún)</option>\n' +
                                            '                            <option value="Crustáceos (langosta, camarones)">Crustáceos (langosta, camarones)</option>\n' +
                                            '                            <option value="Moluscos (calamares, mejillones)">Moluscos (calamares, mejillones)</option>\n' +
                                            '                            <option value="Verduras de hoja verde (espinacas, lechuga)">Verduras de hoja verde (espinacas, lechuga)</option>\n' +
                                            '                            <option value="Tubérculos (patatas, zanahorias)">Tubérculos (patatas, zanahorias)</option>\n' +
                                            '                            <option value="Legumbres (judías, lentejas)">Legumbres (judías, lentejas)</option>\n' +
                                            '                            <option value="Vegetales crucíferos (brócoli, coliflor)">Vegetales crucíferos (brócoli, coliflor)</option>\n' +
                                            '                            <option value="Frutas tropicales (mango, piña)">Frutas tropicales (mango, piña)</option>\n' +
                                            '                            <option value="Frutas de clima templado (manzanas, peras)">Frutas de clima templado (manzanas, peras)</option>\n' +
                                            '                            <option value="Frutas cítricas (naranjas, limones)">Frutas cítricas (naranjas, limones)</option>\n' +
                                            '                            <option value="Bayas (fresas, arándanos)">Bayas (fresas, arándanos)</option>\n' +
                                            '                            <option value="Arroz">Arroz</option>\n' +
                                            '                            <option value="Trigo">Trigo</option>\n' +
                                            '                            <option value="Maíz">Maíz</option>\n' +
                                            '                            <option value="Avena">Avena</option>\n' +
                                            '                            <option value="Leche">Leche</option>\n' +
                                            '                            <option value="Quesos (cheddar, mozzarella)">Quesos (cheddar, mozzarella)</option>\n' +
                                            '                            <option value="Yogur">Yogur</option>\n' +
                                            '                            <option value="Mantequilla">Mantequilla</option>\n' +
                                            '                            <option value="Huevos revueltos">Huevos revueltos</option>\n' +
                                            '                            <option value="Huevos fritos">Huevos fritos</option>\n' +
                                            '                            <option value="Tortillas">Tortillas</option>\n' +
                                            '                            <option value="Mayonesa">Mayonesa</option>\n' +
                                            '                            <option value="Salsas">Salsas</option>\n' +
                                            '                            <option value="Pizzas">Pizzas</option>\n' +
                                            '                            <option value="Hamburguesas">Hamburguesas</option>\n' +
                                            '                            <option value="Sándwiches">Sándwiches</option>\n' +
                                            '                            <option value="Platos de pasta">Platos de pasta</option>\n' +
                                            '                            <option value="Embutidos (salchichas, jamón)">Embutidos (salchichas, jamón)</option>\n' +
                                            '                            <option value="Conservas (atún en lata, verduras en conserva)">Conservas (atún en lata, verduras en conserva)</option>\n' +
                                            '                            <option value="Snacks (patatas fritas, galletas)">Snacks (patatas fritas, galletas)</option>\n' +
                                            '                            <option value="Comida rápida">Comida rápida</option>\n' +
                                            '                            <option value="Pasteles y tartas">Pasteles y tartas</option>\n' +
                                            '                            <option value="Helados">Helados</option>\n' +
                                            '                            <option value="Chocolate">Chocolate</option>\n' +
                                            '                            <option value="Galletas y dulces">Galletas y dulces</option>\n' +
                                            '                            <option value="Agua">Agua</option>\n' +
                                            '                            <option value="Refrescos">Refrescos</option>\n' +
                                            '                            <option value="Jugos de frutas">Jugos de frutas</option>\n' +
                                            '                            <option value="Café y té">Café y té</option>' +
                                            '                        </select>'
                                        ,
                                        preConfirm: (nombre) => {
                                            return nombre;
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            let dato = document.querySelector("#datalistOptions2").value;

                                            let arrayDatos = {
                                                nombre: '',
                                                cantidad: '',
                                                filtros: ''
                                            };
                                            arrayDatos['nombre']=dato1;
                                            arrayDatos['cantidad']=dato2;
                                            arrayDatos['filtros']=dato;
                                            console.log(arrayDatos['filtros']);

                                            this.arrayDatosIngredientes.push(arrayDatos);
                                        }
                                    });
                                } else {
                                    Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                                }
                            }
                        });
                    } else {
                        Swal.fire("ERROR, no se permiten campos vacios", "", "error");
                    }
                }
            });
        },
        async subirDatos2() {
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

            const tipos_comida = [
                "Carne de res",
                "Carne de cerdo",
                "Carne de aves de corral (pollo, pavo, pato)",
                "Carne de caza (venado, conejo, faisán)",
                "Pescado blanco (merluza, lenguado)",
                "Pescado azul (salmón, atún)",
                "Crustáceos (langosta, camarones)",
                "Moluscos (calamares, mejillones)",
                "Verduras de hoja verde (espinacas, lechuga)",
                "Tubérculos (patatas, zanahorias)",
                "Legumbres (judías, lentejas)",
                "Vegetales crucíferos (brócoli, coliflor)",
                "Frutas tropicales (mango, piña)",
                "Frutas de clima templado (manzanas, peras)",
                "Frutas cítricas (naranjas, limones)",
                "Bayas (fresas, arándanos)",
                "Arroz",
                "Trigo",
                "Maíz",
                "Avena",
                "Leche",
                "Quesos (cheddar, mozzarella)",
                "Yogur",
                "Mantequilla",
                "Huevos revueltos",
                "Huevos fritos",
                "Tortillas",
                "Mayonesa",
                "Salsas",
                "Pizzas",
                "Hamburguesas",
                "Sándwiches",
                "Platos de pasta",
                "Embutidos (salchichas, jamón)",
                "Conservas (atún en lata, verduras en conserva)",
                "Snacks (patatas fritas, galletas)",
                "Comida rápida",
                "Pasteles y tartas",
                "Helados",
                "Chocolate",
                "Galletas y dulces",
                "Agua",
                "Refrescos",
                "Jugos de frutas",
                "Café y té"
            ];

            this.nombreIngredientes=[];
            this.cantidadIngredientes=[];
            this.filtroIngredientes=[];

            if (this.arrayDatosIngredientes.length>0 && this.imagenRecetaActualizada != "" && this.titulo != "" && this.descripcion != "" && this.pais != "") {
                if (countries.indexOf(this.pais) != -1) {
                    for (let i = 0; i < this.arrayDatosIngredientes.length; i++) {
                        this.nombreIngredientes.push(this.arrayDatosIngredientes[i].nombre);
                        this.cantidadIngredientes.push(this.arrayDatosIngredientes[i].cantidad);
                        this.filtroIngredientes.push(tipos_comida.indexOf(this.arrayDatosIngredientes[i].filtros)+1);
                    }

                    const formData = new FormData();
                    formData.append('image', this.selectedFile);

                    try {
                        const response = await axios.post('/upload', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        });
                        this.imageUrl = response.data.url;

                        axios.put("/updateRecipe", {
                            recipeId: this.recipeId,
                            img: this.imageUrl,
                            tit: this.titulo,
                            nom: this.nombreIngredientes,
                            cant: this.cantidadIngredientes,
                            filt: this.filtroIngredientes,
                            desc: this.descripcion,
                            pais: this.pais
                        }).then((response) => {
                            Swal.fire({
                                position: "bottom-end",
                                icon: "success",
                                title: "Receta actualizada",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            this.isopen2 = 'hidden';
                            this.isopen2Mobile = false;
                            this.isOpen1 = true;
                            this.obtenerRecetasUsuario();

                            this.titulo = "";
                            this.imagenRecetaActualizada = "";
                            this.descripcion = "";
                            this.pais = "";
                            this.nombreIngredientes = [];
                            this.cantidadIngredientes = [];
                            this.filtroIngredientes = [];

                            const rows = document.querySelectorAll('.ing');
                            rows.forEach(row => row.parentNode.removeChild(row));

                        }).catch(error => {
                            if (error.response.status === 422) {
                                Swal.fire("ERROR, el titulo de la receta ya se encuentra en uso", "", "error");
                            } else {
                                console.error(error);
                            }
                        });
                    } catch (error) {
                        console.error('There was an error uploading the file!', error);
                    }
                } else {
                    Swal.fire("ERROR, el pais seleccionado no existe", "", "error");
                }
            } else {
                Swal.fire("ERROR, alguno de los campos esta sin rellenar", "", "error");
            }

        },
        async subirDatos() {
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

            const tipos_comida = [
                "Carne de res",
                "Carne de cerdo",
                "Carne de aves de corral (pollo, pavo, pato)",
                "Carne de caza (venado, conejo, faisán)",
                "Pescado blanco (merluza, lenguado)",
                "Pescado azul (salmón, atún)",
                "Crustáceos (langosta, camarones)",
                "Moluscos (calamares, mejillones)",
                "Verduras de hoja verde (espinacas, lechuga)",
                "Tubérculos (patatas, zanahorias)",
                "Legumbres (judías, lentejas)",
                "Vegetales crucíferos (brócoli, coliflor)",
                "Frutas tropicales (mango, piña)",
                "Frutas de clima templado (manzanas, peras)",
                "Frutas cítricas (naranjas, limones)",
                "Bayas (fresas, arándanos)",
                "Arroz",
                "Trigo",
                "Maíz",
                "Avena",
                "Leche",
                "Quesos (cheddar, mozzarella)",
                "Yogur",
                "Mantequilla",
                "Huevos revueltos",
                "Huevos fritos",
                "Tortillas",
                "Mayonesa",
                "Salsas",
                "Pizzas",
                "Hamburguesas",
                "Sándwiches",
                "Platos de pasta",
                "Embutidos (salchichas, jamón)",
                "Conservas (atún en lata, verduras en conserva)",
                "Snacks (patatas fritas, galletas)",
                "Comida rápida",
                "Pasteles y tartas",
                "Helados",
                "Chocolate",
                "Galletas y dulces",
                "Agua",
                "Refrescos",
                "Jugos de frutas",
                "Café y té"
            ];

            this.nombreIngredientes=[];
            this.cantidadIngredientes=[];
            this.filtroIngredientes=[];

            if (this.arrayDatosIngredientes.length>0 && this.imagen != "" && this.titulo != "" && this.descripcion != "" && this.pais != "") {
                if (countries.indexOf(this.pais) != -1) {
                    for (let i = 0; i < this.arrayDatosIngredientes.length; i++) {
                        this.nombreIngredientes.push(this.arrayDatosIngredientes[i].nombre);
                        this.cantidadIngredientes.push(this.arrayDatosIngredientes[i].cantidad);
                        this.filtroIngredientes.push(tipos_comida.indexOf(this.arrayDatosIngredientes[i].filtros)+1);
                    }

                    const formData = new FormData();
                    formData.append('image', this.selectedFile);

                    console.log(formData);

                    try {
                        const response = await axios.post('/upload', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        });
                        this.imageUrl = response.data.url;

                        await axios.post("/subirReceta", {
                            usuId: JSON.parse(localStorage.getItem('user'))['id'],
                            img: this.imageUrl,
                            tit: this.titulo,
                            nom: this.nombreIngredientes,
                            cant: this.cantidadIngredientes,
                            filt: this.filtroIngredientes,
                            desc: this.descripcion,
                            pais: this.pais
                        }).then((response) => {
                            Swal.fire({
                                position: "bottom-end",
                                icon: "success",
                                title: "Receta creada",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            this.titulo = "";
                            this.imagen = "";
                            this.descripcion = "";
                            this.pais = "";
                            this.nombreIngredientes = [];
                            this.cantidadIngredientes = [];
                            this.filtroIngredientes = [];

                            const rows = document.querySelectorAll('.ing');
                            rows.forEach(row => row.parentNode.removeChild(row));

                        }).catch(error => {
                            if (error.response.status === 422) {
                                Swal.fire("ERROR, el titulo de la receta ya se encuentra en uso", "", "error");
                            } else {
                                console.error(error);
                            }
                        });

                    } catch (error) {
                        console.error('There was an error uploading the file!', error);
                    }
                } else {
                    Swal.fire("ERROR, el pais seleccionado no existe", "", "error");
                }
            } else {
                Swal.fire("ERROR, alguno de los campos esta sin rellenar", "", "error");
            }

        },
        previewImage(event) {
            this.selectedFile = event.target.files[0];
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagen = e.target.result;
                    this.imagenRecetaActualizada = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagen = '';
                this.imagenRecetaActualizada = '';
            }
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
        async pedirDatosReceta(name) {
            console.log(this.arrayDatosIngredientes);
            this.isVisible = true;
            this.arrayDatosIngredientes=[];
            this.nombreIngredientes = [];
            this.cantidadIngredientes.length = 0;
            this.filtroIngredientes = [];

            await axios.get("/getRecipeData", {
                params: {
                    nameRecipe: name
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

                const tipos_comida = [
                    "Carne de res",
                    "Carne de cerdo",
                    "Carne de aves de corral (pollo, pavo, pato)",
                    "Carne de caza (venado, conejo, faisán)",
                    "Pescado blanco (merluza, lenguado)",
                    "Pescado azul (salmón, atún)",
                    "Crustáceos (langosta, camarones)",
                    "Moluscos (calamares, mejillones)",
                    "Verduras de hoja verde (espinacas, lechuga)",
                    "Tubérculos (patatas, zanahorias)",
                    "Legumbres (judías, lentejas)",
                    "Vegetales crucíferos (brócoli, coliflor)",
                    "Frutas tropicales (mango, piña)",
                    "Frutas de clima templado (manzanas, peras)",
                    "Frutas cítricas (naranjas, limones)",
                    "Bayas (fresas, arándanos)",
                    "Arroz",
                    "Trigo",
                    "Maíz",
                    "Avena",
                    "Leche",
                    "Quesos (cheddar, mozzarella)",
                    "Yogur",
                    "Mantequilla",
                    "Huevos revueltos",
                    "Huevos fritos",
                    "Tortillas",
                    "Mayonesa",
                    "Salsas",
                    "Pizzas",
                    "Hamburguesas",
                    "Sándwiches",
                    "Platos de pasta",
                    "Embutidos (salchichas, jamón)",
                    "Conservas (atún en lata, verduras en conserva)",
                    "Snacks (patatas fritas, galletas)",
                    "Comida rápida",
                    "Pasteles y tartas",
                    "Helados",
                    "Chocolate",
                    "Galletas y dulces",
                    "Agua",
                    "Refrescos",
                    "Jugos de frutas",
                    "Café y té"
                ];

                let arrayDatos = {
                    nombre: '',
                    cantidad: '',
                    filtros: ''
                };


                this.nombreIngredientes.forEach((element, index)=>
                {
                    console.log(element);
                    arrayDatos['nombre']=this.nombreIngredientes[index];
                    arrayDatos['cantidad']=this.cantidadIngredientes[index];
                    arrayDatos['filtros']=tipos_comida[this.filtroIngredientes[index]-1];

                    this.arrayDatosIngredientes.push(arrayDatos);

                    arrayDatos = {
                        nombre: '',
                        cantidad: '',
                        filtros: ''
                    };
                });

                this.isOpen1 = false;
                this.isopen2 = 'visible';
                this.isopen2Mobile=true;
            }).catch(error => {
                console.error(error);

            });
        },
        rellenarTabla() {
            const tipos_comida = ["Carne de res", "Carne de cerdo", "Carne de aves de corral (pollo, pavo, pato)", "Carne de caza (venado, conejo, faisán)", "Pescado blanco (merluza, lenguado)", "Pescado azul (salmón, atún)", "Crustáceos (langosta, camarones)", "Moluscos (calamares, mejillones)", "Verduras de hoja verde (espinacas, lechuga)", "Tubérculos (patatas, zanahorias)", "Legumbres (judías, lentejas)", "Vegetales crucíferos (brócoli, coliflor)", "Frutas tropicales (mango, piña)", "Frutas de clima templado (manzanas, peras)", "Frutas cítricas (naranjas, limones)", "Bayas (fresas, arándanos)", "Arroz", "Trigo", "Maíz", "Avena", "Leche", "Quesos (cheddar, mozzarella)", "Yogur", "Mantequilla", "Huevos revueltos", "Huevos fritos", "Tortillas", "Mayonesa", "Salsas", "Pizzas", "Hamburguesas", "Sándwiches", "Platos de pasta", "Embutidos (salchichas, jamón)", "Conservas (atún en lata, verduras en conserva)", "Snacks (patatas fritas, galletas)", "Comida rápida", "Pasteles y tartas", "Helados", "Chocolate", "Galletas y dulces", "Agua", "Refrescos", "Jugos de frutas", "Café y té"];

            let cuerpo = document.querySelector("#tbodyIngredientes");
            while (cuerpo.firstChild) {
                cuerpo.removeChild(cuerpo.firstChild);
            }

            this.recipeData.cantidad.forEach((element, index) => {
                let td1 = document.createElement("td");
                td1.textContent = this.recipeData.ingrediente[index];
                let td2 = document.createElement("td");
                td2.textContent = this.recipeData.cantidad[index];
                let td4 = document.createElement("td");
                td4.textContent = tipos_comida[this.recipeData.cate[index] - 1];

                let i = document.createElement('i');
                i.classList.add("fa-solid", "fa-trash");
                i.addEventListener("click", (element) => {
                    let padre = (element.target.parentNode).parentNode;
                    padre.remove();

                    this.cont--;
                })
                let td3 = document.createElement("td");
                td3.appendChild(i);

                let tr = document.createElement("tr");
                tr.classList.add("ing")
                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td4);
                tr.appendChild(td3);

                cuerpo.appendChild(tr);
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
        async uploadImage() {
            const formData = new FormData();
            formData.append('image', this.hta);

            try {
                const response = await axios.post('/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                this.imageUrl = response.data.url;
                console.log(this.imageUrl);
            } catch (error) {
                console.error('There was an error uploading the file!', error);
            }
        },
        cerrarSesion() {
            localStorage.removeItem("user");
        },
        eliminarIngrediente(element){
            this.arrayDatosIngredientes.splice(element,1);
        },
        limpiarDatos()
        {
            this.titulo = "";
            this.descripcion = "";
            this.pais = "";
            this.arrayDatosIngredientes=[];
        }

    },
    mounted() {
        this.rolUser = JSON.parse(localStorage.getItem('user'))['roll'];
        this.getTranslations();
        this.loadTranslations(this.currentLang);

        this.idUser=JSON.parse(localStorage.getItem('user'))['id'];
        this.obtenerRecetasUsuario();
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

                <button class="text-decoration-none  text-center border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/up')">
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

                <button class="text-decoration-none  text-center border-0 menuPrincipalSeleccionado" v-on:click="actionLink('/up')">
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

            <section class="col-12 d-flex mt-4 gap-4">
                <div class="col-5 d-flex justify-content-end">
                    <span v-on:click="isOpen1=false, isopen2Mobile=false, isOpen3=true, limpiarDatos()" class="btn btn-primary tamañoBotonSubir">{{ translate('SubirBoton') }}</span>
                </div>
                <div class="col-6">
                    <span v-on:click="isOpen1=true, isopen2Mobile=false, isOpen3=false" class="btn btn-warning">{{ translate('GestionarBoton') }}</span>
                </div>
            </section>
<!--ver-->
            <section class="h-450-px mt-40 d-flex justify-content-center align-items-center" v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center w-75 h-100 border border-black border-1 rounded-4">
                    <div class="w-100 h-100 d-flex flex-column justify-content-center align-items-center gap-4">
                        <h3>{{ translate('TusRecetash3') }}</h3>
                        <div class="w-75 d-flex flex-column gap-3 overflow-y-scroll">
                            <div class="d-flex gap-3" v-for="(reci, index) in allRecipe" :key="index">
                                <span class="w-50 custom-cursor" v-on:click="actionLink(`/recipe?nameRecipe=${reci.name_recipe}`)">{{ reci.name_recipe }}</span>
                                <div class="d-flex gap-3">
                                    <i v-on:click="this.nameRecipe=reci.name_recipe, this.recipeId=reci.id, pedirDatosReceta(reci.name_recipe)" class="fa-solid fa-pencil custom-cursor"></i>
                                    <svg v-on:click="this.idRecipe=reci.id, eliminarReceta()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3 custom-cursor" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
<!--subir-->
            <section class="h-100 mt-40 d-flex justify-content-center align-items-center" v-bind:class="{ 'd-none': !isOpen3}" v-if="isOpen3">

                <form action="" class="d-flex flex-column justify-content-center w-75 h-100 border border-black border-1 rounded-4">
                    <div class="row h-100">
                        <div class="d-flex flex-column justify-content-center ">
                            <div class="card mx-3 border-black">
                                <img v-if="this.imagen" :src="imagen" id="imagenFile" class="card-img-top mh-75" alt="...">
                                <div class="card-body">
                                    <input class="form-control border-black" type="file" id="formFile" v-on:change="previewImage" accept="image/*">
                                </div>
                            </div>

                            <div class="my-3 mx-3">
                                <label for="exampleFormControlInput1" class="form-label">{{ translate('TituloLabel') }}</label>
                                <input type="text" v-model="titulo" class="form-control border-black w-100" id="exampleFormControlInput1" :placeholder="translate('TituloInput')">
                            </div>

                            <div class="mb-3 mx-3">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                {{ translate('IngrendientesAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body d-flex flex-column gap-2">
                                                <div class="custom-cursor" id="modal" v-on:click="modalShow2()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-plus" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
                                                        <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
                                                    </svg>
                                                </div>
                                                <div class="overflow-y-scroll form-control border-1 border-black bg-transparent d-flex flex-wrap gap-2" style="height: 100px" id="exampleFormControlTextarea1" rows="3">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ translate('TheadNombre') }}</th>
                                                            <th scope="col">{{ translate('TheadCantidad') }}</th>
                                                            <th scope="col">{{ translate('TheadFiltros') }}</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="tbodyIngredientesSubir">
                                                            <tr v-for="(reci, index) in arrayDatosIngredientes" :key="index">
                                                                <td>{{ reci.nombre }}</td>
                                                                <td>{{ reci.cantidad }}</td>
                                                                <td>{{ reci.filtros }}</td>
                                                                <td><i class="fa-solid fa-trash" v-on:click="eliminarIngrediente(index)"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                {{ translate('DescripcionAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <textarea v-model="descripcion" class="form-control border-black" style="resize: none; height: 150px" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mx-3">
                                <label for="exampleFormControlTextarea1" class="form-label">{{ translate('PaisLabel') }}</label>

                                <input v-model="pais" class="form-control border-black" list="datalistOptions" id="exampleDataList" :placeholder="translate('PaisInput')">
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

                            <div class="mt-4 d-flex justify-content-center">
                                <input type="submit" value="Enviar" v-on:click.prevent="subirDatos()" href="#" class="btn btn-primary w-25">
                            </div>
                        </div>
                    </div>
                </form>

            </section>
<!--actualizar-->
            <section class="h-100 mt-40 d-flex justify-content-center align-items-center" v-bind:style="{ 'd-none': !isopen2Mobile}" v-if="isopen2Mobile">

                <form action="" class="d-flex flex-column justify-content-center w-75 h-100 border border-black border-1 rounded-4">
                    <div class="row h-100">
                        <div class="d-flex flex-column justify-content-center ">
                            <div class="card mx-3 border-black">
                                <img v-if="imagenRecetaActualizada" :src="this.imagenRecetaActualizada" id="imagenFile" class="card-img-top mh-75" alt="...">
                                <div class="card-body">
                                    <input class="form-control border-black" type="file" id="formFile" v-on:change="previewImage" accept="image/*">
                                </div>
                            </div>

                            <div class="my-3 mx-3">
                                <label for="exampleFormControlInput1" class="form-label">{{ translate('TituloLabel') }}</label>
                                <input type="text" v-model="this.titulo" class="form-control border-black w-100" id="exampleFormControlInput1" placeholder="Introduce titulo receta">
                            </div>

                            <div class="mb-3 mx-3">


                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                {{ translate('IngrendientesAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body d-flex flex-column gap-2">
                                                <div class="custom-cursor" id="modal" v-on:click="modalShow2()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-plus" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
                                                        <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
                                                    </svg>
                                                </div>
                                                <div class="overflow-y-scroll form-control border-1 border-black bg-transparent d-flex flex-wrap gap-2" style="height: 100px" id="exampleFormControlTextarea1" rows="3">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ translate('TheadNombre') }}</th>
                                                            <th scope="col">{{ translate('TheadCantidad') }}</th>
                                                            <th scope="col">{{ translate('TheadFiltros') }}</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="tbodyIngredientesSubir">
                                                            <tr v-for="(reci, index) in arrayDatosIngredientes" :key="index">
                                                                <td>{{ reci.nombre }}</td>
                                                                <td>{{ reci.cantidad }}</td>
                                                                <td>{{ reci.filtros }}</td>
                                                                <td><i class="fa-solid fa-trash" v-on:click="eliminarIngrediente(index)"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                {{ translate('DescripcionAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <textarea v-model="this.descripcion" class="form-control border-black" style="resize: none; height: 150px" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mb-3 mx-3">
                                <label for="exampleFormControlTextarea1" class="form-label">{{ translate('PaisLabel') }}</label>

                                <input v-model="this.pais" class="form-control border-black" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
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

                            <div class="mt-4 d-flex justify-content-center gap-3">
                                <input type="submit" value="Actualizar" v-on:click.prevent="subirDatos" href="#" class="btn btn-primary text-center">
                                <input type="submit" value="Cancelar" v-on:click.prevent="isOpen1=true, isopen2Mobile=false" href="#" class="btn btn-danger  text-center">
                            </div>
                        </div>
                    </div>
                </form>

            </section>
        </div>

        <div class="desktop-item">
            <section class="col-7 seccionSubirReceta d-flex gap-5 mt-3 w-75">
                <div class="col-6 d-flex justify-content-end">
                    <span v-on:click="isOpen1=false, isopen2='hidden', isOpen3=true, limpiarDatos()" class="btn btn-primary tamañoBotonSubir">{{ translate('SubirBoton') }}</span>
                </div>
                <div class="col-6">
                    <span v-on:click="isOpen1=true, isopen2='hidden', isOpen3=false" class="btn btn-warning">{{ translate('GestionarBoton') }}</span>
                </div>
            </section>
<!--subir-->
            <section v-bind:class="{ 'd-none': !isOpen3}" v-if="isOpen3" class="col-12 d-flex flex-column justify-content-center fondo1 h-100 position-absolute">

                <form action="" class="col-7 seccionSubirReceta border border-black border-1 rounded-4">
                    <div class="row h-100">
                        <div class="col-6 d-flex flex-column justify-content-center ">
                            <div class="card w-100 mx-3 border-black">
                                <img v-if="this.imagen" :src="imagen" id="imagenFile" class="card-img-top mh-75" alt="...">
                                <div class="card-body">
                                    <input class="form-control border-black" type="file" id="formFile" v-on:change="previewImage" accept="image/*">
                                </div>
                            </div>

                            <div class="mt-3 mx-3">
                                <label for="exampleFormControlInput1" class="form-label">{{ translate('TituloLabel') }}</label>
                                <input type="text" v-model="titulo" class="form-control border-black w-100" id="exampleFormControlInput1" :placeholder="translate('TituloInput')">
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                <input type="submit" value="Enviar" v-on:click.prevent="subirDatos()" href="#" class="btn btn-primary w-25">
                            </div>
                        </div>
                        <div class="col-6 d-flex flex-column justify-content-center">

                            <div class="mb-3 mx-3">


                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                {{ translate('IngrendientesAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body d-flex flex-column gap-2">
                                                <div class="custom-cursor" id="modal" v-on:click="modalShow2()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-plus" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
                                                        <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
                                                    </svg>
                                                </div>
                                                <div class="overflow-y-scroll form-control border-1 border-black bg-transparent d-flex flex-wrap gap-2" style="height: 100px" id="exampleFormControlTextarea1" rows="3">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ translate('TheadNombre') }}</th>
                                                            <th scope="col">{{ translate('TheadCantidad') }}</th>
                                                            <th scope="col">{{ translate('TheadFiltros') }}</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="tbodyIngredientesSubir">
                                                            <tr v-for="(reci, index) in arrayDatosIngredientes" :key="index">
                                                                <td>{{ reci.nombre }}</td>
                                                                <td>{{ reci.cantidad }}</td>
                                                                <td>{{ reci.filtros }}</td>
                                                                <td><i class="fa-solid fa-trash pointer" v-on:click="eliminarIngrediente(index)"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                {{ translate('DescripcionAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <textarea v-model="descripcion" class="form-control border-black" style="resize: none; height: 150px" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mb-3 mx-3">
                                <label for="exampleFormControlTextarea1" class="form-label">{{ translate('PaisLabel') }}</label>

                                <input v-model="pais" class="form-control border-black" list="datalistOptions" id="exampleDataList" :placeholder="translate('PaisInput')">
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
                </form>

            </section>
<!--ver-->
            <section v-bind:class="{ 'd-none': !isOpen1}" v-if="isOpen1" class="col-12 d-flex flex-column justify-content-center fondo1 h-100 position-absolute">

                <section>
                    <div class="pantalla" v-bind:class="{ 'd-none': !isVisible}" v-if="isVisible">
                        <div class="loader"></div>
                    </div>
                </section>

                <div class="d-flex justify-content-center col-7 seccionSubirReceta border border-black border-1 rounded-4">
                    <div class="w-75 izquierda ml-1 mt-5 h-65 d-flex flex-column align-items-center gap-4">
                        <h3>{{ translate('TusRecetash3') }}</h3>
                        <div class="col-6 h-75 d-flex flex-column gap-3 overflow-y-scroll">
                            <div class="d-flex gap-3" v-for="(reci, index) in allRecipe" :key="index">
                                <span class="w-50 custom-cursor" v-on:click="actionLink(`/recipe?nameRecipe=${reci.name_recipe}`)">{{ reci.name_recipe }}</span>
                                <div class="d-flex gap-3">
                                    <i v-on:click="this.nameRecipe=reci.name_recipe, this.recipeId=reci.id, pedirDatosReceta(reci.name_recipe)" class="fa-solid fa-pencil custom-cursor"></i>
                                    <svg v-on:click="this.idRecipe=reci.id, eliminarReceta()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3 custom-cursor" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
<!--actualizar-->
            <section v-bind:style="{ 'visibility': isopen2}" v-if="isopen2" class="col-12 d-flex flex-column justify-content-center fondo1 h-100 position-absolute">

                <form action="" class="col-7 seccionSubirReceta border border-black border-1 rounded-4">
                    <div class="row h-100">
                        <div class="col-6 d-flex flex-column justify-content-center ">
                            <div class="card w-100 mx-3 border-black">
                                <img v-if="imagenRecetaActualizada" :src="this.imagenRecetaActualizada" id="imagenFile" class="card-img-top mh-75" alt="...">
                                <div class="card-body">
                                    <input class="form-control border-black" type="file" id="formFile" v-on:change="previewImage" accept="image/*">
                                </div>
                            </div>

                            <div class="mt-3 mx-3">
                                <label for="exampleFormControlInput1" class="form-label">{{ translate('TituloLabel') }}</label>
                                <input type="text" v-model="this.titulo" class="form-control border-black w-100" id="exampleFormControlInput1" placeholder="Introduce titulo receta">
                            </div>

                            <div class="mt-4 d-flex justify-content-center gap-3">
                                <input type="submit" value="Actualizar" v-on:click.prevent="subirDatos2" href="#" class="btn btn-primary text-center">
                                <input type="submit" value="Cancelar" v-on:click.prevent="isOpen1=true, isopen2='hidden'" href="#" class="btn btn-danger w-25">
                            </div>
                        </div>

                        <div class="col-6 d-flex flex-column justify-content-center">

                            <div class="mb-3 mx-3">


                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                {{ translate('IngrendientesAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body d-flex flex-column gap-2">
                                                <div class="custom-cursor" id="modal" v-on:click="modalShow2()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-plus" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
                                                        <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
                                                    </svg>
                                                </div>
                                                <div class="overflow-y-scroll form-control border-1 border-black bg-transparent d-flex flex-wrap gap-2" style="height: 100px" id="exampleFormControlTextarea1" rows="3">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th scope="col">{{ translate('TheadNombre') }}</th>
                                                            <th scope="col">{{ translate('TheadCantidad') }}</th>
                                                            <th scope="col">{{ translate('TheadFiltros') }}</th>
                                                            <th scope="col"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="tbodyIngredientesSubir">
                                                            <tr v-for="(reci, index) in arrayDatosIngredientes" :key="index">
                                                                <td>{{ reci.nombre }}</td>
                                                                <td>{{ reci.cantidad }}</td>
                                                                <td>{{ reci.filtros }}</td>
                                                                <td><i class="fa-solid fa-trash" v-on:click="eliminarIngrediente(index)"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                {{ translate('DescripcionAcordeon') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <textarea v-model="this.descripcion" class="form-control border-black" style="resize: none; height: 150px" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="mb-3 mx-3">
                                <label for="exampleFormControlTextarea1" class="form-label">{{ translate('PaisLabel') }}</label>

                                <input v-model="this.pais" class="form-control border-black" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
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
                </form>

            </section>
        </div>
    </div>
</template>

<style scoped>

</style>
