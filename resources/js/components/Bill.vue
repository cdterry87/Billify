<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-form method="POST" id="billForm" @submit.prevent="saveBill">
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
                            <v-text-field label="Bill Name" color="deep-purple" v-model="bill.name" required />
                            <v-textarea label="Bill Description" color="deep-purple" v-model="bill.description" />
                            <v-text-field label="Bill Amount" color="deep-purple" v-model="bill.amount" maxlength="9" required />
                            <v-text-field label="Day Due" color="deep-purple" v-model="bill.day" maxlength="2" required />
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
                                    <v-checkbox v-model="bill.january" color="light-green" label="January"></v-checkbox>
                                    <v-checkbox v-model="bill.february" color="light-green" label="February"></v-checkbox>
                                    <v-checkbox v-model="bill.march" color="light-green" label="March"></v-checkbox>
                                    <v-checkbox v-model="bill.april" color="light-green" label="April"></v-checkbox>
                                    <v-checkbox v-model="bill.may" color="light-green" label="May"></v-checkbox>
                                    <v-checkbox v-model="bill.june" color="light-green" label="June"></v-checkbox>
                                </v-flex>
                                <v-flex md6>
                                    <v-checkbox v-model="bill.july" color="light-green" label="July"></v-checkbox>
                                    <v-checkbox v-model="bill.august" color="light-green" label="August"></v-checkbox>
                                    <v-checkbox v-model="bill.september" color="light-green" label="September"></v-checkbox>
                                    <v-checkbox v-model="bill.october" color="light-green" label="October"></v-checkbox>
                                    <v-checkbox v-model="bill.november" color="light-green" label="November"></v-checkbox>
                                    <v-checkbox v-model="bill.december" color="light-green" label="December"></v-checkbox>
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
    import Event from './../events'

    export default {
        name: 'BillForm',
        props: ['id'],
        data() {
            return {
                bill: {
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
                    december: '',
                }
            }
        },
        methods: {
            getBill(id) {
                axios.get('/api/bills/' + id)
                .then(response => {
                    this.bill = response.data
                })
            },
            saveBill() {
                let name = this.bill.name
                let description = this.bill.description
                let amount = this.bill.amount
                let day = this.bill.day
                let january = this.bill.january
                let february = this.bill.february
                let march = this.bill.march
                let april = this.bill.april
                let may = this.bill.may
                let june = this.bill.june
                let july = this.bill.july
                let august = this.bill.august
                let september = this.bill.september
                let october = this.bill.october
                let november = this.bill.november
                let december = this.bill.december
                let month = this.month

                let bill_id = this.id
                let method = 'put'

                if (_.isUndefined(bill_id)) {
                    bill_id = ''
                    method = 'post'
                }

                axios({
                    method: method,
                    url: '/api/bills/' + bill_id,
                    data: {
                        name, description, amount, day, month,
                        january, february, march, april, may, june, july,
                        august, september, october, november, december,
                    }
                })
                .then(response => {
                    if (method == 'post') {
                        this.redirect()
                    }
                    Event.$emit('successMessage', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('errorMessage', 'Bill could not be saved at this time.  Please try again later')
                })
            },
            redirect() {
                this.$router.push('/')
            },
        },
        computed: {
            month() {
                if (this.bill.january || this.bill.february || this.bill.march ||
                this.bill.april || this.bill.may || this.bill.june || this.bill.july ||
                this.bill.august || this.bill.september || this.bill.october ||
                this.bill.november || this.bill.december) {
                    return true
                }
                return false
            }
        },
        created() {
            if (!_.isUndefined(this.id)) {
                this.getBill(this.id)
            }
        }
    }
</script>
