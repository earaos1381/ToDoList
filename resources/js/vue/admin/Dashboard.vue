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

                <div class="card-body d-flex align-items-center justify-content-between">
                    <h5 class="card-header mb-0"> Mis Tareas</h5>
                    <div class="d-flex align-items-center gap-3">
                        <!-- <div class="input-group w-50">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input v-model="filtro" class="form-control" placeholder="Buscar">
                        </div> &nbsp; -->
                        <button type="button" class="btn btn-dark" @click="nuevo"><i class='bx bx-plus-medical'></i> Agregar</button>
                    </div>
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
                                <th><i class='bx bxs-user-rectangle'></i> Propietario</th>
                                <th><i class='bx bxs-user-detail' ></i> Usuarios Asignados</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-if="listTask.length === 0">
                                <td colspan="9" class="text-center">Sin Información</td>
                            </tr>

                            <!-- Iterar sobre las tareas y mostrar cada una en una fila -->
                            <tr v-for="task in listTask" :key="task.id">
                                <td class="datos-columna">{{ task.title }}</td>
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
                                        {{ task.user.name }}
                                    </span>
                                    <span v-else class="text-muted">No asignado</span>
                                </td>
                                <td>
                                    <button class="btn btn-dark" title="Share Task" @click="shareTaskModal(task.id)">
                                        <i class='bx bxs-share-alt'></i>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-edit" title="Editar" @click="editTask(task.id)">
                                        <i class="bx bx-edit"></i>
                                        </button>&nbsp;
                                    <button class="btn btn-delete"  title="Eliminar" @click="deleteTask(task.id)">
                                        <i class="bx bxs-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Task -->
                <div id="modal" class="modal fade custom-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bx bx-task"></i> {{ modalTitle }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input v-model="task.title" class="form-control" placeholder="Nombre de la tarea" required>
                                                <label>Título de la Tarea</label>
                                            </div>
                                            <div v-if="!task.title" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" v-model="task.due_date" placeholder="Fecha de Vencimiento" required>
                                                <label>&nbsp;Fecha de Vencimiento</label>
                                            </div>
                                            <div v-if="!task.due_date" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>
                                    </div>

                                    <!-- Descripción (Ocupa toda la fila) -->
                                    <div class="row g-3 mt-2">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea v-model="task.description" class="form-control" placeholder="Descripción" style="height: 120px;" required></textarea>
                                                <label>Descripción de la Tarea</label>
                                            </div>
                                            <div v-if="!task.description" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>
                                    </div>

                                    <!-- Prioridad y Estatus -->
                                    <div class="row g-3 mt-2 row-cols-md-2">
                                        <div class="col">
                                            <div class="form-floating">
                                                <select class="form-select" v-model="task.priority_id">
                                                    <option disabled selected>Seleccionar</option>
                                                    <option v-for="priority in priorities" :key="priority.id" :value="priority.id">
                                                        {{ priority.description }}
                                                    </option>
                                                </select>
                                                <label>Prioridad</label>
                                            </div>
                                            <div v-if="!task.priority_id" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>

                                        <div class="col">
                                            <div class="form-floating">
                                                <select class="form-select" v-model="task.status_id">
                                                    <option disabled selected>Seleccionar</option>
                                                    <option v-for="status in statuses" :key="status.id" :value="status.id">
                                                        {{ status.name }}
                                                    </option>
                                                </select>
                                                <label>Estatus</label>
                                            </div>
                                            <div v-if="!task.status_id" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>
                                    </div>

                                    <!-- Tipo de Tarea y Categoría -->
                                    <div class="row g-3 mt-2 row-cols-md-2">
                                        <div class="col">
                                            <div class="form-floating">
                                                <select class="form-select" v-model="task.task_type_id">
                                                    <option disabled selected>Seleccionar</option>
                                                    <option v-for="type in types" :key="type.id" :value="type.id">
                                                        {{ type.name }}
                                                    </option>
                                                </select>
                                                <label>Tipo de Tarea</label>
                                            </div>
                                            <div v-if="!task.task_type_id" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>

                                        <div class="col">
                                            <div class="form-floating">
                                                <select class="form-select" v-model="task.category_id">
                                                    <option disabled selected>Seleccionar</option>
                                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                                        {{ category.description }}
                                                    </option>
                                                </select>
                                                <label>Categoría</label>
                                            </div>
                                            <div v-if="!task.category_id" class="text-danger mt-1">Este campo es obligatorio.</div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x"></i> Cerrar
                                </button>
                                <button v-if="flag === 0" type="button" class="btn btn-success" @click="createTask">
                                    <i class="bx bx-save"></i> Guardar
                                </button>
                                <button v-if="flag === 1" type="button" class="btn btn-warning text-white" @click="updateTask">
                                    <i class="bx bx-edit"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Share Task -->
                <div class="modal fade" id="shareTaskModal" tabindex="-1" aria-labelledby="shareTaskModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="shareTaskModalLabel">Compartir Tarea</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="shareTask">
                                    <!-- Lista de usuarios con checkboxes -->
                                    <div class="form-group">
                                        <label for="users">Seleccionar Usuarios</label>
                                        <div v-for="user in users" :key="user.id" class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                :id="'user-' + user.id"
                                                v-model="selectedUsers"
                                                :value="user.id"
                                                :checked="isUserAssigned(user.id)"
                                            />
                                            <label :for="'user-' + user.id" class="form-check-label">
                                                {{ user.name }}
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Botón para guardar -->
                                    <button type="submit" class="btn btn-primary mt-3">Compartir</button>
                                </form>
                            </div>

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
            tasksAboutToExpire: [],
            filtro: '',
            modalTitle: '',
            task: {
                title: '',
                description: '',
                due_date: '',
                priority_id: null,
                status_id: null,
                task_type_id: null,
                category_id: null,
            },
            invalidDate: false,
            priorities: [],
            statuses: [],
            types: [],
            categories: [],
            users: [],
            selectedUsers: [],
            assignedUsers: [] ,
            taskId: null,
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
        this.fetchAssignedUsers();
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
                this.tasksAboutToExpire = response.data.tasksAboutToExpire;

                if (this.tasksAboutToExpire.length > 0) {
                    this.showAlert();
                }

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

        showAlert() {
            let taskDetails = this.tasksAboutToExpire.map(task => {
                return `${task.title} - Limite: ${task.due_date} |`;
            }).join('\n');

            Swal.fire({
                title: '¡Tienes tareas por vencer!',
                text: `Tienes ${this.tasksAboutToExpire.length} tarea(s) por vencer en los próximos 3 días:\n\n${taskDetails}`,
                icon: 'warning',
                confirmButtonText: 'Ver tareas'
            });
        },


        async createTask() {
            this.incrementarCarga();

            if (!this.task.title || !this.task.due_date || !this.task.priority_id || !this.task.status_id || !this.task.task_type_id || !this.task.category_id) {
                Swal.fire('Error', 'Por favor complete todos los campos obligatorios.', 'error');
                this.decrementarCarga();
                return;
            }

            this.validateDueDate();
            if (this.invalidDate) {
                alert('La fecha de vencimiento no puede ser anterior a la actual.');
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
            this.modalTitle = "Editar Tarea";
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

            this.validateDueDate();
            if (this.invalidDate) {
                alert('La fecha de vencimiento no puede ser anterior a la actual.');
                this.decrementarCarga();
                return;
            }

            try {
                const response = await axios.put(`/Dashboard/update/${this.task.id}`, {
                    title: this.task.title,
                    description: this.task.description,
                    due_date: this.task.due_date,
                    priority_id: this.task.priority_id,
                    status_id: this.task.status_id,
                    task_type_id: this.task.task_type_id,
                    category_id: this.task.category_id,
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
                    axios.get('/Dashboard/getU'),
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

        validateDueDate() {
            const today = new Date().toISOString().split("T")[0]; // Obtiene la fecha actual (YYYY-MM-DD)
            this.invalidDate = this.task.due_date < today;
        },

        nuevo() {
            this.resetForm();
            this.modalTitle = "Nueva Tarea";
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


        isUserAssigned(userId) {
            return this.assignedUsers.includes(userId);
        },

        async shareTask() {
            try {
                const response = await axios.post('/Dashboard/shareTask', {
                    task_id: this.taskId,
                    user_ids: this.selectedUsers,
                });

                if (response.data.message) {
                    Swal.fire('Éxito', response.data.message, 'success');
                    this.closeModalshareTask();  // Cerrar el modal
                }
            } catch (error) {
                this.closeModalshareTask();
                if (error.response && error.response.data && error.response.data.message) {
                    Swal.fire('Error', error.response.data.message, 'error');
                } else {
                    Swal.fire('Error', 'Se produjo un error al compartir la tarea.', 'error');
                }
            }
        },

        async shareTaskModal(taskId) {
            this.taskId = taskId;
            this.selectedUsers = [];

            await this.fetchAssignedUsers();

            // Luego abrimos el modal
            this.openModalshareTask();
        },

        async fetchAssignedUsers() {
            try {
                const response = await axios.get(`/Dashboard/getAssigned/${this.taskId}`);
                this.assignedUsers = response.data;  // Asigna los usuarios ya asignados
                this.selectedUsers = [...this.assignedUsers];  // Preselecciona los usuarios
            } catch (error) {
                console.error('Error al obtener los usuarios asignados', error);
            }
        },

        openModalshareTask() {
            $("#shareTaskModal").modal({ backdrop: "static", keyboard: false });
            $("#shareTaskModal").modal("toggle");
        },

        closeModalshareTask() {
            $("#shareTaskModal").modal("hide");
        },


        formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString('es-ES', options);
        },

        getNameById(list, id) {
            const item = list.find(element => element.id === id);
            return item ? item.description || item.name : 'No especificado';
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

table th,
.table td {
    padding: 12px;
    text-align: left;
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


