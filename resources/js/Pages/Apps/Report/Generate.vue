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
                            <div>
                                <h1>Report Form {{ table_name }}</h1>
                            </div>
                            <div>
                                <h5>{{ date }}</h5>
                            </div>
                            <div>
                                <button :loading="downloadLoading" type="primary" icon="el-icon-document" @click="handleDownload">
                                    Export Excel
                                </button>
                            </div>
                            <form>
                                <div v-if="filters.length">
                                    <label>Filtered By :</label>
                                    <div v-for="f in filters">
                                        <div class="row">
                                            <div class="col-sm-1" style="text-align: right;">
                                                <label>{{ f.split('`~>`')[0] }} : </label>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>{{ f.split('`~>`')[1] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                       <th class="text-center" v-for="header in headers"> {{ header.field_description }} </th>
                                </thead>
                                <tbody>
                                    <tr v-for="form in data">
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

    import { reactive } from 'vue';

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
            data: Array,
            filters: Array,
            table_name: String,
            headers: Object,
            csrfToken: String,
            date : String,
        },

        methods: {
            handleDownload() {
                this.downloadLoading = true
                import('@/vendor/Export2Excel').then(excel => {
                    const tHeader = this.headers
                    const filterVal = ['id', 'title', 'author', 'pageviews', 'display_time']
                    const list = this.list
                    const data = this.formatJson(filterVal, list)
                    console.log(tHeader);
                    excel.export_json_to_excel({
                        header: tHeader,
                        data,
                        filename: this.filename,
                        autoWidth: this.autoWidth,
                        bookType: this.bookType
                    })
                    this.downloadLoading = false
                })
            },
            formatJson(filterVal, jsonData) {
            return jsonData.map(v => filterVal.map(j => {
                if (j === 'timestamp') {
                return parseTime(v[j])
                } else {
                return v[j]
                }
            }))
            }
        },

        data: () => ({
            listLoading: true,
            downloadLoading: false,
            filename: '',
            autoWidth: true,
            bookType: 'xlsx'
        }),

        methods: {
            showImage() {
                return "/storage/";
            },
        },
}
</script>