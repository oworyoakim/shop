<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-building"></i> &nbsp;Branches</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="createBranch" class="btn btn-info btn-sm pull-right"><i
                    class="fa fa-plus"></i> Add Branch
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table ref="branchesDatatable" class="table table-condensed table-sm text-sm" width="100%">
                <thead>
                <tr class="bg-pale-purple text-bold">
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th  class="text-center">Balance</th>
                    <th>Manager</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="branch in branches">
                    <td>{{branch.name}}</td>
                    <td>{{branch.phone}}</td>
                    <td>{{branch.email}}</td>
                    <td class="text-center">{{$numeral(branch.balance).format('0,0')}}</td>
                    <td>
                        <span v-if="!!branch.manager">{{branch.manager.fullName}}</span>
                    </td>
                    <td>{{branch.address}} {{branch.city}}, {{branch.country}}</td>
                    <td>
                        <button v-if="branch.canBeEdited"
                                type="button"
                                title="Edit"
                                @click="createBranch(branch)"
                                class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button v-if="branch.canBeDeleted"
                                type="button"
                                title="Delete"
                                @click="deleteBranch(branch)"
                                class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                        <button v-if="branch.canBeLocked"
                                type="button"
                                title="Lock Branch"
                                @click="lockBranch(branch)"
                                class="btn btn-warning btn-sm">
                            <i class="fa fa-lock"></i>
                        </button>
                        <button v-if="branch.canBeUnlocked"
                                type="button"
                                title="Unlock Branch"
                                @click="unlockBranch(branch)"
                                class="btn btn-success btn-sm">
                            <i class="fa fa-unlock"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="modal center-modal fade" ref="branchFormModal" id="branchFormModal" tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form @submit.prevent="saveBranch" class="form-horizontal">
                            <div class="modal-header bg-pale-purple">
                                <h5 class="modal-title">
                                    <span v-if="!!activeBranch.id">Edit Branch</span>
                                    <span v-else>New Branch</span>
                                </h5>
                                <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-4">Name</label>
                                    <div class="col-8">
                                        <input v-model="activeBranch.name" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Phone</label>
                                    <div class="col-8">
                                            <input v-model="activeBranch.phone" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Email</label>
                                    <div class="col-8">
                                            <input v-model="activeBranch.email" type="email" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Country</label>
                                    <div class="col-8">
                                        <input v-model="activeBranch.country" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">City</label>
                                    <div class="col-8">
                                        <input v-model="activeBranch.city" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Address</label>
                                    <div class="col-8">
                                        <textarea v-model="activeBranch.address" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-footer-uniform">
                                <button :disabled="isSending || !(!!activeBranch.name && !!activeBranch.phone)"
                                        type="submit"
                                        class="btn btn-info float-right">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import Branch from "../../../models/Branch";
    import {deepClone} from "../../../utils";
    import {EventBus} from "../../../app";

    export default {
        created() {
            this.getBranches();
            EventBus.$on(['SAVE_BRANCH', 'DELETE_BRANCH','LOCK_BRANCH','UNLOCK_BRANCH'], this.getBranches);
        },
        computed: {
            ...mapGetters({
                branches: 'GET_BRANCHES',
            }),
        },
        data() {
            return {
                isLoading: false,
                isSending: false,
                activeBranch: new Branch(),
            }
        },
        methods: {
            async getBranches() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_BRANCHES');
                    this.isLoading = false;
                    this.$nextTick(() => {
                        $(this.$refs.branchesDatatable).DataTable();
                    });
                } catch (error) {
                    this.isLoading = false;
                    swal({title: error, icon: 'error'});
                }
            },
            async saveBranch() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_BRANCH', this.activeBranch);
                    this.isSending = false;
                    swal({title: response, icon: 'success'});
                    this.closeModal();
                    EventBus.$emit('SAVE_BRANCH');
                } catch (error) {
                    this.isSending = false;
                    let content = document.createElement('div');
                    content.innerHTML = error;
                    swal({content: content, icon: 'error'});
                }
            },
            async deleteBranch(branch) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will delete ${branch.name}!`,
                        icon: 'warning',
                        buttons: [
                            "Cancel",
                            "Delete"
                        ],
                        closeOnClickOutside: false,
                    });
                    if (!isConfirmed) {
                        return;
                    }
                    let response = await this.$store.dispatch('DELETE_BRANCH', branch);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('DELETE_BRANCH');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },

            async lockBranch(branch) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will lock ${branch.name}!`,
                        icon: 'warning',
                        buttons: [
                            "Cancel",
                            "Lock"
                        ],
                        closeOnClickOutside: false,
                    });
                    if (!isConfirmed) {
                        return;
                    }
                    let response = await this.$store.dispatch('LOCK_BRANCH', branch);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('LOCK_BRANCH');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },

            async unlockBranch(branch) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will unlock ${branch.name}!`,
                        icon: 'warning',
                        buttons: [
                            "Cancel",
                            "Unlock"
                        ],
                        closeOnClickOutside: false,
                    });
                    if (!isConfirmed) {
                        return;
                    }
                    let response = await this.$store.dispatch('UNLOCK_BRANCH', branch);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('UNLOCK_BRANCH');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },

            createBranch(branch = null) {
                if (branch) {
                    this.activeBranch = deepClone(branch);
                }
                $(this.$refs.branchFormModal).modal('show');
            },
            closeModal() {
                this.activeBranch = new Branch();
                $(this.$refs.branchFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
