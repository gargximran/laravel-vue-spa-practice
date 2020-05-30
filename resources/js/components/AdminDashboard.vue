<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" app clipped>
            <v-list dense>
                <v-list-item
                    v-for="item in items"
                    :key="item.text"
                    link
                    router
                    :to="item.path"
                >
                    <v-list-item-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ item.text }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-subheader class="mt-4 grey--text text--darken-1"
                    >SUBSCRIPTIONS</v-subheader
                >
                <v-list>
                    <v-list-item v-for="item in items2" :key="item.text" link>
                        <v-list-item-avatar>
                            <img
                                :src="
                                    `https://randomuser.me/api/portraits/men/${item.picture}.jpg`
                                "
                                alt=""
                            />
                        </v-list-item-avatar>
                        <v-list-item-title v-text="item.text" />
                    </v-list-item>
                </v-list>
                <v-list-item class="mt-4" link>
                    <!-- <v-list-item-title class="grey--text text--darken-1"> -->
                    <v-switch v-model="theme" label="Switch Theme"></v-switch>
                    <!-- </v-list-item-title> -->
                </v-list-item>
                <v-list-item link @click="logout">
                    <v-list-item-action>
                        <v-icon color="red lighten-1">mdi-logout</v-icon>
                    </v-list-item-action>
                    <v-list-item-title class="grey--text text--darken-1"
                        >Logout</v-list-item-title
                    >
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app clipped-left dense>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-avatar size="25" tile color="secondary" class="mr-1">
                <img src="/gxi.png" alt="alt" />
            </v-avatar>
            <v-toolbar-title class="mr-12 align-center">
                <span class="title"> - LaraVue</span>
            </v-toolbar-title>
        </v-app-bar>

        <v-content>
            <v-container fluid>
                <v-row no-gutters>
                    <v-col>
                        <router-view></router-view>

                        <v-snackbar color="cyan darken-2" v-model="snackbar">
                            Logged in succesfully!
                            <v-btn
                                small
                                icon
                                dark
                                text
                                @click="snackbar = false"
                            >
                                <v-icon class="black--text text--lighten-2"
                                    >mdi-close-circle</v-icon
                                >
                            </v-btn>
                        </v-snackbar>
                    </v-col>
                </v-row>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
export default {
    props: {
        source: String
    },
    data: () => ({
        theme: true,
        drawer: null,
        items: [
            { icon: "mdi-account", text: "Users", path: "/admin/users" },
            { icon: "mdi-teach", text: "Roles", path: "/admin/roles" }
        ],
        items2: [
            { picture: 28, text: "Joseph" },
            { picture: 38, text: "Apple" },
            { picture: 48, text: "Xbox Ahoy" },
            { picture: 58, text: "Nokia" },
            { picture: 78, text: "MKBHD" }
        ],
        snackbar: false
    }),
    watch: {
        theme: function(old, newval) {
            this.$vuetify.theme.dark = old;
        }
    },
    created() {
        this.$vuetify.theme.dark = true;
    },
    mounted() {
        this.snackbar = localStorage.getItem("logged") ? true : false;
        localStorage.removeItem("logged");
    },
    methods: {
        logout() {
            let tk = localStorage.getItem("token").split("|");

            axios
                .get("/api/logout/" + tk[0])
                .then(res => {
                    localStorage.removeItem("token");
                    localStorage.removeItem("logged");
                    localStorage.setItem("logout", true);
                    return this.$router.push({ name: "Login" });
                })
                .catch(err => {
                    localStorage.removeItem("token");
                    localStorage.setItem("logout", true);
                    return this.$router.push({ name: "Login" });
                });
        }
    }
};
</script>
