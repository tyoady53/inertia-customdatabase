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
                                    <Link :href="`/apps/forms/${table}/add_data`" class="btn btn-primary input-group-text"> <i class="fa fa-plus-circle me-2"></i> Add Data</Link>
                                    <input type="text" class="form-control" placeholder="search by role name . . .">

                                    <button class="btn btn-primary input-group-text" type="submit"> <i class="fa fa-search me-2"></i> SEARCH</button>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                       <th v-for="header in headers"> {{ header.field_description }} </th> <th v-if="hasAnyPermission([edit_data]) || hasAnyPermission([delete_data])"> Action </th>
                                </thead>
                                <tbody>
                                    <tr v-for="form in forms">
                                        <td v-for="header in headers"> {{ form[header.field_name]  }}</td>
                                            <td class="text-center"  v-if="hasAnyPermission([edit_data]) || hasAnyPermission([delete_data])">
                                                <Link :href="`/apps/roles/${table}/edit`" v-if="hasAnyPermission([edit_data])" class="btn btn-success btn-sm me-2"><i class="fa fa-pencil-alt me-1"></i> EDIT</Link>
                                                <button @click.prevent="destroy(form_access.id)" v-if="hasAnyPermission([delete_data])" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
            table: Object,
            table_name: Object,
            headers: Object,
            create_data: Object,
            edit_data: Object,
            delete_data: Object,
            forms:Object,
        },

        setup() {

        const destroy = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.isConfirmed) {

                    Inertia.delete(`/apps/roles/${id}`);

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