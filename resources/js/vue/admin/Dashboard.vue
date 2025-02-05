<template>
    <div class="content-wrapper">
            <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <a href="/dashboard" class="text-muted fw-light">
                    Dashboard /
                </a>
                <span> Tareas</span>
            </h4>

            <div class="card">
                <h5 class="card-header">Lista de Tareas</h5>

                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="nav-item d-flex align-items-center w-75">
                        <i class="bx bx-search fs-4 lh-0"></i>
                        <input  type="text" class="form-control border-0 shadow-none" placeholder="Buscar..." aria-label="Buscar..."/>
                    </div>
                    <button type="button" class="btn btn-dark" @click="nuevo">Agregar</button>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th>Nombre de la tarea</th>
                                <th>Descripción</th>
                                <th><i class='bx bx-chevron-up-square'></i> Prioridad</th>
                                <th>Fecha de Creación / Límite</th>
                                <th><i class='bx bx-category-alt'></i> Estatus</th>
                                <th><i class='bx bxs-select-multiple' ></i> Tipo de Tarea</th>
                                <th><i class='bx bx-list-ul'></i> Categoria</th>
                                <th><i class='bx bxs-user-detail' ></i> Asignación</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-if="listTask.length === 0">
                                <td colspan="9" class="text-center">Sin Información</td>
                            </tr>

                            <!-- Iterar sobre las tareas y mostrar cada una en una fila -->
                            <tr v-for="task in listTask" :key="task.id">
                                <td>{{ task.title }}</td>
                                <td class="datos-columna">{{ task.description || 'Sin descripción' }}</td>
                                <td>
                                    <span v-if="task.priority_id"
                                        :class="getPriorityClass(getNameById(priorities, task.priority_id))"
                                        class="px-2 py-1 text-white text-sm rounded">
                                        {{ getNameById(priorities, task.priority_id) }}
                                    </span>
                                    <span v-else class="text-gray-500">Sin prioridad</span>
                                </td>
                                <td class="datos-columna">
                                    {{ formatDate(task.created_at) }}
                                    <br>
                                    {{ formatDate(task.due_date) }}
                                </td>
                                <td>
                                    <span v-if="task.status_id"
                                        :class="['badge', getStatusClass(getNameById(statuses, task.status_id))]">
                                        {{ getNameById(statuses, task.status_id) }}
                                    </span>
                                    <span v-else class="text-muted">Sin estado</span>
                                </td>
                                <td>
                                    <span v-if="task.task_type_id"
                                        :class="['badge', getTaskTypeClass(getNameById(types, task.task_type_id))]">
                                        {{ getNameById(types, task.task_type_id) }}
                                    </span>
                                    <span v-else class="text-muted">No especificado</span>
                                </td>
                                <td>
                                    <span v-if="task.category_id"
                                        :class="['badge', getCategoryClass(getNameById(categories, task.category_id))]">
                                        {{ getNameById(categories, task.category_id) }}
                                    </span>
                                    <span v-else class="text-muted">Sin categoría</span>
                                </td>
                                <td>
                                    <span v-if="task.user_assignments">
                                        <ul>
                                            <li v-for="userId in task.user_assignments.split(',')" :key="userId">
                                                {{ getUserNameById(users, parseInt(userId)) }}
                                            </li>
                                        </ul>
                                    </span>
                                    <span v-else class="text-muted">No asignado</span>
                                </td>
                                <!-- <td>
                                    <button @click="editTask(task.id)" class="btn-edit">Editar</button>
                                    <button @click="deleteTask(task.id)" class="btn-delete">Eliminar</button>
                                </td> -->

                                <td>
                                    <button class="btn btn-edit btn-sm" title="Editar" @click="editTask(task.id)">
                                        <i class="bx bx-edit"></i>
                                        </button>&nbsp;
                                    <button class="btn btn-delete btn-sm"  title="Eliminar" @click="deleteTask(task.id)">
                                        <i class="bx bxs-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agregamos el modal -->
            <div id="modal" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bx bx-task"></i> Nueva Tarea</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="mb-3 floating-label">
                                            <input v-model="task.title" class="form-control" placeholder=" " required />
                                            <label for="task.title">Nombre de la Tarea</label>
                                            <div v-if="!task.title" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="mb-3 floating-label">
                                            <input v-model="task.description" class="form-control" placeholder=" " required />
                                            <label for="task.description">Descripción</label>
                                            <div v-if="!task.description" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control" v-model="task.due_date" required>
                                    <div v-if="!task.due_date" class="text-danger">Este campo es obligatorio.</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-label-select">
                                            <select class="form-control" v-model="task.priority_id">
                                                <option disabled selected>Seleccionar</option>
                                                <option v-for="priority in priorities" :key="priority.id" :value="priority.id">
                                                    {{ priority.description }}
                                                </option>
                                            </select>
                                            <label for="grupo">Prioridad</label>
                                            <div v-if="!task.priority_id" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-label-select">
                                            <select class="form-control" v-model="task.status_id">
                                                <option disabled selected>Seleccionar</option>
                                                <option v-for="status in statuses" :key="status.id" :value="status.id">
                                                    {{ status.name }}
                                                </option>
                                            </select>
                                            <label for="grupo">Estatus</label>
                                            <div v-if="!task.status_id" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-label-select">
                                            <select class="form-control" v-model="task.task_type_id">
                                                <option disabled selected>Seleccionar</option>
                                                <option v-for="type in types" :key="type.id" :value="type.id">
                                                    {{ type.name }}
                                                </option>
                                            </select>
                                            <label for="grupo">Tipo de Tarea</label>
                                            <div v-if="!task.task_type_id" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-label-select">
                                            <select class="form-control" v-model="task.category_id">
                                                <option disabled selected>Seleccionar</option>
                                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                                    {{ category.description }}
                                                </option>
                                            </select>
                                            <label for="grupo">Categoría de Tarea</label>
                                            <div v-if="!task.category_id" class="text-danger">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Asignar a</label>
                                    <select class="form-select" multiple v-model="task.assigned_users">
                                        <option v-for="user in users" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                    <small class="text-muted">Puedes seleccionar múltiples usuarios (Ctrl + Click).</small>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button v-if="flag === 0" type="button" class="btn btn-primary" @click="createTask">Guardar</button>
                            <button v-if="flag === 1" type="button" class="btn btn-primary" @click="updateTask">Editar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin del modal -->
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
            listTask: [],

            task: {
                title: '',
                description: '',
                due_date: '',
                priority_id: null,
                status_id: null,
                task_type_id: null,
                category_id: null,
                assigned_users: []
            },
            priorities: [],
            statuses: [],
            types: [],
            categories: [],
            users: [],
            flag: 0,
        };
    },

    created() {

    },

    computed: {

    },

    mounted() {
        this.getTasks();
        this.fetchOptions();
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

        /* Resto de Método */
        async getTasks() {
            try {
                this.incrementarCarga();
                const response = await axios.get('/Dashboard/get');
                this.listTask = response.data.data;
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async createTask() {
            this.incrementarCarga();

            if (!this.task.title || !this.task.due_date || !this.task.priority_id || !this.task.status_id || !this.task.task_type_id || !this.task.category_id) {
                Swal.fire('Error', 'Por favor complete todos los campos obligatorios.', 'error');
                this.decrementarCarga();
                return;
            }

            const task = {
                title: this.task.title,
                description: this.task.description,
                due_date: this.task.due_date,
                priority_id: this.task.priority_id,
                status_id: this.task.status_id,
                task_type_id: this.task.task_type_id,
                category_id: this.task.category_id,
                assigned_users: this.task.assigned_users
            };

            try {
                const response = await axios.post('/Dashboard/create', task);
                if (response.data.success) {
                    this.getTasks();
                    this.closeModal();
                    this.resetForm();
                    Swal.fire('Éxito', response.data.message, 'success');
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al crear la tarea.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async editTask(id) {
            this.incrementarCarga();
            this.flag = 1;
            this.openModal();

            try {
                const response = await axios.get(`/Dashboard/edit/${id}`);

                if (response.data.data) {
                    // Llenamos el formulario con los datos de la tarea obtenida
                    this.task = {
                        id: response.data.data.id,
                        title: response.data.data.title,
                        description: response.data.data.description,
                        due_date: response.data.data.due_date,
                        priority_id: response.data.data.priority_id,
                        status_id: response.data.data.status_id,
                        task_type_id: response.data.data.task_type_id,
                        category_id: response.data.data.category_id,
                        assigned_users: response.data.data.user_assignments
                            ? response.data.data.user_assignments.split(',').map(id => parseInt(id))
                            : []
                    };

                } else {
                    Swal.fire('Error', 'No se encontraron datos para esta tarea.', 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al editar la tarea.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async updateTask() {
            this.incrementarCarga();

            try {
                const response = await axios.put(`/Dashboard/update/${this.task.id}`, {
                    title: this.task.title,
                    description: this.task.description,
                    due_date: this.task.due_date,
                    priority_id: this.task.priority_id,
                    status_id: this.task.status_id,
                    task_type_id: this.task.task_type_id,
                    category_id: this.task.category_id,
                    assigned_users: this.task.assigned_users.join(',') // Convertimos el array a string separado por comas
                });

                if (response.data.task) {
                    Swal.fire('Éxito', 'Tarea actualizada correctamente', 'success');
                    this.closeModal();
                    this.getTasks(); // Refrescar la lista de tareas
                } else {
                    Swal.fire('Error', 'No se pudo actualizar la tarea', 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al actualizar la tarea.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async deleteTask(id) {
            const confirmDelete = await Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta tarea será eliminada permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (confirmDelete.isConfirmed) {
                this.incrementarCarga(); // Mostrar el indicador de carga

                try {
                    const response = await axios.delete(`/Dashboard/delete/${id}`);

                    if (response.data.message) {
                        Swal.fire('Éxito', response.data.message, 'success');
                        this.getTasks(); // Refrescar la lista de tareas
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar la tarea', 'error');
                    }
                } catch (error) {
                    if (error.response && error.response.data && error.response.data.message) {
                        Swal.fire('Error', error.response.data.message, 'error');
                    } else {
                        Swal.fire('Error', 'Se produjo un error al eliminar la tarea.', 'error');
                    }
                } finally {
                    this.decrementarCarga(); // Ocultar el indicador de carga
                }
            }
        },


        async fetchOptions() {
            this.incrementarCarga();
            try {
                const [prioritiesRes, statusesRes, typesRes, categoriesRes, usersRes] = await Promise.all([
                    axios.get('/Priority/get'),
                    axios.get('/Status/get'),
                    axios.get('/Types/get'),
                    axios.get('/Categories/get'),
                    axios.get('/Users/get')
                ]);

                this.priorities = prioritiesRes.data.data;
                this.statuses = statusesRes.data.data;
                this.types = typesRes.data.data;
                this.categories = categoriesRes.data.data;
                this.users = usersRes.data.data;
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                }
            } finally {
                this.decrementarCarga();
            };
        },

        nuevo() {
            this.resetForm();
            this.flag = 0;
            this.openModal();
        },

        resetForm() {
            this.task = {
                title: '',
                description: '',
                due_date: '',
                priority_id: null,
                status_id: null,
                task_type_id: null,
                category_id: null,
                assigned_users: []
            };
        },

        openModal() {
            $("#modal").modal({ backdrop: "static", keyboard: false });
            $("#modal").modal("toggle");
        },

        closeModal() {
            $("#modal").modal("hide");
        },

        formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString('es-ES', options);
        },

        getNameById(list, id) {
            const item = list.find(element => element.id === id);
            return item ? item.description || item.name : 'No especificado';
        },

        getUserNameById(users, id) {

            const user = users.find(user => user.id === id);
            return user ? user.name : 'Usuario no encontrado';
        },

        getPriorityClass(priority) {
            switch (priority.toLowerCase()) {
                case 'alta':
                    return 'bg-danger';
                case 'media':
                    return 'bg-warning';
                case 'baja':
                    return 'bg-success';
                default:
                    return 'bg-dark';
            }
        },

        getStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'no iniciada':
                    return 'bg-secondary';
                case 'en progreso':
                    return 'bg-primary';
                case 'completada':
                    return 'bg-success';
                case 'bloqueada':
                    return 'bg-danger';
                default:
                    return 'bg-light';
            }
        },

        getTaskTypeClass(taskType) {
            switch (taskType.toLowerCase()) {
                case 'nueva':
                    return 'bg-primary';
                case 'actualización':
                    return 'bg-info';
                case 'corrección':
                    return 'bg-warning';
                default:
                    return 'bg-secondary';
            }
        },

        getCategoryClass(category) {
            switch (category.toLowerCase()) {
                case 'personal':
                    return 'bg-success';
                case 'trabajo':
                    return 'bg-danger';
                case 'estudio':
                    return 'bg-primary';
                default:
                    return 'bg-secondary';
            }
        },

        getAssignmentClass(count) {
            if (count === 0) {
                return 'bg-secondary';
            } else if (count === 1) {
                return 'bg-success';
            } else {
                return 'bg-warning';
            }
        },

    }
};
</script>

<style scoped>
.datos-columna {
    max-width: 150px;
    overflow-wrap: break-word;
    word-wrap: break-word;
    white-space: normal;
}

.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.table td {
    border-bottom: 1px solid #ddd;
}

.btn-edit {
    padding: 5px 10px;
    background-color: #ffc107;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.btn-edit:hover {
    background-color: #e0a800;
}

.btn-delete {
    padding: 5px 10px;
    background-color: #dc3545;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.btn-delete:hover {
    background-color: #c82333;
}
</style>


