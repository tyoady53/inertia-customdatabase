<template>
    <Head>
        <title>MANAGE - Form {{ table_name }}</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-plus"></i> MANAGE :: {{ table_name }}</span>
                            </div>
                            <div class="card-body">
                                <button @click="add_field = true" class="btn btn-primary btn-sm me-2" type="button">Add Field</button>
                                <div v-show="add_field" class="absolute inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50">
                                    <div class="max-w-2xl p-6 bg-white rounded-md shadow-xl">
                                    <div class="flex items-center justify-between">
                                        <br>
                                        <h3 class="text-2xl">Add Field</h3>
                                    </div>
                                    <div class="mt-4">
                                        <form action="/apps/forms/new_field" method="post">
                                            <input class="form-control" :value="table" type="hidden" name="table">
                                            <input type="hidden" name="_token" :value="csrfToken">
                                            <input class="form-control" type="text" name="name">
                                            <select class="form-control" name="data_type">
                                                <option v-for="field in fields" :value="field.datatype">{{ field.name }}</option>
                                            </select>
                                            <br>
                                            <Link href="#" class="btn btn-danger" >Cancel</Link>&nbsp&nbsp&nbsp&nbsp
                                            <button class="btn btn-success" type="submit">
                                            Save
                                            </button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                                <br><br>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <th class="text-center">Field Name</th> <th class="text-center"> Relation </th><th class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="header in headers">
                                            <td> {{ header.field_description }}</td>
                                            <td v-if="header.relation === '0'"> 
                                                <div v-if="header.input_type.split('#')[0] === 'Parent'">Parent</div>
                                                <div v-else-if="header.input_type.split('#')[0] === 'Child'">Child</div>
                                                <div v-else>. .</div>
                                            </td>
                                            <td v-else>
                                                <div v-for="rel in relation">
                                                    <div v-if="rel.table_name_from == table">
                                                        <div v-if="rel.field_from == header.field_name">
                                                            This Column Have relation with <strong>{{ rel.table_to_desc }}</strong> column <strong>{{ rel.refer_to_desc }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  -->
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <div v-if="header.relation === '0'">
                                                        <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#relationModal" @click="sendInfo(header)"> <i class="fa fa-link   me-1"></i> Add Relation</button>
                                                        <br>
                                                        <br>
                                                    </div>
                                                    <div v-if="parent_count === '0'">
                                                        <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#parentModal" @click="setParent(header)"> <i class="fas fa-code-branch"></i> Set As Parent</button>
                                                        <br>
                                                        <br>
                                                    </div>
                                                    <button @click.prevent="destroy(form_access.id)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal Add Relation -->
        <div class="modal" id="relationModal" ref="relationModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Add Relation : {{ selected_field.field_description }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/add_relation" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="field_from" :value="selected_field.field_name" type="hidden">
                            <input class="form-control" name="table_structure_id" :value="selected_field.id" type="hidden">
                            <div class="mb-3">
                                <select class="form-control" name="relate_table" v-model="selectedChainIds" @change="onChangeChain">
                                    <option v-for="table in avail_tables" :value="table.id">{{ table.description }}</option>
                                </select>
                            </div>
                            <div  v-if="filteredChain.length">
                                <label>Refer To Column:</label>
                                <select class="form-control" name="refer_to" v-model="selectedSubChainIds">
                                    <option v-for="chain in filteredChain" :value="chain.field_name">{{ chain.field_description }}</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">Update</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                </div>
            </div>
        </div>
        <!-- End of Modal Add Relation -->

        <!-- The Modal Parent-Child -->
        <div class="modal" id="parentModal" ref="parentModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Set {{ as_parent.field_description }} As Parent</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/set_parent" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="field_name" :value="as_parent.field_name" type="hidden">
                            <div class="mb-3">
                                <label>Select Child</label>
                                <select class="form-control" name="child">
                                    <option v-for="table in filteredParent" :value="table.field_name">{{ table.field_description }}</option>
                                </select>
                            </div>
                            <br>
                            <label>Data From</label>
                            <div class="mb-3">
                                <select class="form-control" name="data_from_table" v-model="parentChainIds" @change="onChangeParent">
                                    <option v-for="table in avail_tables" :value="table.id">{{ table.description }}</option>
                                </select>
                            </div>
                            <div  v-if="filteredChild.length">
                                <div>
                                    <label>Parent Reference</label>
                                    <select class="form-control" name="parent_reference" v-model="parentSubChainIds">
                                        <option v-for="chain in filteredChild" :value="chain.id">{{ chain.field_description }}</option>
                                    </select>
                                    <br>
                                    <label>Child Data</label>
                                    <select class="form-control" name="child_data" v-model="childSubChainIds">
                                        <option v-for="chain in filteredChild" :value="chain.id">{{ chain.field_description }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">Update</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                </div>
            </div>
        </div>
        <!-- End of Modal Parent-Child -->

    </main>
</template>

<script>
    import LayoutApp from '../../../../Layouts/App.vue';

    import { Head, Link } from '@inertiajs/inertia-vue3';

    import { reactive } from 'vue';

    import { ref } from 'vue';

    import { Inertia } from '@inertiajs/inertia';

    import Swal from 'sweetalert2';

    export default {

        layout: LayoutApp,

        components: {
            Head,
            Link
        },

        props: {
            errors: Object,
            roles: Array,
            table: String,
            table_name: String,
            headers: Array,
            create_data: String,
            edit_data: String,
            delete_data: String,
            forms:Object,
            fields:Array,
            avail_tables: Array,
            structures: Array,
            relation: Array,
            related: Array,
            relate: String,
            csrfToken: String,
            parent_count: String,
        },

        data: () => ({
            selected_field: '',
            selectedChainIds: -1,
            selectedSubChainIds: -1,
            as_parent:'',
            child_available: '',
            parent_available: '',
            parentChainIds: -1,
            parentSubChainIds: -1,
            childSubChainIds: -1,
        }),

        methods: {
            sendInfo(header) {
                this.selected_field = header;
            },

            setParent(header) {
                this.as_parent = header;
            },

            onChangeChain() {
                this.selectedSubChainIds = -1;
                if(!this.selectedChainIds) {
                    this.selectedChainIds = -1;
                }
            },

            onChangeParent() {
                this.parentSubChainIds = -1;
                if(!this.parentChainIds) {
                    this.parentChainIds = -1;
                }
                this.childSubChainIds = -1;
                if(!this.parentChainIds) {
                    this.parentChainIds = -1;
                }
            }
        },

        computed: {
            filteredChain() {
            let filteredsubChains = [];
            for(let i = 0 ; i < this.structures.length ; i++) {
                let structures = this.structures[i];
                if(structures.table_id == this.selectedChainIds) {
                    filteredsubChains.push(structures);
                }
            }
            return filteredsubChains;
            },

            filteredChild() {
            let filteredChildData = [];
            for(let i = 0 ; i < this.structures.length ; i++) {
                let structures = this.structures[i];
                if(structures.table_id == this.parentChainIds) {
                    filteredChildData.push(structures);
                }
            }
            return filteredChildData;
            },

            filteredParent() {
            let filteredParentSelected = [];
            for(let i = 0 ; i < this.structures.length ; i++) {
                let structures = this.structures[i];
                if(structures.table_id == this.as_parent.table_id) {
                    if(structures.id != this.as_parent.id) {
                        filteredParentSelected.push(structures);
                    }
                }
            }
            return filteredParentSelected;
            },
        },

        setup() {
            const form = reactive({
                name: '',
                roles: [],
            });

            const submit = () => {

                Inertia.post('/apps/forms/new_data', {
                    name: form.name,
                    roles: form.roles
                }, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'User saved successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                });
            };

            let add_field = ref(false);

            return {
                add_field,
                form,
                submit,
            };
        }
    }
</script>