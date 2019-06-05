<template>
    <div>
        <v-app class="inspire">
            <v-toolbar color="deep-purple" dark tabs >
                <v-toolbar-title>
                    <a href="/">Billify</a>
                </v-toolbar-title>

                <v-spacer></v-spacer>


                <v-toolbar-items class="hidden-sm-and-down">
                    <v-btn flat dark>
                        Notifications
                    </v-btn>
                    <v-menu bottom offset-y>
                        <template #activator="{ on: menu }">
                            <v-tooltip bottom>
                            <template #activator="{ on }">
                                <v-btn flat dark v-on="{ ...menu }">
                                    <span>User Account</span>
                                </v-btn>
                            </template>
                            </v-tooltip>
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
