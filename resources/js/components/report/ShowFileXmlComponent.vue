<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">
                    <i class="fa fa-cogs"></i> Reportes en formatos XML
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <h6>Documento Declaración IGTF: procesarITFServlet</h6>
                        <textarea class="form-control" name="textarea" rows="25" cols="50" readonly> 
                            
                        </textarea>
                    </div>
                    <div class="form-group">
                        <h6>Documento Confirmación Declaración IGTF: confirmarITFServlet</h6>
                        <textarea class="form-control" name="textarea" rows="25" cols="50" readonly> 
                            
                        </textarea>
                    </div>
                    <div class="form-group">
                        <h6>Documento Confirmación Declaración IGTF: confirmarITFServlet</h6>
                        <textarea class="form-control" name="textarea" rows="25" cols="50" readonly> 
                            
                        </textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-8">
                            <button class="btn btn-primary" :disabled="sheet == 'Temporal' ? true : false" @click="getTemporary">Temporal</button>
                            <button class="btn btn-primary" :disabled="sheet == 'Operacion' ? true : false" @click="getOperaction">Operaciones</button>
                            <button class="btn btn-primary" :disabled="sheet == 'Definitivo' ? true : false" @click="getDefinitive">Definitivo</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button :class="[sheet == 'Definitivo' ? 'btn btn-success' : 'btn btn-success disabled']" @click="setConfirmation">Confirmar</button>
                            <button class="btn btn-success">Exportar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['details'],
        data() {
            return {
                sheet: 'Temporal'
            }
        }
    }
</script>