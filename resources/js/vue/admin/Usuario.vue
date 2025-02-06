<template>
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Administración /
                </a>
                <span> Usuarios</span>
            </h4>

            <div class="card">
                <h5 class="card-header">Gestion de Usuario</h5>

                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="nav-item d-flex align-items-center w-75">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input v-model="filtro" type="text" class="form-control border-0 shadow-none" placeholder="Buscar..." aria-label="Buscar..."/>
                    </div>
                    <button v-if="hasPermission('Agregar_Usuario')" type="button" class="btn btn-dark" @click="nuevo">Agregar</button>
                </div>

                <div class="table-responsive text-nowrap">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-if="paginatedUsers.length === 0">
                                <td colspan="5" class="text-center">Sin Información</td>
                            </tr>
                            <tr v-for="(user, index) in paginatedUsers" :key="user.id">
                            <td>{{ (pagina - 1) * registrosPorPagina + index + 1 }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.roles.join(', ') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a v-if="hasPermission('Editar_Usuario')" class="dropdown-item" href="javascript:void(0);" @click="detalleUsuario(user.id)">
                                                <i class="bx bx-edit-alt me-2"></i> Editar
                                            </a>
                                            <a v-if="hasPermission('Eliminar_Usuario')" class="dropdown-item" href="javascript:void(0);" @click="eliminarUsuario(user.id)">
                                                <i class="bx bx-trash me-2"></i> Eliminar
                                            </a>
                                            <a v-if="hasPermission('Asignar_Rol')" class="dropdown-item" href="javascript:void(0);" @click="detalleRol(user.id)">
                                                <i class="bx bx-user-check me-2"></i> Asignar Rol
                                            </a>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- Modal Usuario -->
                <div id="modalusuario" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Usuarios</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"> <strong>Nombre Completo:</strong></label>
                                            <input v-model="name" type="text" class="form-control" placeholder="Nombre">
                                            <div v-if="!name" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"> <strong>Correo Electrónico:</strong></label>
                                            <input v-model="email" type="text" class="form-control" placeholder="@">
                                            <div v-if="!email" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"> <strong>Contraseña:</strong></label>
                                            <div class="input-group input-group-merge">
                                                <input :type="mostrarPassword ? 'text' : 'password'" v-model="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required/>
                                                <span class="input-group-text cursor-pointer" @click="togglePasswordVisibility">
                                                    <i :class="mostrarPassword ? 'bx bx-show' : 'bx bx-hide'"></i>
                                                </span>
                                            </div>
                                            <div v-if="!password" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button v-if="bandera === 0" type="button" class="btn btn-primary" @click="agregarUsuario">Guardar</button>
                                <button v-if="bandera === 1" type="button" class="btn btn-primary" @click="editarUsuario">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin del modal -->

                <!-- Modal Rol-->
                <div id="modalrol" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Rol</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Selecciona el rol:</label>
                                        <div v-for="role in roles" :key="role.id" class="form-check">
                                            <input type="checkbox" class="form-check-input" :id="'role-' + role.id" v-model="selectedRoles" :value="role.id"/>
                                            <!-- <input type="radio" class="form-check-input" :id="'role-' + role.id" v-model="selectedRoles" :value="role.id"/> -->
                                            <label class="form-check-label" :for="'role-' + role.id">{{ role.name }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button v-if="bandera === 3" type="button" class="btn btn-primary" @click="asignarRoles">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin del modal -->


                <!-- Agregamos el paginador -->
                <ul class="pagination justify-content-center">
                    <li class="page-item" :class="{ disabled: pagina === 1 }">
                        <a class="page-link" href="javascript:void(0);" @click="paginaAnterior"><i class="tf-icon bx bx-chevrons-left"></i></a>
                    </li>
                    <li class="page-item" v-if="paginasVisibles[0] !== 1">
                        <a class="page-link" href="javascript:void(0);" @click="cambiarPagina(1)">1</a>
                    </li>
                    <li class="page-item disabled" v-if="paginasVisibles[0] > 2">
                        <a class="page-link" href="javascript:void(0);">...</a>
                    </li>

                    <li class="page-item" v-for="numero in paginasVisibles" :key="numero" :class="{ active: numero === pagina }">
                        <a class="page-link" href="javascript:void(0);" @click="cambiarPagina(numero)">{{ numero }}</a>
                    </li>

                    <li class="page-item disabled" v-if="paginasVisibles[paginasVisibles.length - 1] < totalPages - 1">
                        <a class="page-link" href="javascript:void(0);">...</a>
                    </li>
                    <li class="page-item" v-if="paginasVisibles[paginasVisibles.length - 1] !== totalPages">
                        <a class="page-link" href="javascript:void(0);" @click="cambiarPagina(totalPages)">{{ totalPages }}</a>
                    </li>

                    <li class="page-item" :class="{ disabled: pagina === totalPages }">
                        <a class="page-link" href="javascript:void(0);" @click="paginaSiguiente"><i class="tf-icon bx bx-chevrons-right"></i></a>
                    </li>
                </ul>
                <!-- Fin del paginador -->

            </div>

        </div>
    </div>

    <loader v-if="cargandoCount > 0" />
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref, reactive } from 'vue';
import loader from '../../components/loader.vue';

export default {

    components: {
        loader,
    },

    props: {
        rolesGranted: {
            type: Array,
            default: () => []
        },
        permisosGranted: {
            type: Array,
            default: () => []
        },
    },


    data() {

        return {
            cargandoCount: 0,
            filtro: '',
            users: [],
            roles: [],
            selectedRoles: [],
            userRoles: {},
            pagina: 1,
            totalPaginas: 0,
            registrosPorPagina: 7,
            bandera: "",
            name: "",
            email: "",
            password: "",
            mostrarPassword: false,
            iduse: "",
            idro: "",
        };
    },

    created() {
        this.obtenerUsuarios();
    },

    computed: {

        usuariosFiltrados() {
            const filtroMinusculas = this.filtro.toLowerCase();
            return this.users.filter((usuarios) => {
                const nombreCompleto = `${usuarios.name}`;
                const nombreSinAcentos = nombreCompleto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                const rolesCoinciden = usuarios.roles.some((rol) =>
                    rol.toLowerCase().includes(filtroMinusculas)
                );
                this.pagina = 1;

                return (
                    nombreCompleto.toLowerCase().includes(filtroMinusculas) ||
                    nombreSinAcentos.includes(filtroMinusculas) ||
                    usuarios.email.toLowerCase().includes(filtroMinusculas) ||
                    rolesCoinciden
                );
            });
        },

        paginatedUsers() {
            const start = (this.pagina - 1) * this.registrosPorPagina;
            const end = start + this.registrosPorPagina;
            return this.usuariosFiltrados.slice(start, end);
        },

        totalPages() {
            return Math.ceil(this.usuariosFiltrados.length / this.registrosPorPagina);
        },

        paginasVisibles() {
            const maxPaginasVisibles = 10;
            let startPage = Math.max(1, this.pagina - Math.floor(maxPaginasVisibles / 2));
            let endPage = startPage + maxPaginasVisibles - 1;

            if (endPage > this.totalPages) {
                endPage = this.totalPages;
                startPage = Math.max(1, endPage - maxPaginasVisibles + 1);
            }

            const pages = [];
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }

            return pages;
        },

    },

    mounted() {
        this.calcularTotalPaginas();
    },


    methods: {

        /* Método para gestionar permisos */
        hasPermission(permiso) {
            return this.permisosGranted.includes(permiso);
        },


        /* Método para componente de carga */
        incrementarCarga() {
            this.cargandoCount++;
        },

        decrementarCarga() {
            if (this.cargandoCount > 0) {
                this.cargandoCount--;
            }
        },


        /* Método para paginación */
        calcularTotalPaginas() {
            this.totalPaginas = Math.ceil(this.users.length / this.registrosPorPagina);
        },

        cambiarPagina(numero) {
            this.pagina = numero;
        },

        paginaAnterior() {
            if (this.pagina > 1) {
                this.pagina--;
            }
        },

        paginaSiguiente() {
            if (this.pagina < this.totalPaginas) {
                this.pagina++;
            }
        },


        /* Resto de Método */

        obtenerUsuarios() {
            this.incrementarCarga();
            axios.get('/Users/get')
                .then((response) => {
                    if (response.data.data) {
                        this.users = response.data.data;
                        this.calcularTotalPaginas();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                    }
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        togglePasswordVisibility() {
            this.mostrarPassword = !this.mostrarPassword;
        },

        agregarUsuario() {
            this.incrementarCarga();
            const nuevoUsuario = {
                name: this.name,
                email: this.email,
                password: this.password,
            };

            axios.post('/Users/agregarUsuario', nuevoUsuario)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.cerrarModalUsuario();
                        this.obtenerUsuarios();
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

        detalleUsuario(id) {
            this.incrementarCarga();
            this.iduse = id;
            this.bandera = 1;
            this.abrirModalUsuario();

            axios.get("/Users/detalleUsuario/" + id)
                .then((response) => {
                    const user = response.data;
                    this.name = user.name;
                    this.email = user.email;
                    this.password = user.password;

                })
                .catch((error) => {
                    Swal.fire('Error', error.response.data.message, 'error');
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        editarUsuario(id) {
            this.incrementarCarga();
            var id = this.iduse;

            let data = {
                id: id,
                name: this.name,
                email: this.email,
                password: this.password,
            };

            axios.put(`/Users/editarUsuario`, data)
                .then((response) => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.limpiarvar();
                        this.cerrarModalUsuario();
                        this.obtenerUsuarios();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al editar el Usuario.', 'error');
                    }
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        eliminarUsuario(id) {

            Swal.fire({
                title: '¿Estás seguro de que deseas eliminar este Usuario?',
                text: "No se podra revertir dicha acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No'

                }).then((result) => {
                    if (result.isConfirmed) {
                        this.incrementarCarga();
                        axios.delete("/Users/eliminarUsuario/" + id)
                        .then(response => {
                            Swal.fire('Éxito', response.data.message, 'success');
                            this.obtenerUsuarios();
                        })
                        .catch((error) => {
                            if (error.response && error.response.data && error.response.data.message) {
                                Swal.fire('Error', error.response.data.message, 'error');
                            } else {
                                Swal.fire('Error', error.response.data.message, 'error');
                            }
                    })
                    .finally(() => {
                        this.decrementarCarga();
                    });
                }
            })
        },

        abrirModalUsuario() {
            $("#modalusuario").modal({ backdrop: "static", keyboard: false });
            $("#modalusuario").modal("toggle");
        },

        cerrarModalUsuario() {
            $("#modalusuario").modal("hide");
        },


        /* Metodos de Roles */
        detalleRol(idRol) {
            this.incrementarCarga();
            this.idro = idRol;
            this.bandera = 3;
            this.idRolesSeleccionado = idRol;
            this.abrirModalRoles();

        axios.get(`/Users/obtenerRolesUsuario/${this.idro}`)
                .then((response) => {
                const rolesAsignados = response.data.roles;

                const rolesArray = Array.isArray(rolesAsignados)
                    ? rolesAsignados
                    : Object.values(rolesAsignados);

                if (!this.userRoles[this.idro]) {
                    this.userRoles[this.idro] = ref(reactive([]));
                }

                this.userRoles[this.idro].value = rolesArray;
                this.selectedRoles = rolesArray.map(role => role.id);
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                            Swal.fire('Error', error.response.data.message, 'error');
                        } else {
                            Swal.fire('Error', 'Se produjo un error al obtener los roles.', 'error');
                        }
                })
                .finally(() => {
                    this.decrementarCarga();
                });

                this.incrementarCarga();

        axios.get('/Roles/obtenerRoles')
            .then((response) => {
                    if (response.data.role) {
                        this.roles = response.data.role;

                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                            Swal.fire('Error', error.response.data.message, 'error');
                        } else {
                            Swal.fire('Error', 'Se produjo un error al obtener los roles.', 'error');
                        }
                })
                .finally(() => {
                    this.decrementarCarga();
                });
        },

        asignarRoles() {
            const idUser = this.idRolesSeleccionado;

            axios.post('/Users/asignarRoles', { idUser, selectedRoles: this.selectedRoles })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Éxito', 'Roles asignados correctamente.', 'success');
                        this.cerrarModalRoles();
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

        abrirModalRoles() {
            this.selectedRoles = [];
            $("#modalrol").modal({ backdrop: "static", keyboard: false });
            $("#modalrol").modal("toggle");
        },

        cerrarModalRoles() {
            $("#modalrol").modal("hide");
        },

        /* General */
        nuevo() {
            this.limpiarvar();
            this.bandera = 0;
            this.abrirModalUsuario();
        },

        limpiarvar() {
            this.name = null;
            this.email = null;
            this.password = null;
        },

    }
};
</script>
