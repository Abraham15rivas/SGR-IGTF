<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <button :class="[seleted != null ? 'btn btn-danger' : 'btn btn-danger disabled']" @click="delRecord">Borrar</button>
            <button class="btn btn-success" @click="setOperation">Operaciones</button>
            <div class="card">
                <vue-excel-editor
                    ref="grid"
                    @select="onSelect"
                    v-model="jsondata" 
                    autocomplete 
                    filter-row
                    delete
                ></vue-excel-editor>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    export default {
        props: ['temporary'],
        data() {
            return {
                seleted: null,
                jsondata: []
            }
        },
        methods: {
            delRecord() {
                if(this.seleted) {
                    this.$refs.grid.deleteRecord(this.seleted)
                    this.seleted = null
                }
            },
            onSelect(selectedRows) {
                this.seleted = selectedRows
            },
            async setOperation() {
                try {
                    const url  = `/store/excel/operation`
                    let params = {
                        transactions: this.jsondata
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
                    }
                } catch (error) {
                    console.log(error)
                }
            }
        },
        mounted() {
            this.jsondata = JSON.parse(JSON.stringify(this.temporary))
        }
    }
</script>
