<template>
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Utilidades /
                </a>
                <span> Respaldos </span>
            </h4>

            <div class="card">
                <h5 class="card-header">Gestión de Respaldos</h5>

                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="nav-item d-flex align-items-center w-75">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input v-model="filtro" type="text" class="form-control border-0 shadow-none" placeholder="Buscar..." aria-label="Buscar..."/>
                    </div>
                    <button type="button" class="btn btn-dark" @click="nuevo">Crear +</button>
                </div>

                <div class="table-responsive text-nowrap">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Descripción</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creación</th>
                            <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-if="paginatedRespaldos.length === 0">
                                <td colspan="5" class="text-center">Sin Información</td>
                            </tr>
                            <tr v-for="(respaldo, index) in paginatedRespaldos" :key="respaldo.id">
                                <td>{{ (pagina - 1) * registrosPorPagina + index + 1 }}</td>
                                <td>{{ respaldo.nombre }}</td>
                                <td>{{ respaldo.size }} MB</td>
                                <td>{{ formatDate(respaldo.created_at) }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);" @click="descargar(respaldo.id)">
                                                <i class="bx bx-edit-alt me-2"></i> Descargar
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" @click="eliminar(respaldo.id)">
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
                <div id="modalPassword" class="modal fade" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Respaldo</h5>
                                <button type="button" class="btn-close" @click="cerrarModalPassword" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Ingresa la contraseña</strong></label>
                                            <input v-model="password" type="password" class="form-control" placeholder="***">
                                            <div v-if="!password" class="text-danger">Este campo es obligatorio</div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" @click="cerrarModalPassword">Cerrar</button>
                                <button v-if="bandera === 0" type="button" class="btn btn-primary" @click="realizarRespaldo">Crear</button>
                                <button v-if="bandera === 1" type="button" class="btn btn-primary" @click="descargarRespaldo">Descargar</button>
                                <button v-if="bandera === 2" type="button" class="btn btn-primary" @click="eliminarRespaldo">Eliminar</button>
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

                    <li class="page-item" :class="{ disabled: pagina === totalPaginas }">
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

