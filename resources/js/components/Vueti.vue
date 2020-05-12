<template>
<div>
    <v-app>
        <v-container>
            <v-layout row wrap>
                <v-flex xs12 sm6 md4 lg3 v-for="(person, index) in list" :key='index'>
                    <v-card flat hover class="text-center ma-3"> 
                        <v-img
                        height="200"
                        class="white--text align-center justify-center"
                        src="https://cdn.vuetifyjs.com/images/cards/docks.jpg">
                        </v-img>
                        <v-card-title>Hello I am subtitle</v-card-title>

                        <v-card-text>
                            Hello I am ttext of this cals and I love lto be ths sthelkfsdm sdlfj s dl lsjdfl l skdjfi nslkjdff lsdfj oi sldjff lskdff 
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn color="success">text</v-btn>
                            <v-btn color="success">text</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn color="success">text</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </v-app>
</div>








</template>
<script>
    export default {
        name: 'vueti',
        data() {
            return {
                drawer : false,
                list : [
                    { name: 'Imran Hossain', trole: "web developer", ruote: '/' },
                    { name: 'Raihan Khan', role: "graphics Designer", ruote: '/' },
                    { name: 'Arif Hasan', role: "App developer", ruote: '/' },
                    { name: 'Arif Hasan', role: "App developer", ruote: '/' }
                ]
            }
        },
        methods : {
            
        }
    }

</script>