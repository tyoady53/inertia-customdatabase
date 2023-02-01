<template>
    <Head>
        <title>Add New Users - Master Form</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> ADD FORM </span>
                            </div>
                            <div class="card-body">

                                <form @submit.prevent="submit">
                                    <div class="mb-3">
                                        <label class="fw-bold">Form Name</label>
                                        <input class="form-control" v-model="form.name" :class="{ 'is-invalid': errors.name }" type="text" placeholder="Form Name">

                                        <div v-if="errors.name" class="alert alert-danger">
                                            {{ errors.name }}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label class="fw-bold">Role</label>
                                        <br>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <th class="text-center"> Show Data For </th> <th class="text-center"> Create </th> <th class="text-center"> Edit </th> <th class="text-center"> Delete </th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(role, index) in roles" :key="index">
                                                    <td>&nbsp&nbsp
                                                        <input class="form-check-input" type="checkbox" v-model="form.roles" :value="role.name" :id="`check-${role.id}`" @change="check(role)">
                                                        <label class="form-check-label" :for="`check-${role.id}`">{{ role.name }}</label> 
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" v-model="form.create" :value="`${role.name}.create`" :id="`create-${role.name}`">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" v-model="form.edit" :value="`${role.name}.edit`" :id="`edit-${role.name}`">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" v-model="form.delete" :value="`${role.name}.delete`" :id="`delete-${role.name}`">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- <div class="form-check form-check-inline" v-for="(role, index) in roles" :key="index">
                                            <input class="form-check-input" type="checkbox" v-model="form.roles" :value="role.name" :id="`check-${role.id}`" @change="check(role)">
                                            <label class="form-check-label" :for="`check-${role.id}`">{{ role.name }}</label>
                                        </div>
<br>
                                        <div class="form-check form-check-inline" v-for="(role, index) in roles" :key="index">
                                            <div v-show="role.name">
                                                <label class="fw-bold" :name="role.name">Add Other Permission For {{ role.name }}</label>
                                                <br>
                                                <input class="form-check-input" type="checkbox" :value="`create-${role.name}`" :id="`create-${role.name}`">
                                                <label class="form-check-label" :for="`create-${role.name}`">Create</label>
                                                <br>
                                                <input class="form-check-input" type="checkbox" :value="`edit-${role.name}`" :id="`edit-${role.name}`">
                                                <label class="form-check-label" :for="`edit-${role.name}`">Edit</label>
                                                <br>
                                                <input class="form-check-input" type="checkbox" :value="`delete-${role.name}`" :id="`delete-${role.name}`">
                                                <label class="form-check-label" :for="`delete-${role.name}`">Delete</label>
                                            </div>
                                        </div> -->

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
            roles: Array
        },

        data: () => ({
            roles_list: '',
        }),

        methods: {
            check(role) {
                this.roles_list = role;
            }
        },

        setup() {
            const form = reactive({
                name: '',
                roles: [],
                create: [],
                edit: [],
                delete: []
            });

            const submit = () => {

                Inertia.post('/apps/forms/new_form', {
                    name: form.name,
                    roles: form.roles,
                    create: form.create,
                    edit: form.edit,
                    delete: form.delete,
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

            // let roles_list = this.roles;
            // for (let role_ of roles_list) {
            //     this.role_ = ref(false);
            // };

            return {
                form,
                submit,
            };
        }
    }
</script>