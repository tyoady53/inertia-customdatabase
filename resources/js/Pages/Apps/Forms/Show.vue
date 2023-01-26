<template>
    <Head>
        <title>Form {{ table_name }}</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 rounded-3 shadow border-top-purple">
                        <div class="card-header">
                            <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> FORM {{ table_name }}</span>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="input-group mb-3" v-if="hasAnyPermission([create_data])">
                                    <!-- /apps/forms/${table}/add_data -->
                                    <Link href="#" class="btn btn-primary input-group-text" data-bs-toggle="modal" data-bs-target="#add_dataModal"> <i class="fa fa-plus-circle me-2"></i> Add Data</Link>
                                    <input type="text" class="form-control" placeholder="search by role name . . .">

                                    <button class="btn btn-primary input-group-text" type="submit"> <i class="fa fa-search me-2"></i> SEARCH</button>
                                </div>
                                <div class="input-group mb-3" v-if="hasAnyPermission(['form.create'])">
                                    <Link :href="`/apps/forms/${table}/manage`" class="btn btn-primary input-group-text"> <i class="fa fa-edit me-2"></i> Manage Form</Link>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                       <th class="text-center" v-for="header in headers"> {{ header.field_description }} </th> <th class="text-center" v-if="hasAnyPermission([edit_data]) || hasAnyPermission([delete_data])"> Action </th>
                                </thead>
                                <tbody>
                                    <tr v-for="form in forms">
                                        <td v-for="header in headers"> {{ form[header.field_name]  }}</td>
                                            <td class="text-center"  v-if="hasAnyPermission([edit_data]) || hasAnyPermission([delete_data])">
                                                <button v-if="hasAnyPermission([edit_data])" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal" @click="sendInfo(form)"> <i class="fa fa-pencil-alt me-1"></i> Edit Data</button>
                                                <button @click.prevent="destroy(form.id,table)" v-if="hasAnyPermission([delete_data])" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal Add Data [NEW]-->
        <div class="modal" id="add_dataModal" ref="add_dataModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Add Data : {{ table_name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/new_data" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="data_id" :value="selectedUser.id" type="hidden">
                            <div class="mb-3" v-for="header in headers" :key="header">
                                <div v-if="relate == 'yes'">
                                    <div v-if="header.relation == '1'">
                                        <div v-for="rel_data in relation">
                                            <div v-if="rel_data.field_from == header.field_name">
                                                <div v-for="rel in related">
                                                    <div v-if="rel.field_from == header.field_name">
                                                        <label class="fw-bold">{{ header.field_description }}</label>
                                                        <select class="form-control" :name="header.field_name">
                                                            <option v-for="option in rel[header.field_name]" :value="option[header.field_name]">{{option[rel_data.refer_to]}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <div v-if="header.input_type === 'Text'">
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                                        </div>
                                        <div v-else-if="header.input_type === 'Number'">
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description">
                                        </div>
                                        <div v-else-if="header.input_type === 'Longtext'">
                                            <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description">{{ selectedUser[header.field_name] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="fw-bold">{{ header.field_description }}</label>
                                    <div v-if="header.input_type === 'Text'">
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                                    </div>
                                    <div v-else-if="header.input_type === 'Number'">
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description">
                                    </div>
                                    <div v-else-if="header.input_type === 'Longtext'">
                                        <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description">{{ selectedUser[header.field_name] }}</textarea>
                                    </div>
                                </div>
                                <!-- <input class="form-control" :name="header.field_name" type="text" :placeholder="header.field_description"> -->
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">Save</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                </div>
            </div>
        </div>
        <!-- End of Modal Add Data -->
    
        <!-- The Modal Edit Data -->
        <div class="modal" id="editModal" ref="editModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Update Data : {{ table_name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/update_data" method="POST">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="data_id" :value="selectedUser.id" type="hidden">
                            <div class="mb-3" v-for="header in headers" :key="header">
                                <div v-if="relate == 'yes'">
                                    <div v-if="header.relation == '1'">
                                        <div v-for="rel_data in relation">
                                            <div v-if="rel_data.field_from == header.field_name">
                                                <div v-for="rel in related">
                                                    <div v-if="rel.field_from == header.field_name">
                                                        <label class="fw-bold">{{ header.field_description }}</label>
                                                        <select class="form-control" :name="header.field_name">
                                                            <option v-for="option in rel[header.field_name]" :value="option[header.field_name]">{{option[rel_data.refer_to]}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <div v-if="header.input_type === 'Text'">
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                                        </div>
                                        <div v-else-if="header.input_type === 'Number'">
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description">
                                        </div>
                                        <div v-else-if="header.input_type === 'Longtext'">
                                            <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description">{{ selectedUser[header.field_name] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="fw-bold">{{ header.field_description }}</label>
                                    <div v-if="header.input_type === 'Text'">
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                                    </div>
                                    <div v-else-if="header.input_type === 'Number'">
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description">
                                    </div>
                                    <div v-else-if="header.input_type === 'Longtext'">
                                        <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description">{{ selectedUser[header.field_name] }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="mb-3" v-for="header in headers" :key="header">
                                <label class="fw-bold">{{ header.field_name }}</label>
                                <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                            </div> -->
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
        <!-- End of Modal Edit Data -->

    </main>
</template>

<script>
    import LayoutApp from '../../../Layouts/App.vue';

    import { Head, Link } from '@inertiajs/inertia-vue3';

    import { Inertia } from '@inertiajs/inertia';

    import Swal from 'sweetalert2';

    export default {
        //layout
        layout: LayoutApp,

        //register component
        components: {
            Head,
            Link
        },

        props: {
            table: String,
            roles: Array,
            table_name: String,
            headers: Object,
            create_data: String,
            edit_data: String,
            delete_data: String,
            relation: Array,
            related: Array,
            relate: String,
            forms:Object,
            csrfToken: String,
        },

        data: () => ({
            selectedUser: '',
        }),

        methods: {
            sendInfo(form) {
                this.selectedUser = form;
            }
        },

        setup() {

        const destroy = (id,table) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete data from table "+table,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.isConfirmed) {

                    Inertia.delete(`/apps/forms/${table}/delete_data/${id}`);

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Role deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            })
        }

        return {
            destroy
        }
    }
}
</script>