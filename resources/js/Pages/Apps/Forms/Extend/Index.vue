<template>
    <Head>
        <title>Ticket #{{ ticket }}</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card border-0 rounded-3 shadow border-top-purple">
                        <div class="card-header">
                            <span class="font-weight-bold"><strong>Ticket Number : #{{ data.index_id }}</strong><br>by {{ treat_starter.name }}<br>at {{ data.created_at }}</span>
                        </div>
                        <div class="card-body">
                            {{ data.description }}
                        </div>
                    </div>
                    <div class="card border-0 rounded-3 shadow p-3" v-for="data in extend">
                        <div class="card-header">
                            <span class="font-weight-bold">{{ data.user.name }}<br>at {{ data.created_at }}</span>
                        </div>
                        <div class="card-body">
                            {{ data.description }}
                        </div>
                    </div>
                    <div class="card border-0 rounded-3 shadow p-3">
                        <form :action="`/apps/forms/${table}/add_extend/${ticket}`" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <!-- <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="data_id" :value="ticket" type="hidden"> -->
                            <textarea class="form-control" name="description"></textarea>
                            <br>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    import LayoutApp from '../../../../Layouts/App.vue';

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
            data: Object,
            extend: Array,
            treat_starter: Object,
            ticket: String,
            table: String,
            csrfToken: String,
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