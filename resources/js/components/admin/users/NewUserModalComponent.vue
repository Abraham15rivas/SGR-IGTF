<template>
    <div class="modal fade" id="new_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content bg-bav-ligth">
                <div class="modal-header card-header-bav">
                    <h5 class="modal-title" id="exampleModalLabel">Datos del usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="clearForm">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_register" method="post">
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" v-model="user.name" required autocomplete="name" autofocus>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Correo</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" v-model="user.email" required autocomplete="email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Contraseña</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" v-model="user.password" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirmar Contraseña</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" v-model="user.password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row" v-if="roles">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Asignar rol</label>
                            <div class="col-md-6">
                                <select class="form-control" name="role" v-model="user.role" id="role">
                                    <option value="null">Seleccionar</option>
                                    <option v-for="(role, index) of roles" :key="index">
                                        <span v-text="role.name"></span>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer text-right">
                    <button v-if="user_edit != null && update != true" type="submit" class="btn btn-danger" @click="clearForm">
                        <i class="fa fa-recycle"></i>
                    </button>
                    <button  type="button" class="btn btn-secondary" data-dismiss="modal" @click="clearForm">Cerrar</button>
                    <button  type="submit" :class="['btn', update == true ? 'btn-success' : 'btn-primary']" @click="setUser" v-text="`${ update == true ? 'Actualizar' : 'Registrar' }`"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'
    import Swal from 'sweetalert2'
    export default {
        props: ['user_edit', 'update'],
        data() {
            return {
                roles: null,
                user: {
                    id: null,
                    role: null,
                    name: null,
                    email: null,
                    password: null,
                    password_confirmation: null
                }
            }
        },
        watch: {
            user_edit() {
                if(this.user_edit != null) {
                    let user  = JSON.parse(JSON.stringify(this.user_edit))
                    user.role = user.role.name
                    this.user = user
                }
            }
        },
        methods: {
            clearForm() {
                // Clear form
                document.getElementById("form_register").reset()
                
                // Clear variables
                this.user.role                  = null
                this.user.name                  = null
                this.user.email                 = null
                this.user.password              = null
                this.user.password_confirmation = null

                if(this.user_edit != null) {
                    this.user.id         = null
                    this.user.created_at = null
                    this.user.updated_at = null
                    this.user.status     = null
                    this.user.role_id    = null
                    this.$emit('clearEdit', true)
                }
            },
            async setUser() {
                try {
                    if(
                        this.user.role == null ||
                        this.user.name == null ||
                        this.user.email == null ||
                        this.user.password == null ||
                        this.user.password_confirmation == null
                    ) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: `Debe rellenar todos los campos`,
                        })
                    } else {

                        let url = !this.update ? `/admin/register` : `/admin/update/${ this.user.id }`
                        let params = new FormData(
                            document.getElementById('form_register')
                        )
                       
                        let response = await axios.post(url, params)
                        const object = response.data
                        if(object.success == true) {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: `${ object.message }`,
                                showConfirmButton: false,
                                timer: 1500
                            })

                            // Clear form and variables
                            this.clearForm()

                            // Refresh list User
                            this.$emit('refreshList')

                            // Close modal window
                            setTimeout(() => $('#new_user').modal('hide'), 2000)
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${ object.message }`,
                                html: `<span>${ this.loadError(object.errors) }</span>`,
                            })
                        }
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            loadError(object) {
                let html = `Ningún error detectado`
                
                if(object) {
                    const errors = Object.values(object)
                    let ul = document.createElement("ul")
                    let li = ``
                    html = ``

                    errors.forEach(element => {
                        element.forEach(ele => {
                            li = document.createElement("li")
                            li.innerHTML = ele
                            ul.appendChild(li)
                        })
                    })
                    html += ul.innerHTML
                }

                return html
            },
            ...mapActions(['getRoles']),
        },
        mounted() {
            this.getRoles()
            setTimeout(() => this.roles = this.$store.state.roles, 2000)
        }
    }
</script>