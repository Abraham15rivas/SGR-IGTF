<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav text-center">
                    <span v-if="!ready">No has elegido fecha</span>
                    <span v-else>
                        <i class="fa fa-cogs"></i> Reportes en formatos XML
                    </span>
                </div>
                <div class="card-body">
                    <span v-if="!ready">
                        <h6>Elegir fecha del reporte:</h6>
                        <div class="input-group mb-3">
                            <input id="date" type="date" v-model="date_query" class="form-control" min="" :max="max" autofocus autocomplete="on">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success" @click="queryTransaction">Consultar</button>
                            </div>
                        </div>
                    </span>
                    <span v-else>
                        <div class="form-group" v-if="document == 'ITFBancoDetalle'">
                            <h6>Documento Declaración IGTF: ITFBancoDetalle</h6>
                            <textarea class="form-control" name="textarea" rows="25" cols="50" readonly v-text="files.ITFBancoDetalle.file"></textarea>
                        </div>
                        <div class="form-group" v-if="document == 'ITFBanco'">
                            <h6>Documento Confirmación Declaración IGTF: ITFBanco</h6>
                            <textarea class="form-control" name="textarea" rows="25" cols="50" readonly v-text="files.ITFBanco.file"></textarea>
                        </div>
                        <div class="form-group" v-if="document == 'ITFBancoConfirmacion'">
                            <h6>Documento Confirmación Declaración IGTF: ITFBancoConfirmacion</h6>
                            <textarea class="form-control" name="textarea" rows="25" cols="50" readonly v-text="files.ITFBancoConfirmacion.file"></textarea>
                        </div>
                    </span>
                </div>
                <div class="card-footer">
                    <div v-if="ready" class="row">
                        <div class="col-md-8">
                            <button class="btn btn-primary" :disabled="document == 'ITFBancoDetalle' ? true : false" @click="getITFBancoDetalle">ITFBancoDetalle</button>
                            <button class="btn btn-primary" :disabled="document == 'ITFBanco' ? true : false" @click="getITFBanco">ITFBanco</button>
                            <button class="btn btn-primary" :disabled="document == 'ITFBancoConfirmacion' ? true : false" @click="getITFBancoConfirmacion">ITFBancoConfirmacion</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button :class="[document == 'ITFBanco' ? 'btn btn-success' : 'btn btn-success disabled']">Confirmar</button>
                            <a :download="`${ files[document].name }`" :href="`/storage/${ files[document].route }`" :class="[files[document].route == null ? 'btn btn-warning disabled' : 'btn btn-warning']">
                                <i class="fas fa-fw fa-file-excel"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import moment from 'moment'
    export default {
        data() {
            return {
                document: 'ITFBancoDetalle',
                ready: false,
                date_query: null,
                max: moment().format('YYYY-MM-DD'),
                route_file: null,
                name_file: null,
                files: null,
            }
        },
        methods: {
            async queryTransaction() {
                if(this.date_query) {
                    try {
                        const url = `/show/xml/transaction/${ this.date_query }`
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
                            this.files = object.files
                            this.ready = true
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `No puede estar vació el campo de fecha`,
                    })
                }
            },
            getITFBancoDetalle() {
                this.document = 'ITFBancoDetalle'
            },
            getITFBanco() {
                this.document = 'ITFBanco'
            },
            getITFBancoConfirmacion() {
                this.document = 'ITFBancoConfirmacion'
            }
        }
    }
</script>