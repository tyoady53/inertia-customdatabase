<template>
    <Head>
        <title>Add New Data - {{ table_name }}</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> ADD DATA FORM : {{ table_name }}</span>
                            </div>
                            <div class="card-body">
                                <form action="/apps/forms/new_data" method="post">
                                        <input class="form-control" :value="table" type="hidden" name="table">
                                        <input type="hidden" name="_token" :value="csrf">
                                        <div v-for="header in headers">
                                            <label class="fw-bold">{{ header.field_description  }}</label>
                                            <input class="form-control" :class="{ 'is-invalid': errors.name }" type="text" :name="header.field_name">

                                            <div v-if="errors.name" class="alert alert-danger">
                                                {{ errors.name }}
                                            </div>
                                        </div>
                                    <hr>
                                    <div class="mb-3">

                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">SAVE</button>
                                                <button class="btn btn-warning shadow-sm rouned-sm ms-3" type="reset">RESET</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
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

    import { reactive } from 'vue';

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
        },

        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
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
            }
            return {
                form,
                submit,
            };
        }
    }
</script>