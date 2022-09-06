<template>
    <div class="card mt-2">
        <div class="card-header bg-gradient-secondary">
            <h3 class="card-title">Tenants</h3>
            <div class="card-tools">
                <button type="button"
                        class="btn btn-info btn-sm"
                        @click="editTenant()"
                        :disabled="isLoading">
                    <i class="fas fa-plus mr-2"></i>Add Tenant
                </button>
            </div>
        </div>
        <div class="card-body table-responsive">
            <Spinner v-if="isLoading"/>
            <template v-else>
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subdomain</th>
                        <th>Domain</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Authorized</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="tenant in tenants">
                        <tr>
                            <td>{{tenant.id}}</td>
                            <td>{{tenant.name}}</td>
                            <td>{{tenant.email}}</td>
                            <td>{{tenant.subdomain}}</td>
                            <td>{{tenant.website}}</td>
                            <td>{{tenant.phone}}</td>
                            <td>{{tenant.address}} {{tenant.city}} {{tenant.country}}</td>
                            <td>
                                <input type="checkbox"
                                       class="custom-checkbox"
                                       v-model="tenant.authorized"
                                       disabled>
                            </td>
                            <td>
                                <template v-if="tenant.authorized">
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            @click="deactivate(tenant)"
                                            :disabled="isSending">
                                        <i class="fas fa-lock mr-2"></i>Deactivate
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button"
                                            class="btn btn-success btn-sm"
                                            @click="activate(tenant)"
                                            :disabled="isSending">
                                        <i class="fas fa-unlock mr-2"></i>Activate
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </template>
        </div>
        <div class="card-footer">

        </div>
        <TenantForm @saved="getTenants()" />
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import Spinner from "@/shared/components/Spinner";
import {EventBus} from "@/utils/httpClient";
import TenantForm from "../../components/Tenants/TenantForm";

export default {
    name: "Tenants",
    components: {TenantForm, Spinner},
    computed: {
        ...mapGetters({
            tenants: "TENANTS",
        }),
    },
    data(){
        return {
            isLoading: false,
            isSending: false,
        }
    },
    mounted() {
        this.getTenants();
    },
    methods: {
        editTenant(tenant = null){
            EventBus.$emit("EDIT_TENANT", tenant);
        },
        async getTenants() {
            try {
                this.isLoading = true;
                await this.$store.dispatch("GET_TENANTS");
                this.isLoading = false;
            } catch (error) {
                this.isLoading = false;
                console.log(error)
            }
        },
        async activate(tenant) {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch("ACTIVATE_TENANT", tenant);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: response,
                    icon: 'success'
                });
                return this.getTenants();
            } catch (error) {
                this.isSending = false;
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: error,
                    icon: 'error',
                });
            }
        },
        async deactivate(tenant) {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch("DEACTIVATE_TENANT", tenant);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: response,
                    icon: 'success'
                });
                return this.getTenants();
            } catch (error) {
                this.isSending = false;
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: error,
                    icon: 'error',
                });
            }
        },
    },
}
</script>

<style scoped>

</style>
