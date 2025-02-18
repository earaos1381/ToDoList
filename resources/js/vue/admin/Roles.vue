<template>
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Utilidades / Roles y Permisos /
                </a>
                <span> Roles</span>
            </h4>

            <div class="card">
                <h5 class="card-header">Roles de Usuario</h5>

                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="nav-item d-flex align-items-center w-75">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input v-model="filtro" type="text" class="form-control border-0 shadow-none" placeholder="Buscar..." aria-label="Buscar..."/>
                    </div>
                    <button v-if="hasPermission('Agregar_Rol')" type="button" class="btn btn-dark" @click="nuevo">Agregar</button>
                </div>

                <div class="table-responsive text-nowrap">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-if="paginatedRoles.length === 0">
                                <td colspan="3" class="text-center">Sin Información</td>
                            </tr>
                            <tr v-for="(role, index) in paginatedRoles" :key="role.id">
                                <td>{{ (pagina - 1) * registrosPorPagina + index + 1 }}</td>
                                <td>{{ role.name }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a v-if="hasPermission('Editar_Rol')" class="dropdown-item" href="javascript:void(0);" @click="detalleRol(role.id)">
                                                <i class="bx bx-edit-alt me-2"></i> Editar
                                            </a>
                                            <a v-if="hasPermission('Eliminar_Rol')" class="dropdown-item" href="javascript:void(0);" @click="eliminarRol(role.id)">
                                                <i class="bx bx-trash me-2"></i> Eliminar
                                            </a>
                                            <a v-if="hasPermission('Asignar_Permisos')" class="dropdown-item" href="javascript:void(0);" @click="detallePermiso(role.id)">
                                                <i class="bx bx-user-check me-2"></i> Asignar Permisos
                                            </a>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- Modal Roles -->
                <div id="modalroles" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Roles</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Descripción del Rol:</strong></label>
                                            <input v-model="name" type="text" class="form-control" placeholder="Nombre">
                                            <div v-if="!name" class="text-danger">Este campo es obligatorio</div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button v-if="bandera === 0" type="button" class="btn btn-primary" @click="agregarRol">Guardar</button>
                                <button v-if="bandera === 1" type="button" class="btn btn-primary" @click="editarRol">Editar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fin del modal -->

                <!-- Modal Permisos-->
                <div id="modalpermisos" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Permisos</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Selecciona los permisos:</strong></label>
                                        <div v-for="permiso in permisos.slice(0, Math.ceil(permisos.length / 2))" :key="permiso.id" class="form-check">
                                            <input type="checkbox" class="form-check-input" :id="'permiso-' + permiso.id" v-model="selectedPermisos" :value="permiso.id"/>
                                            <label class="form-check-label" :for="'permiso-' + permiso.id">{{ permiso.name }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label>&nbsp;</label>
                                        <div v-for="permiso in permisos.slice(Math.ceil(permisos.length / 2))" :key="permiso.id" class="form-check">
                                            <input type="checkbox" class="form-check-input" :id="'permiso-' + permiso.id" v-model="selectedPermisos" :value="permiso.id"/>
                                            <label class="form-check-label" :for="'permiso-' + permiso.id">{{ permiso.name }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button v-if="bandera === 3" type="button" class="btn btn-primary" @click="asignarPermiso">Guardar</button>
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
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import loader from '../../components/loader.vue';
import { ref, reactive } from 'vue';

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
            filtro: '',
            rolesM: [],
            permisos: [],
            selectedPermisos: [],
            userPermissions: {},
            pagina: 1,
            totalPaginas: 0,
            registrosPorPagina: 7,
            bandera: "",
            name: "",
            idro: "",
            idpe: "",
        };
    },

    created() {
        this.obtenerRoles();
        this.obtenerPermisos();
    },

    computed: {

        usuariosRoles() {
            const filtroMinusculas = this.filtro.toLowerCase();
            return this.rolesM.filter((rolesM) => {
                const nombreCompleto = `${rolesM.name}`;
                const nombreSinAcentos = nombreCompleto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                this.pagina = 1;

                return (
                    nombreCompleto.toLowerCase().includes(filtroMinusculas) ||
                    nombreSinAcentos.includes(filtroMinusculas)
                );
            });
        },

        paginatedRoles() {
            const start = (this.pagina - 1) * this.registrosPorPagina;
            const end = start + this.registrosPorPagina;
            return this.usuariosRoles.slice(start, end);
        },

        totalPages() {
            return Math.ceil(this.usuariosRoles.length / this.registrosPorPagina);
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
            this.totalPaginas = Math.ceil(this.rolesM.length / this.registrosPorPagina);
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


        obtenerPermisos() {
            axios.get('/Permission/obtenerPermisos')
                .then((response) => {
                    if (response.data.permiso) {
                        this.permisos = response.data.permiso;
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
                });
        },

        /* Metodos de Roles */
        obtenerRoles() {
            axios.get('/Roles/obtenerRoles')
                .then((response) => {
                    if (response.data.role) {
                        this.rolesM = response.data.role;
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
                });
        },

        agregarRol() {
            axios.post('/Roles/agregarRol', { name: this.name })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.cerrarModalRol();
                        this.obtenerRoles();
                    } else {
                        this.cerrarModalRol();
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al agregar el Rol.', 'error');
                    }
                });
        },

        detalleRol(idRol) {

        this.idro = idRol;
        this.bandera = 1;
        this.abrirModalRol();

        axios.get("/Roles/detalleRol/" + idRol)
            .then((response) => {
                const evento = response.data[0];
                this.name = evento.name;

            })
            .catch((error) => {
                this.cerrarModalRol();
                Swal.fire('Error', error.response.data.message, 'error');
            });
        },

        editarRol() {
            var idRol = this.idro;
            this.cerrarModalRol();

            let data = {
                id: idRol,
                name: this.name,
            };

            axios.put(`/Roles/editarRol`, data)
                .then((response) => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.limpiarvar();
                        this.cerrarModalRol();
                        this.obtenerRoles();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al editar el Rol.', 'error');
                    }
                });

        },

        eliminarRol(idRol){
            Swal.fire({
                title: '¿Estás seguro de que deseas eliminar este Rol?',
                text: "No se podra revertir dicha acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No'

                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete("/Roles/eliminarRol/" + idRol)
                        .then(response => {

                            Swal.fire('Éxito', response.data.message, 'success');
                            this.obtenerRoles();
                        })
                        .catch((error) => {
                            this.cerrarModalRol();
                            if (error.response && error.response.data && error.response.data.message) {
                                Swal.fire('Error', error.response.data.message, 'error');
                            } else {
                                Swal.fire('Error', error.response.data.message, 'error');
                            }
                    });
                }
            })
        },

        abrirModalRol() {
            $("#modalroles").modal({ backdrop: "static", keyboard: false });
            $("#modalroles").modal("toggle");
        },

        cerrarModalRol() {
            $("#modalroles").modal("hide");
        },

        /* Metodos de Permisos */
        detallePermiso(idPerm) {

        this.idpe = idPerm;
        this.bandera = 3;
        this.idRolSeleccionado = idPerm;
        this.abrirModalPermiso();

        axios.get(`/Roles/obtenerPermisosRol/${this.idpe}`)
                .then((response) => {
                const permisosAsignados = response.data.permisos;

                const permisosArray = Array.isArray(permisosAsignados)
                    ? permisosAsignados
                    : Object.values(permisosAsignados);

                if (!this.userPermissions[this.idpe]) {
                    this.userPermissions[this.idpe] = ref(reactive([]));
                }

                this.userPermissions[this.idpe].value = permisosArray;
                this.selectedPermisos = permisosArray.map(permiso => permiso.id);
                })
                .catch((error) => {
                    this.cerrarModalPermiso();
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                    }
                });


        axios.get('/Permission/obtenerPermisos')
            .then((response) => {
                    if (response.data.permiso) {
                        this.permisos = response.data.permiso;

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
                });
        },

        asignarPermiso() {
            const idRol = this.idRolSeleccionado;

            axios.post('/Roles/asignarpermisos', { idRol, selectedPermisos: this.selectedPermisos })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.cerrarModalPermiso();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    this.cerrarModalPermiso();
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al agregar el Rol.', 'error');
                    }
                });
        },

        abrirModalPermiso() {
            this.selectedPermisos = [];
            $("#modalpermisos").modal({ backdrop: "static", keyboard: false });
            $("#modalpermisos").modal("toggle");
        },

        cerrarModalPermiso() {
            $("#modalpermisos").modal("hide");
        },


        /* General */
        nuevo() {
            this.limpiarvar();
            this.bandera = 0;
            this.abrirModalRol();
        },

        limpiarvar() {
            this.name = null;
        },



    }
};
</script>
