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
                            <v-text-field label="Bill Name" color="deep-purple" v-model="name" required />
                            <v-textarea label="Bill Description" color="deep-purple" v-model="description" />
                            <v-text-field label="Bill Amount" color="deep-purple" v-model="amount" maxlength="10" required />
                            <v-text-field label="Day Due" color="deep-purple" v-model="day" maxlength="2" required />
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
                                    <v-checkbox v-model="january" color="light-green" label="January"></v-checkbox>
                                    <v-checkbox v-model="february" color="light-green" label="February"></v-checkbox>
                                    <v-checkbox v-model="march" color="light-green" label="March"></v-checkbox>
                                    <v-checkbox v-model="april" color="light-green" label="April"></v-checkbox>
                                    <v-checkbox v-model="may" color="light-green" label="May"></v-checkbox>
                                    <v-checkbox v-model="june" color="light-green" label="June"></v-checkbox>
                                </v-flex>
                                <v-flex md6>
                                    <v-checkbox v-model="july" color="light-green" label="July"></v-checkbox>
                                    <v-checkbox v-model="august" color="light-green" label="August"></v-checkbox>
                                    <v-checkbox v-model="september" color="light-green" label="September"></v-checkbox>
                                    <v-checkbox v-model="october" color="light-green" label="October"></v-checkbox>
                                    <v-checkbox v-model="november" color="light-green" label="November"></v-checkbox>
                                    <v-checkbox v-model="december" color="light-green" label="December"></v-checkbox>
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
                let name = this.name
                let description = this.description
                let amount = this.amount
                let day = this.day
                let january = this.january
                let february = this.february
                let march = this.march
                let april = this.april
                let may = this.may
                let june = this.june
                let july = this.july
                let august = this.august
                let september = this.september
                let october = this.october
                let november = this.november
                let december = this.december

                axios.post('/api/bills', { name, description, amount, day,
                    january, february, march, april, may, june, july,
                    august, september, october, november, december
                })
                .then(response => {
                    this.redirect()
                })
                .catch(function (error) {

                })
            },
            redirect() {
                this.$router.push('/')
            },
        }
    }
</script>

<style scoped>
.v-input--selection-controls {
    padding: 0 !important;
    margin: 0 !important;
}
</style>
