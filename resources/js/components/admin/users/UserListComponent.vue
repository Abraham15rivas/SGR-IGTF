<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">Todos los usuarios registrados</div>
                <div class="card-body">
                    <table class="table table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Fecha Creación</th>
                                <th>Última actualización</th>
                                <th>Status</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) of list_user" :key="index">
                                <td v-text="user.name"></td>
                                <td v-text="user.email"></td>
                                <td v-text="user.role.name"></td>
                                <td>{{ user.created_at | captalize }}</td>
                                <td>{{ user.updated_at | captalize }}</td>
                                <td>{{ user.status | status}}</td>
                                <td>
                                    <button class="btn btn-info">Detalles</button>
                                    <button class="btn btn-warning">Editar</button>
                                    <button class="btn btn-danger">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#new_user">Nuevo</button>
                </div>
            </div>
            <!-- Ventana modal -->
            <new-user-modal-componet />
        </div>
    </div>
</template>

<script>
    import NewUserModalComponet from './NewUserModalComponent'
    export default {
        props: ['users'],
        data() {
            return {
                list_user: []
            }
        },
        components: {
            'new-user-modal-componet': NewUserModalComponet
        },
        filters: {
            captalize(value) {
                let date_part = value.split('T')
                let date      = date_part[0]
                let hour      = date_part[1].substr(0, 8)
                return `${ date } / ${ hour }`
            },
            status(value) {
                let status = 'Inactivo'
                if(value) {
                    status = 'Activo'
                } else {
                    status = 'Inactivo'
                }
                return status
            }
        },
        mounted() {
            this.list_user = this.users
        }
    }
</script>