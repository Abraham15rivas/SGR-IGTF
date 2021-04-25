<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav text-center">
                    <span v-if="!ready">No has elegido fecha</span>
                    <span v-else>Transacciones: {{ sheet }}</span>
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
                        <vue-excel-editor
                            v-if="jsonData.length > 0"
                            ref="grid"
                            @select="onSelect"
                            v-model="jsonData" 
                            autocomplete 
                            filter-row
                            delete
                        >
                            <vue-excel-column class="yellow" field="referencia" label="REFERENCIA" />
                            <vue-excel-column field="rif" label="RIF" />
                            <vue-excel-column field="cuenta" label="CUENTA" />
                            <vue-excel-column field="cliente" label="CLIENTE" />
                            <vue-excel-column field="fecha" label="FECHA" />
                            <vue-excel-column field="hora" label="HORA" />
                            <vue-excel-column field="tansaccion" label="TANSACCION" />
                            <vue-excel-column field="instrumento" label="INSTRUMENTO" />
                            <vue-excel-column field="endoso" label="ENDOSO" />
                            <vue-excel-column field="concepto" label="CONCEPTO" />
                            <vue-excel-column field="monto" label="MONTO" />
                            <vue-excel-column field="impuesto" label="IMPUESTO" />
                            <vue-excel-column field="estatus" label="ESTATUS" />
                        </vue-excel-editor>
                        <span v-else>
                            <h1 class="text-center">No hay datos para mostrar</h1>
                        </span>
                        <span v-if="sheet == 'Definitivo'">
                            <h5 class="mt-2 text-center">
                                Totales
                            </h5>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Cantidad de Transacciones</th>
                                        <td v-text="definitives.length"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto Global</th>
                                        <td v-text="globalAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto Impuesto SENIAT</th>
                                        <td v-text="taxAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Cantidad de Transacciones Cheque de Gerencia</th>
                                        <td v-text="checkAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto sumatorio Base Cheque de Gerencia</th>
                                        <td v-text="checkSumAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto sumatorio Impuesto Cheque de Gerencia</th>
                                        <td v-text="checkSumTaxAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Cantidad de Transacciones Debitos Bancarios</th>
                                        <td v-text="dbAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto Sumatorio Base Debitos Bancarios</th>
                                        <td v-text="dbAmount"></td>
                                    </tr>
                                    <tr>
                                        <th>Monto Sumatorio Impuesto Debitos Bancarios</th>
                                        <td v-text="dbSumTaxAmount"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </span>
                    </span>
                </div>
                <div class="card-footer">
                    <div class="row" v-if="ready">
                        <div class="col-md-8">
                            <button class="btn btn-primary" :disabled="sheet == 'Temporal' ? true : false" @click="getTemporary">Temporal</button>
                            <button class="btn btn-primary" :disabled="sheet == 'Operacion' ? true : false" @click="getOperaction">Operaciones</button>
                            <button class="btn btn-primary" :disabled="sheet == 'Definitivo' ? true : false" @click="getDefinitive">Definitivo</button>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-danger" :disabled="seleted == null ? true : false" @click="delRecord">Borrar</button>
                            <button class="btn btn-success" :disabled="sheet != 'Definitivo' ? true : false" @click="setConfirmation">Confirmar</button>
                            <a :download="name_file" :href="`/storage/${ route_file }`" :class="[route_file == null ? 'btn btn-warning disabled' : 'btn btn-warning']">
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
                seleted: null,
                transactions: [],
                jsonData: [],
                temporalies: [],
                operactions: [],
                definitives: [],
                sheet: 'Temporal',
                date_query: null,
                max: moment().format('YYYY-MM-DD'),
                ready: false,
                route_file: null,
                name_file: null
            }
        },
        methods: {
            async queryTransaction() {
                if(this.date_query) {
                    try {
                        const url = `/show/excel/transaction/${ this.date_query }`
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
                            this.transactions = object.transactions
                            this.filterTransaction()
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
            getTemporary() {
                this.sheet = 'Temporal'
                this.$set(this, 'jsonData', this.temporalies)
            },
            getOperaction() {
                this.sheet = 'Operacion'
                this.$set(this, 'jsonData', this.operactions)
            },
            getDefinitive() {
                this.sheet = 'Definitivo'
                this.filterDefinitive()
                this.$set(this, 'jsonData', this.definitives)
            },
            delRecord() {
                if(this.seleted) {
                    this.$refs.grid.deleteRecord(this.seleted)
                    this.seleted = null
                }
            },
            onSelect(selectedRows) {
                this.seleted = selectedRows
            },
            async setConfirmation() {
                try {
                    const url  = `/store/excel/confirmation`
                    let params = {
                        definitives: this.definitives,
                        operactions: this.operactions,
                        temporalies: this.temporalies
                    }
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
                        this.route_file = object.route_file
                        this.name_file  = object.name_file
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
            filterTransaction() {
                let operations = JSON.parse(JSON.stringify(this.transactions))
                operations.forEach((element) => {
                    let tax = Object.values(element.impuesto)
                    tax.forEach(ele => {
                        if(ele.concepto == 'IMPUESTO') {
                            ele.impuesto = ''
                            if(element.operacion.estatus == 'OK') {
                                this.operactions.push(ele)
                            }
                        }
                        this.temporalies.push(ele)
                    })

                    this.temporalies.push(element.operacion)
                    
                    if(element.operacion.estatus == 'OK') {
                        this.operactions.push(element.operacion)
                    }
                })
            },
            filterDefinitive() {
                let operations   = JSON.parse(JSON.stringify(this.operactions))
                this.definitives = []
                operations.forEach((element) => {
                    delete element.estatus
                    if(element.instrumento != 'IMPUESTO') {
                        this.definitives.push(element)
                    }
                })
            }
        },
        watch: {
            temporalies() {
                this.jsonData = this.temporalies
            }
        },
        computed: {
            globalAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                if(definitives.length > 0) {
                    total = definitives.reduce((a, b) => ({monto: Number(a.monto) + Number(b.monto)}));
                    total = total.monto
                }
                return total
            },
            taxAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                if(definitives.length > 0) {
                    total = definitives.reduce((a, b) => ({impuesto: Number(a.impuesto) + Number(b.impuesto)}));
                    total = total.impuesto
                }
                return total
            },
            checkAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                if(definitives) {
                    let transCheck = definitives.filter((element) => element.concepto == '0010')
                    total = transCheck.length
                }
                return total
            },
            checkSumAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                let transCheck = definitives.filter((element) => element.concepto == '0010')
                if(transCheck.length > 0) {
                    total = transCheck.reduce((a, b) => ({monto: Number(a.monto) + Number(b.monto)}));
                    total = total.monto
                } 
                return total
            },
            checkSumTaxAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                let transCheck = definitives.filter((element) => element.concepto == '0010')
                if(transCheck.length > 0) {
                    total = transCheck.reduce((a, b) => ({impuesto: Number(a.impuesto) + Number(b.impuesto)}));
                    total = total.impuesto
                }
                return total
            },
            dbAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                let transDb = definitives.filter((element) => element.concepto == '0001')
                total = transDb.length
                return total
            },
            dbSumAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                let transDb = definitives.filter((element) => element.concepto == '0001')
                if(transDb.length > 0) {
                    total = transDb.reduce((a, b) => ({monto: Number(a.monto) + Number(b.monto)}));
                    total = total.monto
                } 
                return total
            },
            dbSumTaxAmount() {
                let total = 0
                let definitives = Object.values(this.definitives)
                let transDb = definitives.filter((element) => element.concepto == '0001')
                if(transDb.length > 0) {
                    total = transDb.reduce((a, b) => ({impuesto: Number(a.impuesto) + Number(b.impuesto)}));
                    total = total.impuesto
                }
                return total
            }
        }
    }
</script>