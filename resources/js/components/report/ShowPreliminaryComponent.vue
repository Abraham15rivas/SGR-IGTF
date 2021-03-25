<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <vue-excel-editor 
                    v-model="jsondata" 
                    autocomplete 
                    filter-row
                ></vue-excel-editor>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['preliminaries'],
        data() {
            return {
                jsondata: []
            }
        },
        mounted() {
            this.jsondata = JSON.parse(JSON.stringify(this.preliminaries))
        }
    }
</script>
