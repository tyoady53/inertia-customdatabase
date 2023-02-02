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
                            <form class="card p-3">
                                <div class="input-group justify-content">
                                    <select name="filter-user">
                                        <option value="">Select User</option>
                                        <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                                    </select>
                                </div>
                                <div class="input-group justify-content-md-end">
                                    <Link href="#" class="btn btn-primary input-group-text"> <i class="fa fa-edit me-2"></i> Generate Report</Link>
                                </div>
                            </form>
                            <!-- <table class="table table-striped table-bordered table-hover">
                                <thead>
                                       <th class="text-center" v-for="header in headers"> {{ header.field_description }} </th>
                                </thead>
                                <tbody>
                                    <tr v-for="form in forms">
                                        <td v-for="header in headers">
                                            <div v-if="header.input_type == 'File'">
                                                <img :src="showImage() + form[header.field_name]" class="object-cover h-40 w-80"/>
                                            </div>
                                            <div v-else-if="header.input_type == 'Yes/No'" class="text-center">
                                                <div v-if="form[header.field_name] == '1'">
                                                    Yes
                                                </div>
                                                <div v-else>
                                                    No
                                                </div>
                                            </div>
                                            <div v-else>
                                                {{ form[header.field_name] }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            users: Array,
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
            parent:'',
            selectedChainIds: -1,
            selectedSubChainIds: -1,
        }),

        methods: {
            onChangeChain(reference) {
                this.selectedSubChainIds = -1;
                if(!this.selectedChainIds) {
                    this.selectedChainIds = -1;
                }
                parent = reference;
            },

            sendInfo(form) {
                this.selectedUser = form;
                for(let i = 0 ; i < this.headers.length ; i++) {
                    let structures = this.headers[i];
                    if(structures.input_type.split('#')[0] == 'Parent') {
                        this.selectedChainIds = form[structures.field_name];
                        this.onChangeChain(structures.relate_to.split('#')[1]);
                    }
                    if(structures.input_type.split('#')[0] == 'Child') {
                        this.selectedSubChainIds = form[structures.field_name];
                    }
                }
            },

            newData() {
                this.selectedChainIds = '';
                this.selectedSubChainIds = '';
            },

            showImage() {
                return "/storage/";
            },
        },

        computed: {
            filteredChain() {
            let filteredsubChains = [];
            for(let i = 0 ; i < this.child_data.length ; i++) {
                let structures = this.child_data[i];
                if(structures[parent] == this.selectedChainIds) {
                    filteredsubChains.push(structures);
                }
            }
            return filteredsubChains;
            },
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