export default {

    data() {

        return {
            filtro: '',
            respaldos: [],
            pagina: 1,
            totalPaginas: 0,
            registrosPorPagina: 7,
            bandera: '',
            password: '',
            idRespaldoParaDescargar: null,
        };
    },

    created() {
        this.obtenerRespaldos();
    },

    computed: {

        respaldosFiltrados() {
            const filtroMinusculas = this.filtro.toLowerCase();
            return this.respaldos.filter((respaldo) => {
                const descripcion = `${respaldo.nombre}`;
                const descripcionsinacentos = descripcion.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
                const peso = `${respaldo.size}`;
                const fechaFormateada = this.formatDate(respaldo.created_at).toLowerCase();

                this.pagina = 1;

                return (
                    descripcion.toLowerCase().includes(filtroMinusculas) ||
                    descripcionsinacentos.includes(filtroMinusculas) ||
                    peso.includes(filtroMinusculas) ||
                    fechaFormateada.includes(filtroMinusculas)
                );
            });
        },

        paginatedRespaldos() {
            const start = (this.pagina - 1) * this.registrosPorPagina;
            const end = start + this.registrosPorPagina;
            return this.respaldosFiltrados.slice(start, end);
        },

        totalPages() {
            return Math.ceil(this.respaldosFiltrados.length / this.registrosPorPagina);
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



        obtenerRespaldos() {
            axios.get('/obtenerRespaldos')
                .then((response) => {
                    if (response.data.respaldo) {
                        this.respaldos = response.data.respaldo;
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
            this.abrirModalPassword();
        },

        abrirModalPassword() {
            $("#modalPassword").modal({ backdrop: "static", keyboard: false });
            $("#modalPassword").modal("toggle");
        },

        cerrarModalPassword() {
            $("#modalPassword").modal("hide");
        },

        realizarRespaldo() {
            axios.post('/confirmarPassword', { password: this.password })
                .then(response => {
                    if (response.data.success) {
                        //Swal.fire('Éxito', response.data.message, 'success');
                        this.cerrarModalPassword();
                        this.limpiarvar();

                        axios.post('/realizarRespaldo')
                            .then(respaldoResponse => {
                                if (respaldoResponse.data.success) {
                                    Swal.fire('Éxito', respaldoResponse.data.message, 'success');
                                    this.obtenerRespaldos();
                                } else {
                                    Swal.fire('Error', respaldoResponse.data.message, 'error');
                                }
                            })
                            .catch(error => {
                                if (error.response && error.response.data && error.response.data.message) {
                                    Swal.fire('Error', error.response.data.message, 'error');
                                } else {
                                    Swal.fire('Error', 'Se produjo un error al realizar el respaldo.', 'error');
                                }
                            });
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                        this.password = null;
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                        this.limpiarvar();
                    } else {
                        Swal.fire('Error', 'Se produjo un error al confirmar el password.', 'error');
                    }
                });
        },

        descargar(id) {
            this.idRespaldoParaDescargar = id;
            this.limpiarvar();
            this.bandera = 1;
            this.abrirModalPassword();
        },

        descargarRespaldo() {
            axios.post('/confirmarPassword', { password: this.password })
                .then(response => {
                    if (response.data.success) {
                        this.cerrarModalPassword();
                        this.limpiarvar();

                        axios({
                            url: '/descargarRespaldo/' + this.idRespaldoParaDescargar,
                            method: 'POST',
                            responseType: 'blob',
                        }).then(res => {
                            const url = window.URL.createObjectURL(new Blob([res.data]));
                            const link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', 'respaldo_completo.zip');
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            window.URL.revokeObjectURL(url);

                            Swal.fire({
                                title: 'Éxito',
                                text: 'El respaldo se descargó correctamente.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                this.obtenerRespaldos();
                            });
                        }).catch(error => {
                            Swal.fire('Error', 'Se produjo un error al descargar el respaldo.', 'error');
                        });
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                        this.password = null;
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                        this.limpiarvar();
                    } else {
                        Swal.fire('Error', 'Se produjo un error al confirmar el password.', 'error');
                    }
                });
        },

        eliminar(id) {
            this.idRespaldoParaDescargar = id;
            this.limpiarvar();
            this.bandera = 2;
            this.abrirModalPassword();
        },

        eliminarRespaldo() {
            axios.post('/confirmarPassword', { password: this.password })
                .then(response => {
                    if (response.data.success) {
                        this.cerrarModalPassword();
                        this.limpiarvar();

                        Swal.fire({
                            title: '¿Estás seguro de que deseas eliminar este Respaldo?',
                            text: "¡No se podrá revertir dicha acción!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí',
                            cancelButtonText: 'No'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.delete("/eliminarRespaldo/" + this.idRespaldoParaDescargar)
                                    .then(response => {
                                        Swal.fire('Éxito', response.data.message, 'success');
                                        this.obtenerRespaldos();
                                    })
                                    .catch(error => {
                                        this.cerrarModalPassword();
                                        if (error.response && error.response.data && error.response.data.message) {
                                            Swal.fire('Error', error.response.data.message, 'error');
                                        } else {
                                            Swal.fire('Error', 'Se produjo un error al eliminar el respaldo.', 'error');
                                        }
                                    });
                            }
                        });
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                        this.password = null;
                    }
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                        this.limpiarvar();
                    } else {
                        Swal.fire('Error', 'Se produjo un error al confirmar el password.', 'error');
                    }
                });
        },

        limpiarvar() {
            this.password = null;
        },

        calcularTotalPaginas() {
            this.totalPaginas = Math.ceil(this.respaldos.length / this.registrosPorPagina);
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

        formatDate(dateString) {
            const date = new Date(dateString);

            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };

            return new Intl.DateTimeFormat('es-ES', options).format(date);
        }

    }
};
</script>
