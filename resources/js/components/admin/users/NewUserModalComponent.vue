<template>
    <div class="modal fade" id="new_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content bg-bav-ligth">
                <div class="modal-header card-header-bav">
                    <h5 class="modal-title" id="exampleModalLabel">Datos del usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form">
                       
                    </form>
                </div>
                <div class="modal-footer text-right">
                    <button  type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button  type="button" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },
        mounted() {
            console.log('Componet mounted')
        }
    }
</script>