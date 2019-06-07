<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex md4>
                <v-card color="light-green" class="action white--text" @click="dialog=true">
                    <v-card-text>
                        <div class="title">
                            Monthly Income
                        </div>
                        <div class="display-1">
                            <span v-if="monthlyIncome == ''">0</span>
                            <span v-else>{{ monthlyIncome }}</span>
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md4>
                <v-card color="light-green" class="action white--text">
                    <v-card-text>
                        <div class="title">
                            Bi-Weekly Income
                        </div>
                        <div class="display-1">
                            {{ biweeklyIncome }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex md4>
                <v-card color="light-green" class="actino white--text">
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
                <v-data-table
                    :headers="headers"
                    :items="bills"
                    :pagination.sync="pagination"
                    hide-actions
                    class="elevation-1"
                    disable-initial-sort
                    no-data-text="Sorry, you have not added any bills yet!"
                    search

                >
                    <template v-slot:items="props" >
                        <td>{{ props.item.name }}</td>
                        <td>{{ props.item.due }}</td>
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
                        <div class="subheading">
                            $2,000.00 / $12,000.00 (YTD)
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
                        <div class="subheading">
                            $1,500.00 / $9,000.00 (YTD)
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
                        <div class="subheading">
                            $500.00 / $3,000.00 (YTD)
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" max-width="300px">
            <v-card>
                <v-toolbar flat color="deep-purple" class="white--text">
                    <v-toolbar-title>Monthly Income</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-text-field label="Income" v-model="monthlyIncome" required></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn dark color="deep-purple" @click="dialog = false">Save</v-btn>
                    <v-btn dark color="light-green" @click="dialog = false">Close</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
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
                monthlyIncome: '0',
                pagination: {
                    sortBy: 'due',
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
                        value: 'due'
                    },
                    {
                        text: 'Amount',
                        align: 'left',
                        value: 'amount'
                    },
                ],
                bills: [
                    {
                        name: 'Cable/Internet',
                        due: '01',
                        amount: 115
                    },
                    {
                        name: 'Netflix',
                        due: '05',
                        amount: 15
                    },
                    {
                        name: 'Vehicle Loan',
                        due: '10',
                        amount: 300
                    },
                    {
                        name: 'Cellphone',
                        due: '15',
                        amount: 120
                    },
                    {
                        name: 'Student Loan',
                        due: '20',
                        amount: 200
                    },
                    {
                        name: 'Utilities',
                        due: '25',
                        amount: 250
                    },
                    {
                        name: 'Mortgage',
                        due: '01',
                        amount: 500
                    },
                ]
            }
        },
        computed: {
            biweeklyIncome: function() {
                return this.monthlyIncome / 2;
            },
            weeklyIncome: function() {
                return this.monthlyIncome / 4;
            }
        },
    }
</script>
