<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <vue-excel-editor 
                    v-model="jsondata" 
                    no-paging 
                    autocomplete 
                    filter-row
                ></vue-excel-editor>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                jsondata: []
            }
        }
    }
</script>
