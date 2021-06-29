<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">
                    <div class="row">
                        <div class="col-4">
                            <span>Todos los usuarios registrados</span>
                        </div>
                        <div class="col-8 text-right">
                            <span>
                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#new_user" @click="update = false">Nuevo</button>
                            </span>
                        </div>
                    </div>
                </div>
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
                                    <button class="btn btn-info" @click="detailUser(user.id)">Detalles</button>
                                    <button class="btn btn-warning" type="button" @click="editUser(user)">Editar</button>
                                    <button
                                        v-if="getIdUser != user.id"
                                        :class="[`btn ${ !user.status ? 'btn-success' : 'btn-danger' }`]" 
                                        @click="changeStatus(index, user.id)"
                                        v-text="`${ !user.status ? 'Habilitar' : 'Deshabilitar' }`"
                                    ></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <!-- code -->
                </div>
            </div>
            <!-- Ventanas modal -->
            <new-user-modal-component 
                :user_edit="user_edit"
                :update="update"
                @clearEdit="user_edit = null" 
                @refreshList="refreshUserList"
            />
            <detail-user-modal-component :detail_user="detail_user"/>
        </div>
    </div>
</template>

<script>
    import NewUserModalComponent from './NewUserModalComponent'
    import DetailUserModalComponent from './DetailUserModalComponent'
    import Swal from 'sweetalert2'
    import { mapActions, mapGetters } from 'vuex'
    export default {
        props: ['users'],
        data() {
            return {
                list_user: [],
                detail_user: null,
                user_edit: null,
                update: false
            }
        },
        components: {
            'new-user-modal-component': NewUserModalComponent,
            'detail-user-modal-component': DetailUserModalComponent
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
                }
                return status
            }
        },
        methods: {
            async detailUser(id) {
                try {
                    let url = `/admin/detail/user/${ id }`
                    let response = await axios.get(url)
                    const object = response.data
                    if(object) {
                        this.detail_user = object
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: `No hay detalles para mostrar`,
                        })
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            async refreshUserList() {
                try {
                    let url = `/admin/list/users/data`
                    let response = await axios.get(url)
                    const object = response.data
                    if(object) {
                        this.list_user = object
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: `Algo salió mal`,
                        })
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            changeStatus(index, id) {
                Swal.fire({
                    title: `¿Deseas continuar con esta acción?`,
                    text: "¡Seguro!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, continuar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'Cambios efectuados de manera satisfactoria',
                            'Listo',
                            'success'
                        )
                        this.change(index, id)
                    }
                })
            },
            async change(index, id) {
                try {
                    let url = `/admin/change/status/user/${ id }`
                    let response = await axios.put(url)
                    const object = response.data
                    if(object) {
                        this.list_user[index].status = !this.list_user[index].status
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: `Algo salió mal`,
                        })
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            editUser(user) {
                this.update = true
                this.user_edit = user
                $('#new_user').modal('show')
            },
            ...mapActions(['getUser']),
        },
        computed: {
            ...mapGetters(['getIdUser'])
        },
        mounted() {
            this.list_user = this.users
            this.getUser()
        }
    }
</script>