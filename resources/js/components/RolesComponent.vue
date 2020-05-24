<template>
    <div>
        <v-data-table
            fixed-header
            show-select
            @input="selectAll"
            :headers="headers"
            :items="roles.data"
            sort-by="calories"
            class="elevation-1"
            item-key="id"
            @pagination="paginate"
            :server-items-length="roles.total"
            :loading="loading"
            loading-text="Loading... Please wait"
            :footer-props="{
                'items-per-page-options': [5, 10, 15],
                'items-per-page-text': 'Roles per page',
                'show-first-last-page': true,
                'show-current-page': true
            }"
        >
            <template v-slot:top>
                <v-toolbar flat color="secondary ">
                    <v-toolbar-title>User Roles</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-btn
                        transition="slide-x-transition"
                        v-if="selectedRow.length > 0"
                        class="text-capitalize"
                        small
                        color="error"
                        @click="openModal"
                        >Delete Selected</v-btn
                    >
                    <v-spacer></v-spacer>
                    <v-text-field
                        class="mt-5 px-4"
                        label="Search..."
                        @input="search"
                        error
                    ></v-text-field>
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn
                                color="gray darken-4"
                                dark
                                class="mb-2"
                                v-on="on"
                                >New Role</v-btn
                            >
                        </template>
                        <v-form lazy-validation v-model="valid" ref="roleForm">
                            <v-card>
                                <v-card-title>
                                    <span class="subheader">{{
                                        formTitle
                                    }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    autofocus
                                                    :rules="roleRules"
                                                    error
                                                    @keyup.enter="save"
                                                    v-model="editedItem.name"
                                                    label="Name"
                                                    prepend-icon="mdi-rename-box"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="secondary darken-1"
                                        text
                                        @click="close"
                                        >Cancel</v-btn
                                    >
                                    <v-btn
                                        color="error darken-1"
                                        text
                                        @click="save"
                                        >Save</v-btn
                                    >
                                </v-card-actions>
                            </v-card>
                        </v-form>
                    </v-dialog>
                </v-toolbar>

                <v-dialog
                    v-model="loadingloder"
                    hide-overlay
                    persistent
                    width="300"
                >
                    <v-card :color="LoaderColor" dark>
                        <v-card-text>
                            {{ saveTextLoader }}
                            <v-progress-linear
                                indeterminate
                                color="white"
                                class="mb-0"
                            ></v-progress-linear>
                        </v-card-text>
                    </v-card>
                </v-dialog>

                <v-snackbar :class="snackbarClass" v-model="snackbar">
                    {{ text }}
                    <v-btn small icon dark text @click="snackbar = false">
                        <v-icon class="white--text">mdi-close-circle</v-icon>
                    </v-btn>
                </v-snackbar>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon small class="indigo--text mr-2" @click="editItem(item)"
                    >mdi-pencil</v-icon
                >
                <v-icon small class="red--text" @click="openModal(item)"
                    >mdi-delete</v-icon
                >
            </template>
        </v-data-table>

        <v-dialog v-model="deleteItemDialog" max-width="500px">
            <v-card>
                <v-card-title>
                    <span class="subheader">{{ deleteModalTitle }}</span>
                </v-card-title>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        class="text-capitalize"
                        text
                        @click="deleteItemDialog = false"
                        >Cancel</v-btn
                    >
                    <v-btn
                        color="error darken-1"
                        class="text-capitalize"
                        text
                        @click="deleteItem"
                        >Yes! Delete</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data: () => ({
        deleteModalTitle: "Delete role?",
        selectedRow: [],
        selectedRowIndexForDelete: [],
        searchIt: "",
        valid: true,
        roleRules: [
            v => !!v || "Required",
            v => v.length > 2 || "Too sort",
            v => v.length < 12 || "Too long"
        ],
        LoaderColor: "",
        toDelete: "",
        deleteItemDialog: false,
        saveTextLoader: "",
        loadingloder: false,
        snackbarClass: "",
        snackbar: false,
        text: "",
        loading: false,
        dialog: false,
        headers: [
            {
                text: "#",
                align: "start",
                sortable: false,
                value: "id"
            },
            { text: "Name", value: "name" },
            { text: "Created at", value: "created_at" },
            { text: "Updated at", value: "updated_at" },
            { text: "Actions", value: "actions", sortable: false }
        ],
        roles: [],
        editedIndex: -1,
        editedItem: {
            name: ""
        },
        defaultItem: {
            name: ""
        }
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New Role" : "Edit Role";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.loading = true;
    },

    methods: {
        selectAll(v) {
            this.selectedRowIndexForDelete = [];
            this.selectedRow = v.map(e => e.id);
            v.forEach(row => {
                this.selectedRowIndexForDelete.push(
                    this.roles.data.indexOf(row)
                );
            });
        },
        search(v) {
            if (v.length > 2) {
                this.searchIt = v;
                this.paginate({ itemsPerPage: 5, page: 1 });
            }

            if (v.length === 0) {
                this.searchIt = "";
                this.paginate({ itemsPerPage: 10, page: 1 });
            }
        },

        paginate(e) {
            axios
                .get("/api/roles?page=" + e.page, {
                    params: { per_page: e.itemsPerPage, search: this.searchIt }
                })
                .then(res => {
                    this.roles = res.data.role;
                    this.loading = false;
                })
                .catch(err => {
                    if (err.response.status === 401) {
                        this.$router.push("/login");
                    }
                });
        },

        editItem(item) {
            this.editedIndex = this.roles.data.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        openModal(item) {
            if (!item.id) {
                this.deleteModalTitle = "Delete selected roles?";
                this.deleteItemDialog = true;
                return;
            }
            this.deleteModalTitle = "Delete role?";
            this.selectedRow = [];
            this.selectedRowIndexForDelete = [];
            this.deleteItemDialog = true;
            this.toDelete = item.id;
            this.editedIndex = this.roles.data.indexOf(item);
        },

        deleteItem() {
            this.deleteItemDialog = false;
            this.LoaderColor = "error";
            if (this.selectedRow.length == 0) {
                let deletedIndex = this.editedIndex;

                this.saveTextLoader = "Role deleting...Please wait";
                this.loadingloder = true;
                axios
                    .delete("/api/roles/delete/" + this.toDelete)
                    .then(res => {
                        this.roles.data.splice(deletedIndex, 1);
                        this.loadingloder = false;
                        this.snackbarClass = "red--text";
                        this.text = "Role Deleted!";
                        this.snackbar = true;
                        this.toDelete = "";
                    })
                    .catch(err => {
                        this.loadingloder = false;
                        this.snackbarClass = "white--text";
                        this.text = "Role Delete failed!";
                        this.snackbar = true;
                        console.dir(err);
                        this.toDelete = "";
                    });
            } else {
                this.saveTextLoader = "Deleting selected...Please wait";
                this.loadingloder = true;
                axios
                    .post("/api/roles/delete/selected", {
                        selectedRole: this.selectedRow
                    })
                    .then(res => {
                        this.selectedRow.forEach(value => {
                            this.roles.data = this.roles.data.filter(
                                v => v.id != value
                            );
                        });

                        this.selectedRowIndexForDelete = [];
                        this.selectedRow = [];
                        this.loadingloder = false;
                        this.snackbarClass = "red--text";
                        this.text = "Selected roles deleted!";
                        this.snackbar = true;
                    })
                    .catch(err => {
                        this.selectedRowIndexForDelete = [];
                        this.selectedRow = [];
                        this.loadingloder = false;
                        this.snackbarClass = "white--text";
                        this.text = "Delete failed!";
                        this.snackbar = true;
                    });
            }
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },

        save() {
            if (this.$refs.roleForm.validate()) {
                this.dialog = false;
                this.loadingloder = true;
                let editedItemIndex = this.editedIndex;
                if (this.editedIndex > -1) {
                    (this.LoaderColor = "info"),
                        (this.saveTextLoader = "Role updating...Please wait");
                    axios
                        .post("/api/roles/update/" + this.editedItem.id, {
                            name: this.editedItem.name
                        })
                        .then(res => {
                            this.roles.data.splice(
                                editedItemIndex,
                                1,
                                res.data.role
                            );
                            this.loadingloder = false;
                            this.snackbarClass = "info--text";
                            this.text = "Role Edited!";
                            this.snackbar = true;
                        })
                        .catch(err => {
                            this.loadingloder = false;
                            this.snackbarClass = "red--text";
                            this.text = "Role Edit Failed!";
                            this.snackbar = true;
                            console.dir(err);
                        });
                } else {
                    (this.LoaderColor = "primary"),
                        (this.saveTextLoader =
                            "New role creating...Please wait");
                    axios
                        .post("/api/roles", { name: this.editedItem.name })
                        .then(res => {
                            this.roles.data.push(res.data.role);
                            this.loadingloder = false;

                            this.snackbarClass = "indigo--text";
                            this.text = "Role added succesfully";
                            this.snackbar = true;
                        })
                        .catch(err => {
                            this.loadingloder = false;
                            this.snackbarClass = "red--text";
                            this.text = "Role add failed";
                            this.snackbar = true;
                        });
                }
            }
        }
    }
};
</script>
<style scoped></style>
