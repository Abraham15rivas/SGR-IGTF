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
    export default {
        props: {
            data: Array,
        },
        data() {
            return {
                options: ['spline', 'line', 'bar', 'pie', 'column'],
                user: this.data[0].user,
                months: this.data[0].months
            }
        },
        computed: {
            chartOptions() { 
                return {
                    chart: { 
                        type: 'pie' 
                    },
                    title: { 
                        text: `Total de usuarios registrados: ${ this.user.user_totals }` 
                    },
                    series: [{
                        name: 'Usuarios',
                        colorByPoint: true,
                        data: [
                            {
                                name: 'Conectados al sistema',
                                y: this.user.sessions_active,
                                sliced: true,
                                selected: true
                            }, 
                            {
                                name: 'Desconectados del sistema',
                                y: this.user.sessions_deactivate,
                            },
                            {
                                name: 'Habilitados',
                                y: this.user.user_enabled,
                            },
                            {
                                name: 'Deshabilitados',
                                y: this.user.user_disabled,
                            },
                        ]
                    }]
                }
            },
            chartOptions2() {
                return {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: `Transacciones mensuales`
                    },
                    subtitle: {
                        text: `Total de transacciones: ${ this.months.total }`
                    },
                    xAxis: {
                        categories: [
                            'Jan',
                            'Feb',
                            'Mar',
                            'Apr',
                            'May',
                            'Jun',
                            'Jul',
                            'Aug',
                            'Sep',
                            'Oct',
                            'Nov',
                            'Dec'
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Cantidad'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: '2017',
                        data: [
                            this.months.name.enero,
                            this.months.name.febrero,
                            this.months.name.marzo,
                            this.months.name.abril,
                            this.months.name.mayo,
                            this.months.name.junio,
                            this.months.name.julio,
                            this.months.name.agosto,
                            this.months.name.septiembre,
                            this.months.name.obtubre,
                            this.months.name.noviembre,
                            this.months.name.diciembre
                        ]
                    }]
                }
            }
        },
    }
</script>