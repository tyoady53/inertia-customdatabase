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
                                <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> MANAGE : {{ table_name }}</span>
                            </div>
                            <div class="card-body">
                                <button @click="add_field = true" class="btn btn-primary btn-sm me-2" type="button">Add Field</button>
                                <div v-show="add_field" class="absolute inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50">
                                    <div class="max-w-2xl p-6 bg-white rounded-md shadow-xl">
                                    <div class="flex items-center justify-between">
                                        <br>
                                        <h3 class="text-2xl">Add Field</h3>
                                        <!-- <svg
                                        @click="isOpen = false"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-8 h-8 text-red-900 cursor-pointer"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                        </svg> -->
                                    </div>
                                    <div class="mt-4">
                                        <form action="/apps/forms/new_field" method="post">
                                            <!-- <label class="fw-bold">Add Field</label> -->
                                            <input class="form-control" :value="table" type="hidden" name="table">
                                            <input type="hidden" name="_token" :value="csrf">
                                            <input class="form-control" type="text" name="name">
                                            <select class="form-control" name="data_type">
                                                <option v-for="field in fields" :value="field.datatype">{{ field.name }}</option>
                                            </select>
                                            <br>
                                            <button @click="add_field = false" class="btn btn-danger" >
                                            Cancel
                                            </button>&nbsp&nbsp&nbsp&nbsp
                                            <button class="btn btn-success" type="submit">
                                            Save
                                            </button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                                <br><br>
                                <!-- <button @click="modal-add_relation">Add Relation</button> -->
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <th class="text-center">Field Name</th> <th class="text-center"> Relation </th><th class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="header in headers">
                                            <td> {{ header.field_description  }}</td>
                                            <td v-if="header.relation === '0'"> .. </td>
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
                                                <Link :href="`/apps/roles/${table}/edit`" class="btn btn-success btn-sm me-2"><i class="fa fa-pencil-alt me-1"></i> EDIT</Link>
                                                <button @click.prevent="destroy(form_access.id)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
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
            relation: Array,
            related: Array,
            relate: String,
        },

        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },

        setup() {
            let add_field = ref(false);

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
            }
            return {
                add_field,
                form,
                submit,
            };
        }
    }
</script>