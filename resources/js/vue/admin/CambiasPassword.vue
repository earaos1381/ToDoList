
<template>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Administración /
                </a>
                <span> Perfil</span>
            </h4>

            <div class="row">
                <!-- Formulario para actualizar nombre y email -->
                <div class="col-md-6">
                    <div class="card">
                        <h5 class="card-header">Actualizar Información</h5>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">Nombre:</label>
                                <input v-model="name" type="text" class="form-control" required />
                                <div v-if="!name" class="text-danger">Este campo es obligatorio.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Correo Electrónico:</label>
                                <input v-model="email" type="email" class="form-control" required />
                                <div v-if="!email" class="text-danger">Este campo es obligatorio.</div>
                            </div>
                            <button class="btn btn-primary" @click="actualizarInfoUser">Actualizar Información</button>
                        </div>
                    </div>
                </div>

                <!-- Formulario para cambiar contraseña -->
                <div class="col-md-6">
                    <div class="card">
                        <h5 class="card-header">Cambiar Contraseña</h5>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="currentPassword">Contraseña Actual:</label>
                                <input v-model="currentPassword" type="password" class="form-control" required />
                                <div v-if="!currentPassword" class="text-danger">Este campo es obligatorio.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="newPassword">Nueva Contraseña:</label>
                                <input v-model="newPassword" type="password" class="form-control" required />
                                <div v-if="!newPassword" class="text-danger">Este campo es obligatorio.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="confirmPassword">Confirmar Nueva Contraseña:</label>
                                <input v-model="confirmPassword" type="password" class="form-control" required />
                                <div v-if="!confirmPassword" class="text-danger">Este campo es obligatorio.</div>
                            </div>
                            <button class="btn btn-primary" @click="cambiarPassword">Cambiar Contraseña</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <loader v-if="cargandoCount > 0" />
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import loader from '../../components/loader.vue';

export default {

    components: {
        loader,
    },

    data() {

        return {
            cargandoCount: 0,
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            name: '', 
            email: '',
        };
    },

    created() {
        this.obtenerInfoUser();
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

        obtenerInfoUser() {
            this.incrementarCarga();
            axios
                .get("/obtenerInfoUser")
                .then((response) => {
                    if (response.data.success) {
                        this.name = response.data.user.name;
                        this.email = response.data.user.email;
                    } else {
                        Swal.fire("Error", "No se pudieron cargar los datos del usuario.", "error");
                    }
                })
                .catch(() => {
                    Swal.fire("Error", "Se produjo un error al obtener los datos.", "error");
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        cambiarPassword() {
            this.incrementarCarga();

            const password = {
                currentPassword: this.currentPassword,
                newPassword: this.newPassword,
                confirmPassword: this.confirmPassword,
            };

            axios.post('/actualizarPassword', password)
                .then(response => {
                    if (response.data.success) {
                        this.limpiarvar();
                        Swal.fire({
                            title: 'Éxito',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.post('/logout')
                                .then(() => {
                                    window.location.href = '/';
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Se produjo un error al cerrar sesión.', 'error');
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al agregar un usuario.', 'error');
                    }
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        actualizarInfoUser() {
            this.incrementarCarga();
            const user = {
                name: this.name,
                email: this.email,
            };
            axios.post('/actualizarInfoUser', user)
                .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('/logout')
                                .then(() => {
                                    window.location.href = '/';
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Se produjo un error al cerrar sesión.', 'error');
                                });
                        }
                    });
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            })
            .catch(error => {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al actualizar el perfil.', 'error');
                }
            })
            .finally(() => {
                this.decrementarCarga();
            });
        },

        limpiarvar() {
            this.currentPassword = null;
            this.newPassword = null;
            this.confirmPassword = null;
        },

    },
};

</script>

<style scoped>
  .btn-primary {
    color: #fff;
    background-color: #440412;
    border-color: #440412;
  }
</style>
