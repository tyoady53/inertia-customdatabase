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
                            <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> Report {{ table_name }}</span>
                        </div>
                        <div class="card-body">
                            <form class="card p-3" action="/apps/report/generate" method="get" target="_blank" >
                            <!-- <form class="card p-3" @submit.prevent="submit"> -->
                                <input type="hidden" name="table" :value="table">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="fw-bold">Select User&nbsp&nbsp</label>
                                        <select name="user" class="form-control" v-model="form.user">
                                            <option value=""></option>
                                            <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                        <label class="fw-bold">Filter By Date</label>
                                            <div class="col-sm-2" style="text-align: right;">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="date" name="start_date">
                                            </div>
                                            <div class="col-sm-2" style="text-align: right;">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="date" name="end_date">
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                </div>
                                <div class="input-group justify-content">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <th class="text-center" colspan="3">Filter Data</th>
                                        </thead>
                                        <tbody class="mb-3">
                                            <tr v-for="header in headers">
                                                <td>
                                                    {{header.field_description}}
                                                </td>
                                                <td class="text-center" v-if="header.input_type != 'File'">
                                                    <input type="checkbox" v-model="checkBoxArray[header.field_name]" @click="someFunction(header.field_name)" name="check_array[]" :value="header.field_name">
                                                </td>
                                                <td class="text-center" v-else>
                                                </td>
                                                <td class="text-center">
                                                    <div v-for="(sel,index) in selected" :key="index">
                                                        <div v-if="index == header.field_name">
                                                            <div v-if="header.input_type == 'Date'">
                                                                <div class="row">
                                                                    <div class="col-sm-2" style="text-align: right;">
                                                                        <label>Start Date</label>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="date" :name="'data[start_date#'+header.field_name+']'">
                                                                    </div>
                                                                    <div class="col-sm-2" style="text-align: right;">
                                                                        <label>End Date</label>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="date" :name="'data[end_date#'+header.field_name+']'">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div v-else>
                                                                <select class="form-control" v-model="selectedChainIds[header.field_name]" @change="onChangeChain(header.field_name)" :name="'data['+header.field_name+']'">
                                                                    <option></option>
                                                                    <option v-for="(data) in sel" :value="data[header.field_name]">{{ data[header.field_name] }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="input-group justify-content-md-end">
                                    <button type="submit" class="btn btn-primary input-group-text"> <i class="fa fa-edit me-2"></i> Generate Report</button>
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

    import { reactive,ref } from 'vue';

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
            users: Array,
            selected: Array,
            table_name: String,
            headers: Object,
            forms:Object,
            csrfToken: String,
        },

        data: () => ({
        }),

        methods: {
            showImage() {
                return "/storage/";
            },
        },


        setup(props) {
            let check_array = [];
            const someFunction = (name) => {
                if(checkBoxArray.value[name] !== true){
                    check_array.push(name);
                }else{
                    var index = check_array.indexOf(name);
                    check_array.splice(index,1);
                }
                console.log('check_array : '+checkBoxArray);
            }

            let array = [];
            let data_array = [];
            const onChangeChain = (field) => {
                data_array.push(field+'->'+selectedChainIds.value[field]);
                array.push(selectedChainIds.value[field]);
                console.log('data_array : '+data_array);
            }

            const checkBoxArray = ref([]);
            const selectedChainIds =  ref([]);

            const form = reactive({
                user: props.users.id,
            });

            const submit = () => {
            Inertia.get(`/apps/report/generate`, {
                user: form.user,
                check_array,
                data_array,
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
            someFunction,
            onChangeChain,
            checkBoxArray,
            selectedChainIds,
            form,
            submit,
        };
    }
}
</script>