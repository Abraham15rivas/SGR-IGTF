<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav text-center">
                    <span>Aquí puedes cambiar el status de las transacciones de una fecha específica</span>
                </div>
                <div class="card-body">
                   <span>
                        <h6>Elegir fecha y status:</h6>
                        <div class="input-group mb-3">
                            <select class="form-control mr-2" name="status" v-model="status_request" id="status">
                                <option value="null">Seleccionar un status</option>
                                <option v-for="(status, index) of status" :key="index" :value="status.pk_Estat">
                                    <span v-text="status.descEstat"></span>
                                </option>
                            </select>
                            
                            <input id="date" type="date" v-model="date_query" class="form-control ml-3" min="" :max="max" autofocus autocomplete="on">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success" @click="confirm">Consultar</button>
                            </div>
                        </div>
                    </span>
                </div>
                <div class="card-footer">
                    <!-- Code -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import moment from 'moment'

    export default {
        props:{
            status: Array
        },
        data() {
            return {
                max: moment().format('YYYY-MM-DD'),
                date_query: null,
                status_request: null
            }
        },
        methods: {
            confirm() {
                if(this.date_query && this.status_request != null) {
                    Swal.fire({
                        title: `¿Seguro deseas ejecutar esta acción, esto afectará todas las transacciones de la siguiente fecha: ${ this.date_query } ?`,
                        text: "¡Estos cambios pueden afectar el funcionamiento del sistema!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, continuar!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.queryTransaction()
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `No puede estar vació los campo`,
                    })
                }
            },
            async queryTransaction() {
                try {
                    const url = `/admin/change/status/${ this.date_query }/${ this.status_request }`
                    let response = await axios.get(url)
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
                            text: `${ object.message }`,
                        })
                        this.ready = false
                    }
                } catch (error) {
                    console.log(error)
                }                
            }
        }
    }
</script>