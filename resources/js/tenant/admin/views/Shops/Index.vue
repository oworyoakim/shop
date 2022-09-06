<template>
    <div class="branches mt-2">
        <Spinner v-if="isLoading" />
        <template v-else>
            <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title"><i class="fa fa-building"></i> &nbsp;Shops</h3>
                <div class="card-tools">
                    <button :disabled="isLoading" @click="editBranch()" class="btn btn-info btn-sm"><i
                        class="fa fa-plus"></i> Add Shop
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-condensed table-sm text-sm w-100">
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
                        <td class="text-center">{{branch.balance | separator}}</td>
                        <td>
                            <template v-if="!!branch.manager">
                                {{branch.manager.username}}
                            </template>
                        </td>
                        <td>{{branch.address}} {{branch.city}}, {{branch.country}}</td>
                        <td>
                            <button v-if="branch.canBeEdited"
                                    type="button"
                                    title="Edit"
                                    @click="editBranch(branch)"
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
            </div>
            <!-- /.box-body -->
            <ShopForm @BRANCH_SAVED="getBranches" />
        </div>
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import {EventBus} from '@/utils/httpClient';
import Spinner from "@/shared/components/Spinner";
import ShopForm from "../../components/Shops/ShopForm";

export default {
    components: {Spinner, ShopForm},
    mounted() {
        this.getBranches();
    },
    computed: {
        ...mapGetters({
            branches: 'GET_BRANCHES',
        }),
    },
    data() {
        return {
            isLoading: false,
        }
    },
    methods: {
        editBranch(branch = null){
            EventBus.$emit('EDIT_BRANCH', branch);
        },

        async getBranches() {
            try {
                this.isLoading = true;
                await this.$store.dispatch('GET_BRANCHES');
                this.isLoading = false;
            } catch (error) {
                this.isLoading = false;
                await this.$store.dispatch('SET_SNACKBAR', {title: error, icon: 'error'});
            }
        },

        async deleteBranch(branch) {
            try {
                let isConfirmed = await this.$store.dispatch('CONFIRM_ACTION',{
                    title: "Are you sure?",
                    text: `You will delete ${branch.name}!`,
                    icon: 'warning',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    closeOnClickOutside: false,
                });
                if (!isConfirmed) {
                    return;
                }
                let response = await this.$store.dispatch('DELETE_BRANCH', branch);
                await this.$store.dispatch('SET_SNACKBAR',{title: response, icon: 'success'});
                this.getBranches();
            } catch (error) {
                await this.$store.dispatch('SET_SNACKBAR',{title: error, icon: 'error'});
            }
        },

        async lockBranch(branch) {
            try {
                let isConfirmed = await this.$store.dispatch('CONFIRM_ACTION',{
                    title: "Are you sure?",
                    text: `You will lock ${branch.name}!`,
                    icon: 'warning',
                    confirmButtonText: 'Lock',
                    cancelButtonText: 'Cancel',
                    closeOnClickOutside: false,
                });
                if (!isConfirmed) {
                    return;
                }
                let response = await this.$store.dispatch('LOCK_BRANCH', branch);
                await this.$store.dispatch('SET_SNACKBAR',{title: response, icon: 'success'});
                this.getBranches();
            } catch (error) {
                await this.$store.dispatch('SET_SNACKBAR',{title: error, icon: 'error'});
            }
        },

        async unlockBranch(branch) {
            try {
                let isConfirmed = await this.$store.dispatch('CONFIRM_ACTION',{
                    title: "Are you sure?",
                    text: `You will unlock ${branch.name}!`,
                    icon: 'warning',
                    confirmButtonText: 'Unlock',
                    cancelButtonText: 'Cancel',
                    closeOnClickOutside: false,
                });
                if (!isConfirmed) {
                    return;
                }
                let response = await this.$store.dispatch('UNLOCK_BRANCH', branch);
                await this.$store.dispatch('SET_SNACKBAR',{title: response, icon: 'success'});
                this.getBranches();
            } catch (error) {
                await this.$store.dispatch('SET_SNACKBAR',{title: error, icon: 'error'});
            }
        },
    }
}
</script>

<style scoped>

</style>
