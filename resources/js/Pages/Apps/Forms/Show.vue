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
                            <!-- {{ divisions }} -->
                            <!-- {{ users }} -->
                            <form>
                                <div class="input-group mb-3" v-if="hasAnyPermission([create_data])">
                                    <!-- /apps/forms/${table}/add_data -->
                                    <Link href="#" class="btn btn-primary input-group-text" data-bs-toggle="modal" data-bs-target="#add_dataModal" @click="newData"> <i class="fa fa-plus-circle me-2"></i> Add Data</Link>
                                    <input type="text" class="form-control" placeholder="Search Data">

                                    <button class="btn btn-primary input-group-text" type="submit"> <i class="fa fa-search me-2"></i> SEARCH</button>
                                </div>
                                <div class="input-group mb-3" v-if="hasAnyPermission(['form.create'])">
                                    <Link :href="`/apps/forms/${table}/manage`" class="btn btn-primary input-group-text"> <i class="fa fa-edit me-2"></i> Manage Form</Link>
                                </div>
                            </form>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <th class="text-center" v-if="extend == '1'"> #ID </th> <th class="text-center" v-for="header in headers"> {{ header.field_description }} </th> <th class="text-center" v-if="extend == '1'"> Action </th>
                                </thead>
                                <tbody>
                                    <tr v-for="form in forms">
                                        <td class="text-center">
                                            {{ form.index_id }}
                                        </td>
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
                                        <td v-if="extend == '1'" class="text-center">
                                            <!-- <button v-if="hasAnyPermission([edit_data])" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal" @click="sendInfo(form)"> <i class="fa fa-pencil-alt me-1"></i> Edit Data</button> -->
                                            <Link :href="`/apps/forms/${table}/extends/${form.index_id}`" v-if="form['index_id']" class="btn btn-success btn-sm me-2"><i class="fa fa-expand"></i> Open Data</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal Add Data [NEW]-->
        <div class="modal" id="add_dataModal" ref="add_dataModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Add Data : {{ table_name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/new_data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="data_id" :value="selectedUser.id" type="hidden">
                            <div class="mb-3" v-for="header in headers" :key="header">
                                <div v-if="relate == 'yes'">
                                    <div v-if="header.relation == '1'">
                                        <div v-for="rel_data in relation">
                                            <div v-if="rel_data.field_from == header.field_name">
                                                <div v-for="rel in related">
                                                    <div v-if="rel.field_from == header.field_name">
                                                        <label class="fw-bold">{{ header.field_description }}</label>
                                                        <select class="form-control" :name="header.field_name" :required="header.required == 'required'">
                                                            <option v-for="option in rel[header.field_name]" :value="option[header.field_name]">{{option[rel_data.refer_to]}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-if="header.input_type === 'Text'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" type="text" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Number'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Time'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" type="time" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Date'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" type="date" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'File'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" type="file" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Yes/No'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <select class="form-control" :name="header.field_name" :required="header.required == 'required'">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <!-- <div v-else-if="header.input_type === 'Today Date'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" :value="today" type="text" readonly>
                                        </div> -->
                                        <div v-else-if="header.input_type === 'Checklist'">
                                            <div v-for="(checklist,index) in checklist_data" :key="index">
                                                <div v-if="header.relate_to.split('#')[0] == index">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <thead>
                                                            <th class="text-center" colspan="2"> {{ header.field_description }} </th>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="check in checklist">
                                                                <td>
                                                                    {{check[header.relate_to.split('#')[1]]}}
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" :name="header.field_name+'[]'" :value="check[header.relate_to.split('#')[1]]" :required="header.required == 'required'">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <!-- <label class="fw-bold">{{ header.field_description }}</label>
                                                    <select class="form-control" :name="header.field_name">
                                                        <option v-for="check in checklist">{{ check[header.relate_to.split('#')[1]] }}</option>
                                                    </select> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else-if="header.input_type === 'Longtext'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description" :required="header.required == 'required'"></textarea>
                                        </div>
                                        <div v-else-if="header.input_type.includes('#')">
                                            <div v-if="header.input_type.split('#')[0] === 'Parent'">
                                                <label class="fw-bold">{{ header.field_description }}</label>
                                                <div class="mb-3">
                                                    <select class="form-control" :name="header.field_name" v-model="selectedChainIds" @change="onChangeChain(header.relate_to.split('#')[1])" :required="header.required == 'required'">
                                                        <option v-for="parent in parentData">{{ parent[header.relate_to.split('#')[1]] }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div v-else-if="header.input_type.split('#')[0] === 'Child'">
                                                <div  v-if="filteredChain.length">
                                                    <label class="fw-bold">{{ header.field_description }}</label>
                                                    <select class="form-control" :name="header.field_name" v-model="selectedSubChainIds" :required="header.required == 'required'">
                                                        <option v-for="chain in filteredChain">{{ chain[header.relate_to.split('#')[1]] }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-if="header.input_type === 'Text'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" type="text" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Number'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Time'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" type="time" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Date'">
                                        <label class="fw-bold">{{ header.field_description }} {{ header.required }}</label>
                                        <input class="form-control" :name="header.field_name" type="date" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'File'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" type="file" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Longtext'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <textarea class="form-control" :name="header.field_name" type="number" :placeholder="header.field_description" :required="header.required == 'required'"></textarea>
                                    </div>
                                    <div v-else-if="header.input_type === 'Yes/No'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <select class="form-control" :name="header.field_name" :required="header.required == 'required'">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <!-- <div v-else-if="header.input_type === 'Today Date'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="today" type="text" readonly>
                                    </div> -->
                                    <div v-else-if="header.input_type === 'Checklist'">
                                        <div v-for="(checklist,index) in checklist_data" :key="index">
                                            <div v-if="header.relate_to.split('#')[0] == index">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <th class="text-center" colspan="2"> {{ header.field_description }} </th>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="check in checklist[index]">
                                                            <td>
                                                                {{check[header.relate_to.split('#')[1]]}}
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" :name="header.field_name+'[]'" :value="check[header.relate_to.split('#')[1]]" :required="header.required == 'required'">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else-if="header.input_type.includes('#')">
                                        <div v-if="header.input_type.split('#')[0] === 'Parent'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <div class="mb-3">
                                                <select class="form-control" :name="header.field_name" v-model="selectedChainIds" @change="onChangeChain(header.relate_to.split('#')[1])" :required="header.required == 'required'">
                                                    <option v-for="parent in parentData">{{ parent[header.relate_to.split('#')[1]] }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div v-else-if="header.input_type.split('#')[0] === 'Child'">
                                            <div  v-if="filteredChain.length">
                                                <label class="fw-bold">{{ header.field_description }}</label>
                                                <select class="form-control" :name="header.field_name" v-model="selectedSubChainIds" :required="header.required == 'required'">
                                                    <option v-for="chain in filteredChain">{{ chain[header.relate_to.split('#')[1]] }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <input class="form-control" :name="header.field_name" type="text" :placeholder="header.field_description"> -->
                            </div>
                            <div v-if="extend == '1'">
                                <div>&nbsp&nbsp&nbsp&nbsp
                                    <input class="form-check-input" type="checkbox" v-model="create_ticket" id="create_ticket" name="create_ticket">
                                    <label class="form-check-label" for="create_ticket">Create Ticket</label>
                                </div>
                            </div>
                            <div v-if="create_ticket">
                                <div>
                                    <label class="fw-bold">Assign By</label>
                                    <select class="form-control" name="assignSelector" v-model="assignSelector" @change="onChangeAssign">
                                        <option value="division">Division</option>
                                        <option value="user">Users</option>
                                    </select>
                                </div>
                                <div  v-if="filteredAssign.length">
                                    <label class="fw-bold">Assign to</label>
                                    <select class="form-control" name="assign_to" v-model="selectedAssign">
                                        <option v-for="chain in filteredAssign" :key="chain" :value="chain.id">{{ chain.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="modal-footer">
                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">Save</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                </div>
            </div>
        </div>
        <!-- End of Modal Add Data -->

        <!-- The Modal Edit Data -->
        <div class="modal" id="editModal" ref="editModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Update Data : {{ table_name }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="/apps/forms/update_data" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_token" :value="csrfToken">
                            <input class="form-control" name="table" :value="table" type="hidden">
                            <input class="form-control" name="data_id" :value="selectedUser.id" type="hidden">
                            <div class="mb-3" v-for="header in headers" :key="header">
                                <div v-if="relate == 'yes'">
                                    <div v-if="header.relation == '1'">
                                        <div v-for="rel_data in relation">
                                            <div v-if="rel_data.field_from == header.field_name">
                                                <div v-for="rel in related">
                                                    <div v-if="rel.field_from == header.field_name">
                                                        <label class="fw-bold">{{ header.field_description }}</label>
                                                        <select class="form-control" v-model="selected[header.field_name]" :name="header.field_name" :required="header.required == 'required'">
                                                            <option v-for="option in rel[header.field_name]" :value="option[header.field_name]">{{option[rel_data.refer_to]}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-if="header.input_type === 'Text'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Number'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Time'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="time" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Date'">
                                            <label class="fw-bold">{{ header.field_description }} </label>
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="date" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'File'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="file" :placeholder="header.field_description" :required="header.required == 'required'">
                                        </div>
                                        <div v-else-if="header.input_type === 'Longtext'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <textarea class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description" :required="header.required == 'required'">{{ selectedUser[header.field_name] }}</textarea>
                                        </div>
                                        <div v-else-if="header.input_type === 'Yes/No'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <select class="form-control" :name="header.field_name" :required="header.required == 'required'">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                        <div v-else-if="header.input_type === 'Checklist'">
                                            <div v-for="(checklist,index) in checklist_data" :key="index">
                                                <div v-if="header.relate_to.split('#')[0] == index">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <thead>
                                                            <th class="text-center" colspan="2"> {{ header.field_description }} </th>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="check in checklist[index]">
                                                                <td>
                                                                    {{check[header.relate_to.split('#')[1]]}}
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="checkbox" :name="header.field_name+'[]'" :value="check[header.relate_to.split('#')[1]]" :required="header.required == 'required'">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else-if="header.input_type.includes('#')">
                                            <div v-if="header.input_type.split('#')[0] === 'Parent'">
                                                <label class="fw-bold">{{ header.field_description }}</label>
                                                <div class="mb-3">
                                                    <select class="form-control" :name="header.field_name" v-model="selectedChainIds" @change="onChangeChain(header.relate_to.split('#')[1])" :required="header.required == 'required'">
                                                        <option v-for="parent in parentData" :value="parent[header.relate_to.split('#')[1]]">{{ parent[header.relate_to.split('#')[1]] }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div v-else-if="header.input_type.split('#')[0] === 'Child'">
                                                <div  v-if="filteredChain.length">
                                                    <label class="fw-bold">{{ header.field_description }}</label>
                                                    <select class="form-control" :name="header.field_name" v-model="selectedSubChainIds" :required="header.required == 'required'">
                                                        <option v-for="chain in filteredChain" :value="chain[header.relate_to.split('#')[1]]">{{ chain[header.relate_to.split('#')[1]] }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <div v-if="header.input_type === 'Text'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Number'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="number" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Time'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="time" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Date'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="date" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'File'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="file" :placeholder="header.field_description" :required="header.required == 'required'">
                                    </div>
                                    <div v-else-if="header.input_type === 'Longtext'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <textarea class="form-control" :name="header.field_name" type="text" :placeholder="header.field_description" :required="header.required == 'required'">{{ selectedUser[header.field_name] }}</textarea>
                                    </div>
                                    <div v-else-if="header.input_type === 'Today Date'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <input class="form-control" :name="header.field_name" disabled readonly>
                                    </div>
                                    <div v-else-if="header.input_type === 'Yes/No'">
                                        <label class="fw-bold">{{ header.field_description }}</label>
                                        <select class="form-control" :name="header.field_name" :required="header.required == 'required'">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div v-else-if="header.input_type === 'Checklist'">
                                        <div v-for="(checklist,index) in checklist_data" :key="index">
                                            <div v-if="header.relate_to.split('#')[0] == index">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                        <th class="text-center" colspan="2"> {{ header.field_description }} </th>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="check in checklist[index]">
                                                            <td>
                                                                {{check[header.relate_to.split('#')[1]]}}
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" :name="header.field_name+'[]'" :value="check[header.relate_to.split('#')[1]]" :required="header.required == 'required'">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else-if="header.input_type.includes('#')">
                                        <div v-if="header.input_type.split('#')[0] === 'Parent'">
                                            <label class="fw-bold">{{ header.field_description }}</label>
                                            <div class="mb-3">
                                                <select class="form-control" :name="header.field_name" v-model="selectedChainIds" @change="onChangeChain(header.relate_to.split('#')[1])" :required="header.required == 'required'">
                                                    <option v-for="parent in parentData" :value="parent[header.relate_to.split('#')[1]]">{{ parent[header.relate_to.split('#')[1]] }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div v-else-if="header.input_type.split('#')[0] === 'Child'">
                                            <div  v-if="filteredChain.length">
                                                <label class="fw-bold">{{ header.field_description }}</label>
                                                <select class="form-control" :name="header.field_name" v-model="selectedSubChainIds" :required="header.required == 'required'">
                                                    <option v-for="chain in filteredChain" :value="chain[header.relate_to.split('#')[1]]">{{ chain[header.relate_to.split('#')[1]] }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="mb-3" v-for="header in headers" :key="header">
                                <label class="fw-bold">{{ header.field_name }}</label>
                                <input class="form-control" :name="header.field_name" :value="selectedUser[header.field_name]" type="text" :placeholder="header.field_description">
                            </div> -->
                            <div class="modal-footer">
                                <button class="btn btn-primary shadow-sm rounded-sm" type="submit">Update</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                    <!-- Modal footer -->
                </div>
            </div>
        </div>
        <!-- End of Modal Edit Data -->
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
            today: String,
            extend: String,
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
            parentData: Array,
            child_data: Array,
            checklist_data: Object,
            divisions: Array,
            users: Array,
        },

        data: () => ({
            selectedUser: '',
            sel:[],
            selected:[],
            parent:'',
            selectedChainIds: -1,
            selectedSubChainIds: -1,
            assignSelector: -1,
            selectedAssign: -1,
            create_ticket : false,
        }),

        methods: {
            currentDate() {
                console.log(this.today);
                const current = new Date();
                return current.toLocaleString();
            },

            onChangeChain(reference) {
                this.selectedSubChainIds = -1;
                if(!this.selectedChainIds) {
                    this.selectedChainIds = -1;
                }
                parent = reference;
            },

            onChangeAssign() {
                this.selectedAssign = -1;
                if(!this.assignSelector) {
                    this.assignSelector = -1;
                }
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
                    if(structures.relation == '1'){
                        this.sel[structures.field_name] = form[structures.field_name];
                        console.log(this.selected);
                    }
                }
            },

            newData() {
                this.selectedChainIds = '';
                this.selectedSubChainIds = '';
                this.assignSelector = '';
                this.selectedAssign = '';
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

            filteredAssign() {
            let filtered = [];
            if(this.assignSelector == 'division'){
                for(let i = 0 ; i < this.divisions.length ; i++) {
                    let structures = this.divisions[i];
                    filtered.push(structures);
                }
            } else if(this.assignSelector == 'user') {
                for(let i = 0 ; i < this.users.length ; i++) {
                    let structures = this.users[i];
                    filtered.push(structures);
                }
            }
            return filtered;
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