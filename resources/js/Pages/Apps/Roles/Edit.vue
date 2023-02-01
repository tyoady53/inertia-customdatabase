<template>
    <Head>
        <title>Edit Role - Master Form</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> EDIT ROLE</span>
                            </div>
                            <div class="card-body">

                                <form @submit.prevent="submit">
                                    <div class="mb-3">
                                        <label class="fw-bold">Role Name</label>
                                        <input class="form-control" v-model="form.name" :class="{ 'is-invalid': errors.name }" type="text" placeholder="Role Name">
                                        <div v-if="errors.name" class="alert alert-danger">
                                            {{ errors.name }}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label class="fw-bold">Permissions</label>
                                        <br>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <th class="text-center"> Permission Name </th> <th class="text-center"> Access </th>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(permission, index) in permissions" :key="index">
                                                    <td>
                                                        <div v-if="permission.name.includes('.index')">
                                                            <strong>{{ permission.name }}</strong>
                                                        </div>
                                                        <div v-else-if="permission.name == 'form.create' || permission.name == 'form.relation' || permission.name == 'form.manage' ">
                                                            <strong style="color:red;">{{ permission.name }}</strong>
                                                        </div>
                                                        <div v-else>
                                                            {{ permission.name }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="checkbox" v-model="form.permissions" :value="permission.name" :id="`check-${permission.id}`">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- <div class="form-check form-check-inline" v-for="(permission, index) in permissions" :key="index">
                                            <input class="form-check-input" type="checkbox" v-model="form.permissions" :value="permission.name" :id="`check-${permission.id}`">
                                            <label class="form-check-label" :for="`check-${permission.id}`">{{ permission.name }}</label>
                                        </div> -->
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary shadow-sm rounded-sm" type="submit">UPDATE</button>
                                            <button class="btn btn-warning shadow-sm rounded-sm ms-3" type="reset">RESET</button>
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
            permissions: Array,
            role: Object,
        },

        setup(props) {
            const form = reactive({
                name: props.role.name,
                permissions: props.role.permissions.map(obj => obj.name),
            });

            const submit = () => {

                Inertia.put(`/apps/roles/${props.role.id}`, {
                    name: form.name,
                    permissions: form.permissions,
                }, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Role updated successfully.',
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

<style>

</style>