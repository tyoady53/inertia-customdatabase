<template>
    <Head>
        <title>Report</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 rounded-3 shadow border-top-purple">
                        <div class="card-header">
                            <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> REPORTS</span>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col"> Table Name </th>
                                        <!-- <th scope="col" style="width:50%">Group</th> -->
                                        <th scope="col" style="width:20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="form_access in form_accesses">
                                        <td>{{ form_access.description }}</td>
                                        <!-- <td>{{ form_access.group }}</td> -->
                                            <td class="text-center">
                                                <Link :href="`/apps/report/${form_access.name}/show`" class="btn btn-success btn-sm me-2"><i class="fa fa-pencil-alt me-1"></i> Open Report</Link>
                                                <!-- <button @click.prevent="destroy(role.id)" v-if="hasAnyPermission(['roles.delete'])" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> DELETE</button> -->
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
            form_accesses: Object,
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