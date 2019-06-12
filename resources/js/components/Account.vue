<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex md8>
                <v-card>
                    <v-form method="POST" id="userForm" @submit.prevent="updateUser">
                        <v-toolbar flat color="deep-purple" class="white--text" dense>
                            <v-toolbar-title>
                                Update Account
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
                            <v-text-field label="Name" color="deep-purple" v-model="user.name" required />
                            <v-text-field label="Email" color="deep-purple" v-model="user.email" required />
                            <v-text-field label="Bi-Weekly Income" color="deep-purple" v-model="user.income" maxlength="10" required />
                        </v-container>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn type="submit" dark color="deep-purple">Save</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-flex>
            <v-flex md4>
                <v-card>
                    <v-form method="POST" id="userForm" @submit.prevent="updateUser">
                        <v-toolbar flat color="light-green" class="white--text" dense>
                            <v-toolbar-title>
                                Change Password
                            </v-toolbar-title>
                        </v-toolbar>
                        <v-container>
                            <v-text-field label="Password" color="deep-purple" v-model="password" required />
                            <v-text-field label="Confirm Password" color="deep-purple" v-model="password_confirmation" required />
                        </v-container>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn type="submit" dark color="light-green">Change Password</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import event from './../events'

    export default {
        name: 'Account',
        data() {
            return {
                user: [],
                password: '',
                password_confirmation: ''
            }
        },
        methods: {
            getUser() {
                axios.get('/api/user')
                .then(response => {
                    this.user = response.data
                })
            },
            updateUser() {

            }
        },
        mounted() {
            this.getUser()
        }
    }
</script>
