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
                        <canvas id="Daily" class="chart" height="300"></canvas>
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
                var ctx = document.getElementById('IncomeVsPayments');
                var IncomeVsPayments = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Income', 'Payments'],
                        datasets: [{
                            data: [4000, 1850],
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
                });
            },
            paymentCategories() {
                var ctx = document.getElementById('PaymentCategories');
                var PaymentCategories = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Netflix', 'Student Loan', 'Cable/Internet', 'Cell Phone', 'Vehicle Loan', 'Utilities', 'Mortgage'],
                        datasets: [{
                            data: [15, 250, 150, 100, 350, 150, 850],
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
                });
            },
            daily() {
                var ctx = document.getElementById('Daily');
                var Daily = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [1, 5, 10, 15, 20, 25, 30],
                        datasets: [{
                            // fill: false,
                            label: 'Payments This Month',
                            backgroundColor: '#673AB7',
                            borderColor: '#8BC34A',
                            data: [
                                {
                                    x: 1,
                                    y: 15
                                }, {
                                    x: 5,
                                    y: 250
                                },
                                {
                                    x: 10,
                                    y: 150
                                }, {
                                    x: 15,
                                    y: 100
                                },
                                {
                                    x: 20,
                                    y: 350
                                }, {
                                    x: 25,
                                    y: 150
                                },
                                {
                                    x: 30,
                                    y: 850
                                },
                            ]
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Payments by Day of the Month'
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            }
        },
        mounted() {

            this.incomeVsPayments()
            this.paymentCategories()
            this.daily()
        }
    }
</script>
