<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex md3>
                <v-card color="deep-purple" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Yearly Income
                        </div>
                        <div class="display-1">
                            {{ yearlyIncome }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md3>
                <v-card color="deep-purple" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Monthly Income
                        </div>
                        <div class="display-1">
                            {{ monthlyIncome }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md3>
                <v-tooltip top color="deep-orange" debounce="2000">
                    <template v-slot:activator="{ on }">
                        <v-card color="light-green" class="action white--text" @click="dialog=true" v-on="on">
                            <v-card-text>
                                <div class="title">
                                    Bi-Weekly Income
                                </div>
                                <div class="display-1">
                                    <span v-if="biweeklyIncome == '' || !biweeklyIncome">0</span>
                                    <span v-else>{{ biweeklyIncome }}</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </template>
                    <span>
                        Click to specify your income.
                    </span>
                </v-tooltip>
            </v-flex>
            <v-flex md3>
                <v-card color="deep-purple" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Weekly Income
                        </div>
                        <div class="display-1">
                            {{ weeklyIncome }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout row>
            <v-flex>
                <v-toolbar class="white--text" flat color="deep-purple" dense>
                    <v-toolbar-title>My Monthly Bills</v-toolbar-title>

                    <v-spacer></v-spacer>

                    <v-toolbar-items>
                        <v-btn flat class="white--text" to="bill">
                            <v-icon>add</v-icon>
                            <span>New Bill</span>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-text-field
                    box
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    color="light-green"
                    background-color="light-green lighten-5"
                    single-line
                    hide-details
                    clearable
                ></v-text-field>
                <v-data-table
                    :headers="headers"
                    :items="bills"
                    :search="search"
                    :pagination.sync="pagination"
                    hide-actions
                    class="elevation-1"
                    no-data-text="Sorry, you have not added any bills yet!"
                >
                    <template v-slot:items="props" >
                        <td>{{ props.item.name }}</td>
                        <td>{{ props.item.day }}</td>
                        <td>{{ props.item.amount }}</td>
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>
        <v-layout row wrap class="mt-2">
            <v-flex xs12>
                <v-toolbar class="white--text" flat color="deep-purple" dense>
                    <v-toolbar-title>Summary</v-toolbar-title>
                </v-toolbar>
            </v-flex>
            <v-flex md4>
                <v-card color="light-green" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Total Income
                        </div>
                        <div class="headline">
                            {{ monthlyIncome }} / {{ yearlyIncome }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md4>
                <v-card color="light-green" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Total Payments
                        </div>
                        <div class="headline">
                            {{ totalBills }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md4>
                <v-card color="light-green" class="white--text">
                    <v-card-text>
                        <div class="title">
                            Total Remainder
                        </div>
                        <div class="headline">
                            {{ totalRemainder }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" max-width="300px">
            <v-card>
                <v-form method="POST" id="incomeForm" @submit.prevent="addIncome">
                    <v-toolbar flat color="deep-purple" class="white--text">
                        <v-toolbar-title>Bi-Weekly Income</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-text-field label="Bi-Weekly Income" color="deep-purple" v-model="biweeklyIncome" maxlength="15" required></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" dark color="deep-purple" @click="dialog = false">Save</v-btn>
                        <v-btn dark color="light-green" @click="dialog = false">Close</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    export default {
        name: 'Home',
        data() {
            return {
                dialog: false,
                search: '',
                valid: true,
                biweeklyIncome: '0',
                pagination: {
                    sortBy: 'day',
                    rowsPerPage: -1
                },
                headers: [
                    {
                        text: 'Name',
                        align: 'left',
                        value: 'name'
                    },
                    {
                        text: 'Due',
                        align: 'left',
                        value: 'day'
                    },
                    {
                        text: 'Amount',
                        align: 'left',
                        value: 'amount'
                    },
                ],
                user: [],
                bills: [],
            }
        },
        methods: {
            getUser() {
                axios.get('/api/user')
                .then(response => {
                    this.user = response.data
                    this.biweeklyIncome = this.user.income
                })
            },
            getBills() {
                axios.get('/api/bills')
                .then(response => {
                    this.bills = response.data
                })
            },
            addIncome() {
                let income = this.biweeklyIncome

                console.log('add income')

                axios.post('/api/income', { income })
                .then(response => {
                    console.log('hit income endpoint')
                })
                .catch(function (error) {

                })
            }
        },
        created() {
            this.getUser()
            this.getBills()
        },
        computed: {
            yearlyIncome: function() {
                return (this.biweeklyIncome * 26)
            },
            monthlyIncome: function() {
                return (this.biweeklyIncome * 2)
            },
            weeklyIncome: function() {
                return (this.biweeklyIncome / 2)
            },
            totalBills: function() {
                return _.sumBy(this.bills, function(o) { return parseInt(o.amount) })
            },
            totalRemainder: function() {
                return this.monthlyIncome - this.totalBills
            }
        },
    }
</script>
