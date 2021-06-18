<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-bav-ligth">
                <div class="card-header card-header-bav">Graficas</div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <highcharts :options="chartOptions"></highcharts>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <highcharts :options="chartOptions2"></highcharts>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                 <!-- code -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import { mapActions, mapGetters } from 'vuex'
    export default {
        props: {
            data: Array
        },
        data() {
            return {
                options: ['spline', 'line', 'bar', 'pie', 'column'],
            }
        },
        computed: {
            chartOptions() { 
                return {
                        chart: { 
                            type: 'pie' 
                        },
                        title: { 
                            text: `Total de usuarios registrados: ${ this.data[0].user_totals }` 
                        },
                        series: [{
                            name: 'Usuarios',
                            colorByPoint: true,
                            data: [
                                {
                                    name: 'Conectados al sistema',
                                    y: this.data[0].sessions_active,
                                    sliced: true,
                                    selected: true
                                }, 
                                {
                                    name: 'Desconectados del sistema',
                                    y: this.data[0].sessions_deactivate,
                                },
                                {
                                    name: 'Habilitados',
                                    y: this.data[0].user_enabled,
                                },
                                {
                                    name: 'Deshabilitados',
                                    y: this.data[0].user_disabled,
                                },
                            ]
                        }]
                }
            },
            chartOptions2() {
                return {
                        chart: { type: 'column' },
                        title: { text: 'Por definir' },
                        series: [
                            {
                                data: [15, 3, 6, 2, 6, 4, 5, 5]
                            },
                            {
                                data: [12, 1, 4, 3, 8, 1, 3, 7]
                            },
                        ]
                }
            }
        },
    }
</script>