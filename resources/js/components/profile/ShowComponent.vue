<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">
                    <h3 class="card-title">Datos personales</h3>
                </div>
                <div v-if="!profile.image == null" class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                :src="preview"
                                alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center">{{ profile.first_name ? profile.first_name : ''}}</h3>
                    </div>
                </div>
                <form @submit.prevent="setProfile" id="form_profile">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="first_name">Primer Nombre</label>
                            <input type="text" class="form-control" id="first_name" v-model="profile.first_name" placeholder="Primer Nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="second_name">Segundo Nombre</label>
                            <input type="text" class="form-control" id="second_name" v-model="profile.second_name" placeholder="Segundo Nombre">
                        </div>
                        <div class="form-group">
                            <label for="surname">Primer Apellido</label>
                            <input type="text" class="form-control" id="surname" v-model="profile.surname" placeholder="Primer Apellido" required>
                        </div>
                        <div class="form-group">
                            <label for="second_surname">Segundo Apellido</label>
                            <input type="text" class="form-control" id="second_surname" v-model="profile.second_surname" placeholder="Segundo Apellido">
                        </div>
                        <div class="form-group">
                            <label for="image">Imagen</label>
                            <div class="input-group">
                            <div class="custom-file">
                                <input type="file" @change="onFileUpload" class="custom-file-input" id="image">
                                <label class="custom-file-label" for="image">Seleccionar una imagen</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">cargar</span>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" :class="[`btn ${ register ? 'btn-primary' : 'btn-success' }`]" v-text="`${ register ? 'Actualizar' : 'Guardar' }`"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import { mapActions } from 'vuex'
    export default {
        data() {
            return {
                register: true,
                profile: {
                    first_name: null,
                    second_name: null,
                    surname: null,
                    second_surname: null,
                    image: null,
                },
                file_route: null,
            }
        },
        methods: {
            onFileUpload(evt) {
                this.profile.image = evt.target.files[0]
                this.file_route = URL.createObjectURL(this.profile.image)
            },
            async setProfile() {
                try {
                    let url = !this.register ? `/profile/store` : `/profile/update/${ this.profile.id }`
                    let params = new FormData(
                        document.getElementById('form_profile')
                    )
                    
                    params.append("first_name", this.profile.first_name)
                    params.append("second_name", this.profile.second_name ?? '')
                    params.append("surname", this.profile.surname)
                    params.append("first_name", this.profile.first_name)
                    params.append("second_surname", this.profile.second_surname ?? '')
                    params.append("image", this.profile.image)

                    let response = await axios.post(url, params)
                    const object = response.data
                    if(object.success == true) {
                        this.register = true
                        this.profile = JSON.parse(JSON.stringify(object.profile))
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
                            text: `${ object.message }`,
                        })
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            showProfile() {
                let profile = this.$store.state.profile
                if(profile) {
                    this.register = true
                    this.profile = JSON.parse(JSON.stringify(profile))
                    this.file_route = `/storage/${ profile.image }`
                } else {
                    this.register = false
                }
            },
            ...mapActions(['getUser', 'getProfile']),
        },
        computed: {
            preview() {
                return this.file_route
            }
        },
        mounted() {
            this.getUser()
            this.getProfile()
            setTimeout(() => this.showProfile() , 1000)
        }
    }
</script>
