<template>
    <div class="d-flex flex-column mt-2">
        <Spinner v-if="isLoading"/>
        <template v-else>
            <template v-if="!!usersInfo">
                <div class="card">
                    <div class="card-header bg-gradient-secondary with-border">
                        <h3 class="card-title">Users</h3>
                        <div class="card-tools">
                            <template v-if="$store.getters.HAS_ANY_ACCESS(['users.create'])">
                                <button type="button"
                                        @click="editUser()"
                                        class="btn btn-info btn-sm">
                                    <i class="fas fa-plus"></i> Add User
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <template v-if="users.length > 0">
                            <table class="table table-condensed table-sm w-100">
                                <thead>
                                <tr>
                                    <th>FirstName</th>
                                    <th>LastName</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Group</th>
                                    <th>Locked</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="user in users">
                                    <td>{{ user.firstName }}</td>
                                    <td>{{ user.lastName }}</td>
                                    <td>{{ user.username }}</td>
                                    <td>{{ user.email }}</td>
                                    <td>{{ user.group }}</td>
                                    <td>
                                        <input type="checkbox" v-model="user.active" class="disabled" disabled>
                                    </td>
                                    <td>
                                        <template v-if="user.canBeUpdated">
                                            <button type="button"
                                                    class="btn btn-info btn-sm"
                                                    @click="editUser(user)"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </template>
                                        <template v-if="user.canBeBlocked">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    @click="blockUser(user)"
                                                    title="Block">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </template>
                                        <template
                                            v-if="user.canBeUnblocked">
                                            <button type="button"
                                                    class="btn btn-warning btn-sm"
                                                    @click="unblockUser(user)"
                                                    title="Unblock">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </template>
                        <template v-else>
                            <h3>No data!</h3>
                        </template>
                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer text-right">
                        <Pagination :items="usersInfo" @gotoPage="getUsers"/>
                    </div>
                </div>
            </template>
            <UserForm />
        </template>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import Pagination from "@/shared/components/Pagination";
import Spinner from "@/shared/components/Spinner";
import {EventBus} from "@/utils/httpClient";
import UserForm from "../../components/Users/UserForm";

export default {
    components: {Pagination, UserForm, Spinner},
    mounted() {
        this.getUsers();
        EventBus.$on(['USER_SAVED', 'USER_BLOCKED', 'USER_UNBLOCKED'], () => {
            this.getUsers();
        });
    },
    data() {
        return {
            isLoading: false,
            filters: {
                page: 1
            },
        }
    },
    computed: {
        ...mapGetters({
            usersInfo: "USERS_INFO",
            loggedInUser: "LOGGED_IN_USER",
        }),
        users() {
            if (!this.usersInfo) {
                return [];
            }
            return this.usersInfo.data;
        },
    },
    methods: {
        editUser(user = null) {
            EventBus.$emit("EDIT_USER", user);
        },
        async getUsers(page = 0) {
            try {
                if (page > 0) {
                    this.filters.page = page;
                }
                this.isLoading = true;
                await this.$store.dispatch("GET_USERS", this.filters);
                this.isLoading = false;
            } catch (error) {
                this.isLoading = false;
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{text: error, icon: 'error'});
            }
        },

        async blockUser(user) {
            try {
                let isConfirmed = await this.$store.dispatch("CONFIRM_ACTION",{
                    title: 'Are you sure?',
                    text: "You will block this user!",
                    icon: 'warning',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Block',
                });
                if (!!isConfirmed) {
                    // cancel the order
                    let response = await this.$store.dispatch("BLOCK_OR_UNBLOCK_USER", {
                        id: user.id,
                        action: 'block'
                    });
                    await this.$store.dispatch("SET_SNACKBAR",{text: response, icon: 'success'});
                    EventBus.$emit("USER_BLOCKED");
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{text: error, icon: 'error'});
            }
        },

        async unblockUser(user) {
            try {
                let isConfirmed = await this.$store.dispatch("CONFIRM_ACTION",{
                    title: 'Are you sure?',
                    text: "You will unblock this user!",
                    icon: 'danger',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Unblock',
                });
                if (!!isConfirmed) {
                    // cancel the order
                    let response = await this.$store.dispatch("BLOCK_OR_UNBLOCK_USER", {
                        id: user.id,
                        action: 'unblock'
                    });
                    await this.$store.dispatch("SET_SNACKBAR",{text: response, icon: 'success'});
                    EventBus.$emit("USER_UNBLOCKED");
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{text: error, icon: 'error'});
            }
        },
    },
}
</script>

<style scoped>

</style>

