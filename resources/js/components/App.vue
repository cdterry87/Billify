<template>
    <div>
        <v-app class="inspire">
            <v-toolbar color="deep-purple" dark tabs >
                <v-toolbar-title>
                    <a href="/">
                        <v-icon>attach_money</v-icon>
                        <span>Billify</span>
                    </a>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-toolbar-items>
                    <v-btn flat>
                        <v-icon>pie_chart</v-icon>
                    </v-btn>
                    <v-btn flat>
                        <v-icon>notifications</v-icon>
                    </v-btn>
                    <v-menu :nudge-width="100" offset-y>
                        <template v-slot:activator="{ on }">
                            <v-btn flat v-on="on">
                                <v-icon>account_circle</v-icon>
                                <v-icon dark>arrow_drop_down</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-tile>
                                <v-list-tile-title>My Account</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="logout">
                                <v-list-tile-title>Logout</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-toolbar-items>
            </v-toolbar>

            <v-content>
                <router-view></router-view>

                <v-container fluid grid-list-md text-xs-center>
                    <v-layout row wrap>
                        <v-flex md4>
                            <v-card color="deep-purple" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Monthly Income
                                    </div>
                                    <div class="display-1">
                                        $2,000.00
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex md4>
                            <v-card color="light-green" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Bi-Weekly Income
                                    </div>
                                    <div class="display-1">
                                        $1,000.00
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex md4>
                            <v-card color="deep-purple" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Weekly Income
                                    </div>
                                    <div class="display-1">
                                        $500.00
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex>
                            <v-card>

                                <v-toolbar class="white--text" flat color="deep-purple">
                                    <v-toolbar-title>My Monthly Bills</v-toolbar-title>

                                    <v-spacer></v-spacer>

                                    <v-toolbar-items>
                                        <v-btn flat class="white--text">
                                            <v-icon>add</v-icon>
                                            <span>New Bill</span>
                                        </v-btn>
                                    </v-toolbar-items>
                                </v-toolbar>
                                <v-card-text>
                                    <v-data-table
                                        :headers="headers"
                                        :items="bills"
                                        :pagination.sync="pagination"
                                        hide-actions
                                    >
                                        <template v-slot:no-data>
                                            <v-alert :value="true" color="error" icon="warning">
                                                Sorry, you have not added any bills yet!
                                            </v-alert>
                                        </template>
                                        <template v-slot:items="props">
                                            <td>{{ props.item.name }}</td>
                                            <td>{{ props.item.due }}</td>
                                            <td>{{ props.item.amount }}</td>
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex md4>
                            <v-card color="light-green" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Total Income
                                    </div>
                                    <div class="title">
                                        $2,000.00 / $12,000.00 (YTD)
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex md4>
                            <v-card color="deep-purple" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Total Payments
                                    </div>
                                    <div class="title">
                                        $1,500.00 / $9,000.00 (YTD)
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                        <v-flex md4>
                            <v-card color="light-green" class="white--text">
                                <v-card-text>
                                    <div class="subheading">
                                        Total Remainder
                                    </div>
                                    <div class="title">
                                        $500.00 / $3,000.00 (YTD)
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>

                <div id="footer" class="text-xs-center">
                    &copy; Chase Terry 2019
                </div>

                <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
                    {{ snackbar.message }}
                    <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
                </v-snackbar>
            </v-content>
        </v-app>
    </div>
</template>

<script>
    export default {
        name: 'App',
        data() {
            return {
                snackbar: {
                    enabled: true,
                    message: 'This is a sample message.',
                    timeout: 5000,
                    y: 'bottom',
                    x: 'right',
                    color: 'success'
                },
                pagination: {
                    sortBy: 'due'
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
                        amount: '$115.00'
                    },
                    {
                        name: 'Netflix',
                        due: '05',
                        amount: '$15.00'
                    },
                    {
                        name: 'Vehicle Loan',
                        due: '10',
                        amount: '$300.00'
                    },
                    {
                        name: 'Cellphone',
                        due: '15',
                        amount: '$120.00'
                    },
                    {
                        name: 'Student Loan',
                        due: '20',
                        amount: '$200.00'
                    },
                    {
                        name: 'Utilities',
                        due: '25',
                        amount: '$250.00'
                    },
                    {
                        name: 'Mortgage',
                        due: '01',
                        amount: '$500.00'
                    },
                ]
            }
        },
        methods: {
            logout() {
                axios.get('/api/logout')
                .then(function () {
                    location.reload()
                });
            }
        }
    }
</script>

<style>
table td {
    text-align: left !important;
}

#footer {
    margin-bottom: 1.5rem;
}
</style>
