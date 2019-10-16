<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs12>
                <v-card>
                    <v-card-text>
                        <v-layout align-baseline>
                            <v-flex xs6>
                                <span class="headline">
                                    <v-icon>file_copy</v-icon> Files
                                </span>
                            </v-flex>
                            <v-flex xs6 text-xs-right>
                                <v-btn color="primary" @click="addFile" small>
                                    <v-icon left dark>add</v-icon>
                                    Add File
                                </v-btn>
                            </v-flex>
                        </v-layout>

                        <v-content v-if="projectFiles">
                            <v-text-field
                                v-model="search"
                                append-icon="search"
                                label="Search"
                                single-line
                                hide-details
                                box
                            ></v-text-field>
                            <v-data-table
                                :headers="headers"
                                :items="projectFiles"
                                :search="search"
                                :pagination.sync="pagination"
                                hide-actions
                                no-data-text="This project does not currently have any files."
                            >
                                <template v-slot:items="props">
                                    <td><a :href="props.item.filepath">{{ props.item.name | truncate(150) }}</a></td>
                                    <td width="15%">{{ props.item.created_at }}</td>
                                    <td width="25%">
                                        <v-form method="POST" id="deleteForm" @submit.prevent="deleteFile(props.item.id)">
                                            <v-btn small type="submit" color="red darken-1" class="white--text">Delete</v-btn>
                                        </v-form>
                                    </td>
                                </template>
                            </v-data-table>
                        </v-content>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="taskForm" @submit.prevent="uploadFile" ref="form" lazy-validation>
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Save Task</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field label="Upload a File" @click="selectFile" v-model="uploadFileName" prepend-icon="attach_file"></v-text-field>
                                <input type="file" ref="file" @change="fileSelected" style="display:none;">
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="taskForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
    import Event from './../events'

    export default {
        name: 'FileUpload',
        props: ['projectInfo', 'projectFiles'],
        data() {
            return {
                dialog: false,
                date_dialog: false,
                description: '',
                due_date: '',
                task_id: '',
                search: '',
                file: '',
                uploadFileName: '',
                pagination: {
                    sortBy: 'completed',
                    rowsPerPage: -1
                },
                headers: [
                    { text: 'File Name', value: 'name' },
                    { text: 'Uploaded At', value: 'created_at' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
            }
        },
        methods: {
            selectFile() {
                this.$refs.file.click();
            },
            fileSelected(e) {
                this.file = e.target.files[0];
                this.uploadFileName = this.file.name;
            },
            addFile() {
                this.dialog = true
            },
            uploadFile() {
                if (this.file != '') {
                    let fileUploadForm = new FormData()
                    fileUploadForm.append('fileUpload', this.file);

                    axios.post('/api/uploads/project/' + this.projectInfo.id, fileUploadForm, {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        Event.$emit('loadFiles', this.projectInfo.id)

                        Event.$emit('success', response.data.message)

                        this.dialog = false;
                        this.file = '';
                        this.uploadFileName = '';
                    })
                    .catch(function (error) {
                        Event.$emit('error', error.data.message)
                    })
                }
            },
            deleteFile(file) {
                axios.delete('/api/uploads/' + file)
                .then(response => {
                    Event.$emit('loadFiles', this.projectInfo.id)

                    Event.$emit('success', response.data.message) 
                })
            },
        },
    }
</script>
