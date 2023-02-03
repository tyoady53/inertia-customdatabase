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
                            <!-- <form class="card p-3" action="/apps/report/generate" method="get"> -->
                            <form class="card p-3" @submit.prevent="submit">
                                <div class="input-group justify-content">
                                    <select name="filter-user" v-model="form.user">
                                        <option value="">Select User</option>
                                        <option v-for="user in users" :value="user.id">{{ user.name }}</option>
                                    </select>
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
                                                    <!-- <input type="checkbox" v-model="checkBoxArray[header.field_name]" @click="someFunction(header.field_name)" name="check[]" :value="header.field_name"> -->
                                                    <input type="checkbox" v-model="checkBoxArray[header.field_name]" @click="someFunction(header.field_name)" name="check[]" :value="header.field_name">
                                                </td>
                                                <td class="text-center" v-else>
                                                </td>
                                                <td class="text-center">
                                                    <div v-for="(sel,index) in selected" :key="index">
                                                        <div v-if="index == header.field_name">
                                                            <select class="form-control" v-model="selectedChainIds[header.field_name]" @change="onChangeChain(header.field_name)" :name="header.field_name">
                                                                <option></option>
                                                                <option v-for="(data) in sel" :value="data[header.field_name]">{{ data[header.field_name] }}</option>
                                                            </select>
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
            // const selectDataArray = ref([]);
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
                if(data_array.length > 0){
                    for(let i = 0; i < data_array.length; i++){
                        console.log(data_array[i])
                        if(data_array[i] === field){
                            console.log(data_array[i])
                            var index = data_array.indexOf(field);
                            data_array.splice(index,1);
                            data_array.splice(index,1);
                            data_array.push(field, selectedChainIds.value[field]);
                        } //else {
                        //     data_array.push(field, selectedChainIds.value[field]);
                        // }
                    }
                }else{
                    data_array.push(field, selectedChainIds.value[field]);
                }
                array.push(selectedChainIds.value[field]);
                console.log('data_array : '+data_array);
            }

            // const header = props.selected
            //     .filter(obj => props.headers.find(field_name => field_name === obj.field_name)) // this returns the array of object that are stored
            //     .map(elm => {
            //         return {
            //             title: elm.field_name,
            //         }
            //     })

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