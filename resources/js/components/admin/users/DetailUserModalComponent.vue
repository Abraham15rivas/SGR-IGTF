<template>
    <div class="modal fade" id="detail_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content bg-bav-ligth">
                <div class="modal-header card-header-bav">
                    <h5 class="modal-title" id="exampleModalLabel">Detalles del usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8 col-12">
                                <ul v-if="detail_user">
                                    <li>
                                        <b>Primer Nombre:</b>
                                        <span v-text="`${ detail_user.first_name != null ? detail_user.first_name : 'No registrado' }`"></span>
                                    </li>
                                    <li>
                                        <b>Segundo Nombre:</b>
                                        <span v-text="`${ detail_user.second_name != null ? detail_user.second_name : 'No registrado' }`"></span>
                                    </li>
                                    <li>
                                        <b>Primer Apellido:</b>
                                        <span v-text="`${ detail_user.surname != null ? detail_user.surname : 'No registrado' }`"></span>
                                    </li>
                                    <li>
                                        <b>Segundo Apellido:</b>
                                        <span v-text="`${ detail_user.second_surname != null ? detail_user.second_surname : 'No registrado' }`"></span>
                                    </li>
                                    <li>
                                        <b>Fecha Creación:</b>
                                        <span>{{ detail_user.created_at != null ? (detail_user.created_at | captalize): 'No registrado' }}</span>
                                    </li>
                                    <li>
                                        <b>Última actualización:</b>
                                        <span>{{ detail_user.updated_at != null ? (detail_user.updated_at | captalize) : 'No registrado' }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-4 text-center col-12">
                                <span v-if="detail_user && detail_user.image != null">
                                    <img class="image-profile" :src="`/storage/${ detail_user.image }`" alt="image profile">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button  type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['detail_user'],
        watch: {
            detail_user() {
                if(this.detail_user != null) {
                    $('#detail_user').modal('show')
                }
            }
        },
        filters: {
            captalize(value) {
                let date_part = value.split('T')
                console.log(date_part)
                let date      = date_part[0]
                let hour      = date_part[1].substr(0, 8)
                return `${ date } / ${ hour }`
            },
        }
    }
</script>

<style lang="css">
    .image-profile {
        background-size: contain;
        width: 50%;
    }
</style>