<template>
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Utilidades / Roles y Permisos /
                </a>
                <span> Permisos</span>
            </h4>

            <div class="card">
                <h5 class="card-header">Permisos de Usuario</h5>

                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="nav-item d-flex align-items-center w-75">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input v-model="filtro" type="text" class="form-control border-0 shadow-none" placeholder="Buscar..." aria-label="Buscar..."/>
                    </div>
                    <button type="button" class="btn btn-dark" @click="nuevo">Agregar</button>
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
                            <tr v-if="paginatedPermisos.length === 0">
                                <td colspan="3" class="text-center">Sin Información</td>
                            </tr>
                            <tr v-for="(permiso, index) in paginatedPermisos" :key="permiso.id">
                                <td>{{ (pagina - 1) * registrosPorPagina + index + 1 }}</td>
                                <td>{{ permiso.name }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a  class="dropdown-item" href="javascript:void(0);" @click="datallePermiso(permiso.id)">
                                                <i class="bx bx-edit-alt me-2"></i> Editar
                                            </a>
                                            <a  class="dropdown-item" href="javascript:void(0);" @click="eliminarPermiso(permiso.id)">
                                                <i class="bx bx-trash me-2"></i> Eliminar
                                            </a>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>


                <!-- Agregamos el modal -->
                <div id="modal" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Permisos</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"> <strong>Descripción del Permiso:</strong></label>
                                            <input v-model="name" type="text" class="form-control" placeholder="Nombre">
                                            <div v-if="!name" class="text-danger">Este campo es obligatorio</div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button v-if="bandera === 0" type="button" class="btn btn-primary" @click="agregarPermiso">Guardar</button>
                                <button v-if="bandera === 1" type="button" class="btn btn-primary" @click="editarPermiso">Editar</button>
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
            permisos: [],
            pagina: 1,
            totalPaginas: 0,
            registrosPorPagina: 7,
            bandera: "",
            name: "",
            idper: "",

        };
    },

    created() {
        this.obtenerPermisos();


    },

    computed: {

        permisosFiltrados() {
            const filtroMinusculas = this.filtro.toLowerCase();
            return this.permisos.filter((permisos) => {
                const nombreCompleto = `${permisos.name}`;
                const nombreSinAcentos = nombreCompleto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                this.pagina = 1;

                return (
                    nombreCompleto.toLowerCase().includes(filtroMinusculas) ||
                    nombreSinAcentos.includes(filtroMinusculas)
                );
            });
        },

        paginatedPermisos() {
            const start = (this.pagina - 1) * this.registrosPorPagina;
            const end = start + this.registrosPorPagina;
            return this.permisosFiltrados.slice(start, end);
        },

        totalPages() {
            return Math.ceil(this.permisosFiltrados.length / this.registrosPorPagina);
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

        hasPermission(permiso) {
            return this.permisosGranted.includes(permiso);
        },


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

        nuevo() {
            this.limpiarvar();
            this.bandera = 0;
            this.abrirModal();
        },

        agregarPermiso() {
            axios.post('/Permission/agregarPermisos', { name: this.name })
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.cerrarModal();
                        this.obtenerPermisos();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al agregar el Permiso.', 'error');
                    }
                });
        },

        datallePermiso(idPerm) {

        this.idper = idPerm;
        this.bandera = 1;
        this.abrirModal();

        axios.get("/Permission/detallePermiso/" + idPerm)
            .then((response) => {
                const permiso = response.data[0];
                this.name = permiso.name;

            })
            .catch((error) => {
                this.cerrarModal();
                Swal.fire('Error', error.response.data.message, 'error');
            });
        },

        editarPermiso() {
            var idPerm = this.idper;
            this.cerrarModal();

            let data = {
                id: idPerm,
                name: this.name,
            };

            axios.put(`/Permission/editarPermiso`, data)
                .then((response) => {
                    if (response.data.success) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.limpiarvar();
                        this.cerrarModal();
                        this.obtenerPermisos();
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                })
                .catch((error) => {
                    this.cerrarModal();
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al editar el Permiso.', 'error');
                    }
                });

        },

        eliminarPermiso(idPerm){
            Swal.fire({
                title: '¿Estás seguro de que deseas eliminar este Permiso?',
                text: "No se podra revertir dicha acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No'

                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete("/Permission/eliminarPermiso/" + idPerm)
                        .then(response => {

                            Swal.fire('Éxito', response.data.message, 'success');
                            this.obtenerPermisos();
                        })
                        .catch((error) => {
                            this.cerrarModal();
                            if (error.response && error.response.data && error.response.data.message) {
                                Swal.fire('Error', error.response.data.message, 'error');
                            } else {
                                Swal.fire('Error', error.response.data.message, 'error');
                            }
                    });
                }
            })
        },

        limpiarvar() {
            this.name = null;
        },

        abrirModal() {
            $("#modal").modal({ backdrop: "static", keyboard: false });
            $("#modal").modal("toggle");
        },

        cerrarModal() {
            $("#modal").modal("hide");
        },

        calcularTotalPaginas() {
            this.totalPaginas = Math.ceil(this.permisos.length / this.registrosPorPagina);
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

    }
};
</script>
