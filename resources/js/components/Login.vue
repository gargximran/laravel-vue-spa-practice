<template>
    <v-app id="inspire">
        <v-content>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col cols="12" sm="8" md="4">
                        <v-card class="elevation-12">
                            <v-toolbar color="error" dark flat>
                                <v-toolbar-title>Login</v-toolbar-title>
                                <v-progress-linear
                                    :active="loading"
                                    :indeterminate="loading"
                                    absolute
                                    bottom
                                    background-color="info lighten-2"
                                    color="info"
                                ></v-progress-linear>
                            </v-toolbar>
                            <v-card-text>
                                <v-form
                                    ref="login"
                                    v-model="valid"
                                    :lazy-validation="false"
                                >
                                    <v-text-field
                                        color="error"
                                        label="Email"
                                        prepend-icon="mdi-account-circle"
                                        type="email"
                                        v-model="email"
                                        required
                                        :rules="emailRules"
                                        autofocus
                                        @keyup.prevent.enter="nextFocus"
                                    />

                                    <v-text-field
                                        color="error"
                                        id="password"
                                        autocomplete
                                        v-on:keyup.enter="login"
                                        :rules="passwordRules"
                                        label="Password"
                                        prepend-icon="mdi-lock"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        :append-icon="
                                            !showPassword
                                                ? 'mdi-eye-off'
                                                : 'mdi-eye'
                                        "
                                        @click:append="
                                            showPassword = !showPassword
                                        "
                                        v-model="password"
                                        required
                                    />
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    type="submit"
                                    :loading="btnloading"
                                    color="error"
                                    class="ml-4"
                                    @click="login"
                                    >Login</v-btn
                                >
                                <v-spacer />
                                <v-btn color="success" class="mr-3"
                                    >Register</v-btn
                                >
                            </v-card-actions>
                        </v-card>
                    </v-col>
                    <v-snackbar class="red--text" v-model="snackbar">
                        {{ text }}
                        <v-btn small icon dark text @click="snackbar = false">
                            <v-icon class="indigo--text"
                                >mdi-close-circle</v-icon
                            >
                        </v-btn>
                    </v-snackbar>
                </v-row>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
export default {
    name: "Login",
    props: {
        source: String
    },
    data() {
        return {
            btnloading: false,
            valid: false,
            showPassword: false,
            loading: false,
            email: "",
            password: "",
            snackbar: false,
            text: "",
            emailRules: [
                v => !!v || "E-mail is required",
                v =>
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                        v
                    ) || "E-mail must be valid"
            ],
            passwordRules: [v => !!v || "Password is required"]
        };
    },
    created() {
        this.$vuetify.theme.dark = false;
    },
    mounted() {
        this.text = localStorage.getItem("logout") ? "Logged out!" : "";
        this.snackbar = localStorage.getItem("logout") ? true : false;
        localStorage.removeItem("logout");
    },
    methods: {
        nextFocus: function() {
            if (event.target.nodeName === "INPUT") {
                let form = event.target.form;
                for (let i = 0; i < form.length; i++) {
                    if (event.target == form[i]) {
                        form.elements[i + 1].focus();
                        break;
                    }
                }
            }
        },
        login: function() {
            if (this.$refs.login.validate()) {
                this.btnloading = true;
                this.loading = true;

                axios
                    .post("/api/login", {
                        email: this.email,
                        password: this.password
                    })
                    .then(res => {
                        if (res.data) {
                            if (res.data.isAdmin && res.data.token) {
                                this.loading = false;
                                localStorage.setItem("token", res.data.token);
                                localStorage.setItem("logged", true);
                                this.btnloading = false;
                                this.loading = false;
                                return this.$router.push({ name: "Admin" });
                            } else {
                                this.text = "Please log in as admin";
                                this.btnloading = false;
                                this.loading = false;
                                return (this.snackbar = true);
                            }
                        }
                    })
                    .catch(err => {
                        this.btnloading = false;
                        this.text = err.response.data.message;
                        this.snackbar = true;
                        this.loading = false;
                    });
            }
        }
    }
};
</script>
