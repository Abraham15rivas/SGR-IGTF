<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav container">
                    <div class="row">
                        <div class="col-4">
                            <span>Log de actividades de los usuarios</span>
                        </div>
                        <div class="col-8 text-right">
                            <span v-if="user_activities">
                                <div class="input-group mb-3">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Filtrar por: Ip, Email, Descripcción ó rol" 
                                        aria-label="Filtro" 
                                        aria-describedby="basic-addon2"
                                        v-model="search"
                                    >
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table v-if="user_activities != null && user_activities.length > 0" class="table table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Rol</th>
                                <th>Dirección IP</th>
                                <th>Descripcción</th>
                                <th>Fecha y hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(activity, index) of user_activities" :key="index">
                                <td v-text="activity.user_id"></td>
                                <td v-text="activity.user.email"></td>
                                <td v-text="activity.user.role.name"></td>
                                <td v-text="activity.ip_address"></td>
                                <td v-text="activity.description"></td>
                                <td>{{ activity.created_at | captalize }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <span v-else class="text-center">
                        <h6>No hay datos para mostrar en esta tabla</h6>
                    </span>
                </div>
                <div class="card-footer text-right">
                    <!-- button>reporte pdf</button -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            logs: Array
        },
        data() {
            return {
                user_activities: null,
                search: null
            }
        },
        filters: {
            captalize(value) {
                let date_part = value.split('T')
                let date      = date_part[0]
                let hour      = date_part[1].substr(0, 8)
                return `${ date } / ${ hour }`
            }
        },
        watch: {
             search() {
                if (this.search == null || this.search == "" || this.search.length < 1) {
                    this.user_activities = this.logs
                } else {
                    let data = this.logs.filter((element) => {
                        return element.description.toLowerCase().includes(this.search.toLowerCase()) ||
                            element.ip_address.toLowerCase().includes(this.search.toLowerCase()) ||
                            element.user.email.toLowerCase().includes(this.search.toLowerCase()) ||
                            element.user.role.name.toLowerCase().includes(this.search.toLowerCase())
                    })                
                    this.user_activities = data
                }          
            }
        },
        mounted() {
            this.user_activities = this.logs
        }
    }
</script>