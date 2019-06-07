<template>
    <div>
        <v-app class="inspire">
            <v-toolbar color="deep-purple" dark tabs dense>
                <v-toolbar-title>
                    <router-link to="/" class="search-link">
                        <v-icon>attach_money</v-icon>
                        <span>Billify</span>
                    </router-link>
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
                    color: 'dark-grey'
                },

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
