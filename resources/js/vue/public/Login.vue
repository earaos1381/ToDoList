<template>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">

                        <div class="app-brand justify-content-center">
                            <a class="app-brand-link gap-2">
                                <img :src="rutaImagen" alt="" height="150px" />
                                <span class="app-brand-link-text"></span>
                            </a>
                        </div>

                        <form class="mb-3" @submit.prevent="ingresar">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input v-model="email" type="email" class="form-control" placeholder="@" autofocus required/>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Contraseña</label>
                                    <a href="">
                                    <small>Olvidate tu contraseña?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input :type="mostrarPassword ? 'text' : 'password'" v-model="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required/>
                                    <span class="input-group-text cursor-pointer" @click="togglePasswordVisibility">
                                        <i :class="mostrarPassword ? 'bx bx-show' : 'bx bx-hide'"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input v-model="rememberMe" class="form-check-input" type="checkbox" id="remember-me" />
                                    <label class="form-check-label" for="remember-me"> Recordar </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Iniciar Sesión</button>
                            </div>
                        </form>

                        <div class="mb-3">
                            <button class="btn btn-secondary d-grid w-100" @click="regresar">Regresar</button>
                        </div>

                        <p class="text-center">
                            <span>¿No tienes usuario? </span>
                            <a href="/register">
                            <span>Registrate</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <loader v-if="cargandoCount > 0" />

</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";
import loader from '../../components/loader.vue';
import "sweetalert2/dist/sweetalert2.min.css";

export default {

    components: {
        loader,
    },

    data() {
        return {
            cargandoCount: 0,
            rutaImagen: "",
            email: "",
            password: "",
            mostrarPassword: false,
            rememberMe: false,
            proyectoId: 0,
        };

    },

    computed: {

    },

    created() {
        this.obtenerImagenes();
        this.cargarEmailRecordado();
    },

    methods: {

        incrementarCarga() {
            this.cargandoCount++;
        },

        decrementarCarga() {
            if (this.cargandoCount > 0) {
                this.cargandoCount--;
            }
        },

        regresar() {
            window.location.href = '/';
        },

        togglePasswordVisibility() {
            this.mostrarPassword = !this.mostrarPassword;
        },

        ingresar() {
            this.incrementarCarga();
            const urlParams = new URLSearchParams(window.location.search);
            this.proyectoId = urlParams.get("proyectoId");
            if(this.proyectoId != null){
                axios.post('/login/evaluacion', {
                    email: this.email,
                    password: this.password,
                    idProyecto: this.proyectoId,
                })
                .then(response => {
                    if (this.rememberMe) {
                        localStorage.setItem('recordarEmail', this.email);
                    } else {
                        localStorage.removeItem('recordarEmail');
                    }
                    window.location.href = response.data.redirect;
                })
                .catch(error => {
                    const messages = error.response.data.messages;
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error en el Inicio de Sesión!',
                        html: messages.join('<br>'),
                    });
                })
                .finally(() => {
                    this.decrementarCarga();
                });
            } else {
                axios.post('/login', {
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    if (this.rememberMe) {
                        localStorage.setItem('recordarEmail', this.email);
                    } else {
                        localStorage.removeItem('recordarEmail');
                    }
                    window.location.href = response.data.redirect;
                })
                .catch(error => {
                    const messages = error.response.data.messages;
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error en el Inicio de Sesión!',
                        html: messages.join('<br>'),
                    });
                })
                .finally(() => {
                    this.decrementarCarga();
                });
            }
        },

        obtenerImagenes(){
            this.incrementarCarga();
            axios
                .get("/Obtenerimagenes/")
                .then((response) => {
                    if (response.data.length > 0) {
                        const imagenLogo = response.data.find(imagen => imagen.tipo === "logo" && imagen.estado === 1);
                        if (imagenLogo) {
                            this.rutaImagen = imagenLogo.ruta;
                        }
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al agregar el Integrante.', 'error');
                }
                })
                .finally(() => {
                    this.decrementarCarga();
                });;
        },

        cargarEmailRecordado() {
            const recordarEmail = localStorage.getItem('recordarEmail');
            if (recordarEmail) {
                this.email = recordarEmail;
                this.rememberMe = true;
            }
        },

        limpiarval(){
            this.name = "",
            this.email = ""
        }
    },

};
</script>
