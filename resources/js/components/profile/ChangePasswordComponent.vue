<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">
                    <h3 class="card-title">Datos personales</h3>
                </div>
                <form @submit.prevent="setPassword">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="email">Correo</label>
                            <input type="email" class="form-control" id="email" :value="getEmailUser" readonly>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" id="password" v-model="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmed_password">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmed_password"  v-model="password_confirmation" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import { mapActions, mapGetters } from 'vuex'
    export default {
        data() {
            return {
                password: null,
                password_confirmation: null
            }
        },
        methods: {
            async setPassword() {
                try {
                    let url = `/profile/update/password/${ this.$store.state.user.id }`
                    const params = {
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    }
                    let response = await axios.put(url, params)
                    const object = response.data
                    if(object.success == true) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: `${ object.message }`,
                            showConfirmButton: false,
                            timer: 1500
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: `
                                <ul>
                                    <li>
                                        ${ object.message.password[0] ?? '' }
                                    </li>
                                    <li>
                                        ${ object.message.password[1] ?? '' }
                                    </li>
                                <ul>
                                `,
                        })
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            ...mapActions(['getUser']),
        },
        computed: {
            ...mapGetters(['getEmailUser'])
        },
        mounted() {
            this.getUser()
        }
    }
</script>
