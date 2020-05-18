<template>
<div>
    <v-app>
        <v-container>
            <v-dialog
                v-model="dialog"
                max-width="600px"
                transition="dialog-transition"
            >
            <v-btn slot="activator" color="success" @click="dialog = true">add new</v-btn>
            <v-card>
                <v-card-title primary-title>
                    This is the title of this form
                </v-card-title>
                <v-card-text>
                    <v-form class="px-3">
                        <v-text-field
                            :rules="Inputrule"
                            name="name"
                            label="Title"
                            id="id"
                        ></v-text-field>
                        
                    </v-form>
                </v-card-text>
            </v-card>
                
            </v-dialog>
        </v-container>
    </v-app>
</div>








</template>
<script>
    export default {
        name: 'Vueti',
        data() {
            return {
                dialog: false,
                Inputrule: [
                    v => v.length < 4 || "Input field is not valid"
                ]
            }
        },
        methods : {
            
        }
    }

</script>