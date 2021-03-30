<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Todos los usuarios registrados</div>
                <div class="card-body">
                    <ul>
                        <li v-for="(user, index) of list_user" :key="index">
                            {{ user }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['users'],
        data() {
            return {
                list_user: []
            }
        },
        mounted() {
            this.list_user = this.users
        }
    }
</script>