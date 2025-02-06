<template>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
            <!-- Register Card -->
            <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a class="app-brand-link gap-2">
                                <img :src="rutaImagen" alt="" height="150px" />
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Registro de Usuario 🚀</h4>
                        <p class="mb-4">Completa la información Correspondiente</p>
                        <form class="mb-3" @submit.prevent="registrarse">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input v-model="name" type="text" class="form-control" placeholder="Nombre" autofocus required/>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="email" type="email" class="form-control" placeholder="@" required/>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input :type="mostrarPassword ? 'text' : 'password'" v-model="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required/>
                                    <span class="input-group-text cursor-pointer" @click="togglePasswordVisibility">
                                        <i :class="mostrarPassword ? 'bx bx-show' : 'bx bx-hide'"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label">Confirmar Password</label>
                                <div class="input-group input-group-merge">
                                    <input :type="mostrarPasswordConfirm ? 'text' : 'password'" v-model="password_confirmation" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required/>
                                    <span class="input-group-text cursor-pointer" @click="togglePasswordConfirmVisibility">
                                        <i :class="mostrarPasswordConfirm ? 'bx bx-show' : 'bx bx-hide'"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" required />
                                    <label class="form-check-label" for="terms-conditions">
                                        Acepto las
                                        <a href="javascript:void(0);">políticas y términos de privacidad</a>
                                    </label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100" type="submit">Registrarse</button>
                        </form>
                        <p class="text-center">
                            <span>¿Cuentas con Usuario? </span>
                            <a href="/login">
                                <span>Inicia Sesión</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

export default {
    data() {

        return {
            rutaImagen: "",
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            mostrarPassword: false,
            mostrarPasswordConfirm: false
        };
    },

    created() {
        this.obtenerImagenes();
    },

    methods: {

        irAIniciarSesion() {
            window.location.href = '/login';
        },

        togglePasswordVisibility() {
            this.mostrarPassword = !this.mostrarPassword;
        },

        togglePasswordConfirmVisibility() {
            this.mostrarPasswordConfirm = !this.mostrarPasswordConfirm;
        },

        registrarse() {

            if (this.password !== this.password_confirmation) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Las contraseñas no coinciden'
                });
                return;
            }

            axios.post('/register', {
                name: this.name,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation
            })
            
            .then(response => {

                this.limpiarval();
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro Exitoso!',
                    text: 'Usuario registrado correctamente',
                }).then(() => {
                    window.location.href = response.data.redirect;
                });
            })
            .catch(error => {
                const messages = error.response.data.messages;
                Swal.fire({
                    icon: 'error',
                    title: '¡Error en el Registro!',
                    html: messages.join('<br>'),
                });
            });
        },

        obtenerImagenes(){
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
                    console.error(error);
                });
        },

        limpiarval(){
            this.name = "",
            this.email = "",
            this.password = "",
            this.password_confirmation = ""
        }
    },
};
</script>
