<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-form method="POST" id="billForm" @submit.prevent="addBill">
            <v-layout row wrap>
                <v-flex md8>
                    <v-card>
                        <v-toolbar flat color="deep-purple" class="white--text" dense>
                            <v-toolbar-title>
                                New Bill
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-toolbar-items>
                                <v-btn flat to="/">
                                    <v-icon>arrow_back</v-icon>
                                    <span>Go Back</span>
                                </v-btn>
                            </v-toolbar-items>
                        </v-toolbar>
                        <v-container>
                            <v-text-field label="Bill Name" v-model="name" />
                            <v-textarea label="Bill Description" v-model="description" />
                            <v-text-field label="Bill Amount" v-model="amount" />
                            <v-text-field label="Day Due" v-model="day" />
                        </v-container>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn type="submit" dark color="deep-purple">Save</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-flex>
                <v-flex md4>
                    <v-card>
                        <v-toolbar flat color="light-green" class="white--text" dense>
                            <v-toolbar-title>
                                Month(s) Due (Optional)
                            </v-toolbar-title>
                        </v-toolbar>
                        <v-container>
                            <div class="text-xs-left caption grey--text">
                                Only use these options if your bill DOES NOT occur EVERY month.
                                If one or more of these options are selected, the bill will ONLY be displayed during the month(s) specified.
                            </div>
                            <br>
                            <v-layout row wrap>
                                <v-flex md6>
                                    <v-checkbox v-model="january" color="deep-purple" label="January"></v-checkbox>
                                    <v-checkbox v-model="february" color="deep-purple" label="February"></v-checkbox>
                                    <v-checkbox v-model="march" color="deep-purple" label="March"></v-checkbox>
                                    <v-checkbox v-model="april" color="deep-purple" label="April"></v-checkbox>
                                    <v-checkbox v-model="may" color="deep-purple" label="May"></v-checkbox>
                                    <v-checkbox v-model="june" color="deep-purple" label="June"></v-checkbox>
                                </v-flex>
                                <v-flex md6>
                                    <v-checkbox v-model="july" color="deep-purple" label="July"></v-checkbox>
                                    <v-checkbox v-model="august" color="deep-purple" label="August"></v-checkbox>
                                    <v-checkbox v-model="september" color="deep-purple" label="September"></v-checkbox>
                                    <v-checkbox v-model="october" color="deep-purple" label="October"></v-checkbox>
                                    <v-checkbox v-model="november" color="deep-purple" label="November"></v-checkbox>
                                    <v-checkbox v-model="december" color="deep-purple" label="December"></v-checkbox>
                                </v-flex>
                            </v-layout>


                        </v-container>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-form>
    </v-container>
</template>

<script>
    export default {
        name: 'BillForm',
        data() {
            return {
                name: '',
                description: '',
                amount: '',
                day: '',
                january: '',
                february: '',
                march: '',
                april: '',
                may: '',
                june: '',
                july: '',
                august: '',
                september: '',
                october: '',
                november: '',
                december: ''
            }
        },
        methods: {
            addBill() {
                axios.post('/api/bills', { name, description, amount, day,
                    january, february, march, april, may, june,
                    july, august, september, october, november, december
                })
                .then(response => {

                })
                .catch(function (error) {

                })
            }
        }
    }
</script>

<style scoped>
.v-input--selection-controls {
    padding: 0 !important;
    margin: 0 !important;
}
</style>
