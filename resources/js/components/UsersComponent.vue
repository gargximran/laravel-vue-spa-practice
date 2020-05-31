<template>
    <div>
        <v-data-table
            fixed-header
            show-select
            @input="selectAll"
            :headers="headers"
            :items="users.data"
            class="elevation-1"
            item-key="id"
            @pagination="paginate"
            :server-items-length="totalUserCount"
            :items-per-page="10"
            :loading="loading"
            loading-text="Loading... Please wait"
            :footer-props="{
                'items-per-page-options': [5, 10, 15],
                'items-per-page-text': 'Users per page',
                'show-first-last-page': true,
                'show-current-page': true
            }"
        >
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title>Users Details</v-toolbar-title>
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
                        secondary
                        append-icon="mdi-magnify"
                        @click:append="search"
                    ></v-text-field>
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="gray darken-4" class="mb-2" v-on="on"
                                >New User</v-btn
                            >
                        </template>
                        <v-form
                            v-if="editedIndex > -2"
                            lazy-validation
                            v-model="valid"
                            ref="userForm"
                        >
                            <v-card>
                                <v-card-title>
                                    <span class="subheader">{{
                                        formTitle
                                    }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="12" md="8">
                                                <v-text-field
                                                    @keyup.prevent.enter="
                                                        nextFocus
                                                    "
                                                    :rules="userNameRules"
                                                    v-model="editedItem.name"
                                                    label="Name"
                                                    type="text"
                                                    prepend-icon="mdi-rename-box"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="12" md="4">
                                                <v-select
                                                    v-model="editedItem.role"
                                                    :rules="UserRoleRules"
                                                    :items="userRole"
                                                    label="Role"
                                                    prepend-icon="mdi-teach"
                                                ></v-select>
                                            </v-col>
                                            <v-col cols="12" sm="12" md="12">
                                                <v-text-field
                                                    @keyup.prevent.enter="
                                                        editedIndex === -1
                                                            ? nextFocus()
                                                            : save()
                                                    "
                                                    type="email"
                                                    :error-messages="
                                                        emailNotValid
                                                    "
                                                    :rules="emailRules"
                                                    v-model="editedItem.email"
                                                    label="Email"
                                                    @input="emailValidity"
                                                    prepend-icon="mdi-email-edit-outline"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="12"
                                                md="12"
                                                v-if="editedIndex == -1"
                                            >
                                                <v-text-field
                                                    @keyup.prevent.enter="
                                                        nextFocus
                                                    "
                                                    label="Password"
                                                    :type="
                                                        showPass
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    :append-icon="
                                                        !showPass
                                                            ? 'mdi-eye-off'
                                                            : 'mdi-eye'
                                                    "
                                                    autocomplete="off"
                                                    :rules="passwordRules"
                                                    prepend-icon="mdi-lock-outline"
                                                    v-model="
                                                        editedItem.password
                                                    "
                                                    @click:append="
                                                        showPass = !showPass
                                                    "
                                                ></v-text-field>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="12"
                                                md="12"
                                                v-if="editedIndex == -1"
                                            >
                                                <v-text-field
                                                    label="Confirm Password"
                                                    prepend-icon="mdi-lock-clock"
                                                    autocomplete="off"
                                                    v-model="r_pass"
                                                    @keyup.prevent.enter="
                                                        save()
                                                    "
                                                    :type="
                                                        rshowPass
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    :append-icon="
                                                        !rshowPass
                                                            ? 'mdi-eye-off'
                                                            : 'mdi-eye'
                                                    "
                                                    :rules="[
                                                        cpasswordRules,
                                                        passwordRules[0]
                                                    ]"
                                                    @click:append="
                                                        rshowPass = !rshowPass
                                                    "
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
            </template>
            <template v-slot:item.role="{ item }">
                <v-edit-dialog
                    :return-value.sync="item.role"
                    large
                    cancel-text="Cancel"
                    save-text="Save"
                    @save="changeRole(item)"
                    persistent
                    transition="fade-transition"
                >
                    <template>{{ item.role }}</template>
                    <template v-slot:input>
                        <v-select
                            v-model="item.role"
                            :rules="UserRoleRules"
                            :items="userRole"
                            label="Change Role"
                            prepend-icon="mdi-teach"
                        ></v-select>
                    </template>
                </v-edit-dialog>
            </template>
            <template v-slot:item.id="{ item }">
                {{ indexCount(item) }}
            </template>
            <template v-slot:item.image="{ item }">
                <v-edit-dialog
                    :return-value.sync="item.image"
                    large
                    cancel-text="Cancel"
                    save-text="Save"
                    @cancel="(showImageReview = ''), (imageUpload = null)"
                    @save="uploadToServer(item)"
                    lazy
                    persistent
                    transition="fade-transition"
                >
                    <v-avatar size="30">
                        <img :src="item.image" />
                    </v-avatar>
                    <template v-slot:input>
                        <div class="d-inline-flex">
                            <v-file-input
                                accept="image/png, image/jpeg, image/jpg, image/avg"
                                label="Change Image"
                                placeholder="upload image"
                                @change="showImage"
                                v-model="imageUpload"
                            ></v-file-input>
                            <v-avatar class="ml-3 mt-3" size="40">
                                <img
                                    :src="
                                        showImageReview != ''
                                            ? showImageReview
                                            : item.image
                                    "
                                />
                            </v-avatar>
                        </div>
                    </template>
                </v-edit-dialog>
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
        <v-snackbar :class="snackbarClass" v-model="snackbar">
            {{ text }}
            <v-btn small icon dark text @click="snackbar = false">
                <v-icon class="white--text">mdi-close-circle</v-icon>
            </v-btn>
        </v-snackbar>

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
        imageUpload: null,
        showImageReview: "",
        emailNotValid: "",
        rshowPass: false,
        showPass: false,
        itemPerPage: 0,
        pageNumber: 0,
        r_pass: "",
        selectItem: "",
        userRole: [],
        totalUserCount: 0,
        deleteModalTitle: "Delete user?",
        selectedRow: [],
        selectedRowIndexForDelete: [],
        searchIt: "",
        valid: true,
        UserRoleRules: [v => !!v || "Required"],
        userNameRules: [
            v => !!v || "Required",
            v => v.length > 2 || "Too sort",
            v => v.length < 22 || "Too long"
        ],
        emailRules: [
            v => !!v || "Required",
            v => {
                const emailPattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                return emailPattern.test(v) || "Enter a valid email";
            }
        ],
        passwordRules: [
            v => !!v || "Required",
            v => v.length >= 8 || "Minimun 8 character"
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
            { text: "Image", sortable: false, value: "image" },
            { text: "Name", sortable: false, value: "name" },
            { text: "Email", sortable: false, value: "email" },
            { text: "Role", sortable: false, value: "role" },

            { text: "Created at", sortable: false, value: "created_at" },
            { text: "Updated at", sortable: false, value: "updated_at" },
            { text: "Actions", value: "actions", sortable: false }
        ],
        users: [],
        editedIndex: -1,
        editedItem: {
            id: "",
            name: "",
            email: "",
            role: "",
            password: ""
        },
        defaultItem: {
            id: "",
            name: "",
            email: "",
            role: "",
            password: ""
        }
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New User" : "Edit User";
        },
        cpasswordRules() {
            return (
                this.editedItem.password == this.r_pass ||
                "Password not matched"
            );
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
        uploadToServer(item) {
            if (this.imageUpload) {
                let index = this.users.data.indexOf(item);
                let form = new FormData();
                form.append("photo", this.imageUpload, this.imageUpload.name);
                form.append("user", item.id);
                axios
                    .post("/api/user/image/upload", form)
                    .then(res => {
                        this.imageUpload = null;
                        this.showImageReview = "";
                        this.users.data[index].image = res.data.newImage;
                    })
                    .catch(err => {
                        console.dir(err);
                    });
            }
        },
        showImage(e) {
            let image = e;
            if (image) {
                let reader = new FileReader();
                reader.readAsDataURL(image);
                reader.onload = e => {
                    this.showImageReview = e.target.result;
                };
            }
            return (this.showImageReview = "");
        },
        changeRole(item) {
            let index = this.users.data.indexOf(item);
            axios
                .post("api/user/role/change", { ...item })
                .then(res => {
                    if (res.data.right == 0) {
                        this.text = "You cannot change your own role";
                        this.snackbar = true;
                    } else if (res.data.right == 1) {
                        this.text =
                            res.data.user.name +
                            "'s Role is changed to " +
                            res.data.user.role;
                        this.snackbar = true;
                    }
                    this.users.data[index].role = res.data.user.role;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        emailValidity(v) {
            let emailvalid = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (emailvalid.test(v)) {
                axios
                    .post("/api/email/verify", {
                        email: v,
                        editedId: this.editedItem.id
                    })
                    .then(res => {
                        if (res.data.exist == 0) {
                            this.emailNotValid = "Email already exist";
                        } else {
                            this.emailNotValid = "";
                        }
                    })
                    .catch(err => {
                        console.dir(err);
                    });
            }
        },
        indexCount(item) {
            let itemPerPage = this.itemsPerPage;
            let pageNumbeer = this.pageNumber - 1;
            let index = this.users.data.indexOf(item) + 1;
            return itemPerPage * pageNumbeer + index;
        },
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
        selectAll(v) {
            this.selectedRowIndexForDelete = [];
            this.selectedRow = v.map(e => e.id);
            v.forEach(row => {
                this.selectedRowIndexForDelete.push(
                    this.users.data.indexOf(row)
                );
            });
        },
        search(v) {
            if (v.length > 2) {
                this.loading = true;
                this.searchIt = v;
                this.paginate({ itemsPerPage: 5, page: 1 });
            }

            if (v.length === 0) {
                this.loading = true;
                this.searchIt = "";
                this.paginate({ itemsPerPage: 10, page: 1 });
            }
        },

        paginate(e) {
            this.itemsPerPage = e.itemsPerPage;
            this.pageNumber = e.page;
            this.loading = true;

            axios
                .get("/api/users?page=" + e.page, {
                    params: {
                        per_page: e.itemsPerPage,
                        search: this.searchIt
                    }
                })
                .then(res => {
                    this.users = res.data.Users;
                    this.userRole = res.data.role;
                    this.totalUserCount = res.data.Users.total;
                    this.loading = false;
                })
                .catch(err => {
                    if (err.response.status === 401) {
                        this.$router.push("/login");
                    }
                });
        },

        editItem(item) {
            this.editedIndex = this.users.data.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        openModal(item) {
            if (!item.id) {
                this.deleteModalTitle = "Delete selected users?";
                this.deleteItemDialog = true;
                return;
            }
            this.deleteModalTitle = "Delete user?";
            this.selectedRow = [];
            this.selectedRowIndexForDelete = [];
            this.deleteItemDialog = true;
            this.toDelete = item.id;
            this.editedIndex = this.users.data.indexOf(item);
        },

        deleteItem() {
            this.deleteItemDialog = false;
            this.LoaderColor = "error";
            if (this.selectedRow.length == 0) {
                let deletedIndex = this.editedIndex;

                this.saveTextLoader = "User deleting...Please wait";
                this.loadingloder = true;
                axios
                    .delete("/api/users/delete/" + this.toDelete)
                    .then(res => {
                        this.users.data.splice(deletedIndex, 1);
                        this.loadingloder = false;
                        this.snackbarClass = "red--text";
                        this.text = "User Deleted!";
                        this.snackbar = true;
                        this.toDelete = "";
                        this.totalUserCount -= 1;
                        this.editedIndex = -1;
                    })
                    .catch(err => {
                        this.loadingloder = false;
                        this.snackbarClass = "white--text";
                        this.text = "Role Delete failed!";
                        this.snackbar = true;
                        console.dir(err);
                        this.toDelete = "";
                        this.editedIndex = -1;
                    });
            } else {
                this.saveTextLoader = "Deleting selected...Please wait";
                this.loadingloder = true;
                axios
                    .post("/api/users/delete/selected", {
                        selectedUser: this.selectedRow
                    })
                    .then(res => {
                        this.selectedRow.forEach(value => {
                            this.users.data = this.users.data.filter(
                                v => v.id != value
                            );
                        });

                        this.totalUserCount -= res.data;

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
                this.editedIndex = -4;
                setTimeout(() => {
                    this.editedIndex = -1;
                }, 300);
            });
        },

        save() {
            if (this.emailNotValid != "") {
                return;
            }
            if (this.$refs.userForm.validate()) {
                this.dialog = false;
                this.loadingloder = true;
                let editedItemIndex = this.editedIndex;
                if (this.editedIndex > -1) {
                    this.LoaderColor = "info";
                    this.saveTextLoader = "Role updating...Please wait";
                    axios
                        .post("/api/users/update/" + this.editedItem.id, {
                            name: this.editedItem.name,
                            email: this.editedItem.email,
                            role: this.editedItem.role
                        })
                        .then(res => {
                            this.users.data.splice(
                                editedItemIndex,
                                1,
                                res.data.updateUser
                            );
                            this.loadingloder = false;
                            this.snackbarClass = "info--text";
                            this.text = "User Edited!";
                            this.snackbar = true;
                        })
                        .catch(err => {
                            this.loadingloder = false;
                            this.snackbarClass = "red--text";
                            this.text = "User Edit Failed!";
                            this.snackbar = true;
                            console.dir(err);
                        });
                } else {
                    let pageDetail = [this.itemPerPage, this.pageNumber];
                    let totalPageCount =
                        Math.floor(this.totalUserCount / pageDetail[0]) + 1;
                    this.LoaderColor = "primary";
                    this.saveTextLoader = "New user creating...Please wait";
                    axios
                        .post("/api/users", { ...this.editedItem })
                        .then(res => {
                            if (totalPageCount == pageDetail[1]) {
                                this.users.data.push(res.data.newUser);
                            }

                            this.totalUserCount += 1;
                            this.loadingloder = false;

                            this.snackbarClass = "indigo--text";
                            this.text = "New User Created";
                            this.snackbar = true;
                        })
                        .catch(err => {
                            this.loadingloder = false;
                            this.snackbarClass = "red--text";
                            this.text = "Role add failed";
                            this.snackbar = true;
                        })
                        .then(v => {
                            this.valid = true;
                            this.r_pass = "";
                            this.editedItem = Object.assign(
                                {},
                                this.defaultItem
                            );
                            this.editedIndex = -4;
                            setTimeout(() => {
                                this.editedIndex = -1;
                            }, 300);
                        });
                }
            }
        }
    }
};
</script>
<style scoped></style>
