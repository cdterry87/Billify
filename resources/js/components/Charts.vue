<template>
    <div>
        <v-container fluid grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex md6>
                    <v-card>
                        <canvas id="IncomeVsPayments" class="chart" height="500"></canvas>
                    </v-card>
                </v-flex>
                <v-flex md6>
                    <v-card>
                        <canvas id="PaymentCategories" class="chart" height="500"></canvas>
                    </v-card>
                </v-flex>
                <v-flex xs12>
                    <v-card>
                        <canvas id="Daily" class="chart" height="500"></canvas>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    import Chart from 'chart.js'

    export default {
        name: 'Charts',
        methods: {
            incomeVsPayments() {
                axios.get('/api/charts/incomevspayments')
                .then(response => {
                    let data = response.data

                    var ctx = document.getElementById('IncomeVsPayments');
                    var IncomeVsPayments = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Income', 'Payments'],
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#673AB7',
                                    '#8BC34A',
                                ],
                            }]
                        },
                        options: {
                            title: {
                                display: true,
                                text: 'Income Vs Payments'
                            },
                        }
                    })
                })
            },
            paymentCategories() {
                axios.get('/api/charts/paymentcategories')
                .then(response => {
                    let labels = []
                    let data = []

                    response.data.forEach(function(obj) {
                        labels.push(obj.name)
                        data.push(obj.amount)
                    });

                    var ctx = document.getElementById('PaymentCategories');
                    var PaymentCategories = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#673AB7',
                                    '#8BC34A',
                                    '#673AB7',
                                    '#8BC34A',
                                    '#673AB7',
                                    '#8BC34A',
                                    '#673AB7',
                                    '#8BC34A',
                                ],
                            }]
                        },
                        options: {
                            title: {
                                display: true,
                                text: 'Payment Categories'
                            },
                        }
                    })
                })
            },
            daily() {
                axios.get('/api/charts/daily')
                .then(response => {
                    let data = response.data

                    var ctx = document.getElementById('Daily');
                    var Daily = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
                            datasets: [{
                                label: 'Payments This Month',
                                backgroundColor: '#673AB7',
                                data: data,
                            }]
                        },
                        options: {
                            tooltips: {
                                enabled: false,
                            },
                            title: {
                                display: true,
                                text: 'Payments by Day of the Month'
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        label: 'Payment Amount'
                                    }
                                }]
                            }
                        }
                    })
                })
            }
        },
        mounted() {
            this.incomeVsPayments()
            this.paymentCategories()
            this.daily()
        }
    }
</script>
