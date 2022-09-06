<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-truck"></i> &nbsp;Suppliers</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="createSupplier" class="btn btn-info btn-sm pull-right">
                    <i class="fa fa-plus"></i> Add Supplier
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table ref="unitsDatatable" class="table table-condensed table-sm text-sm" width="100%">
                <thead>
                <tr class="bg-pale-purple text-bold">
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="supplier in suppliers" :key="supplier.id">
                    <td>{{supplier.name}}</td>
                    <td><span>{{supplier.phone }}</span><br>
                        <span>{{supplier.email }}</span>
                    </td>
                    <td>{{supplier.address}}</td>
                    <td>{{supplier.city}}</td>
                    <td>{{supplier.country}}</td>
                    <td>
                        <button v-if="supplier.canBeEdited"
                                type="button"
                                title="Edit"
                                @click="createSupplier(supplier)"
                                class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button v-if="supplier.canBeDeleted"
                                type="button"
                                title="Delete"
                                @click="deleteSupplier(supplier)"
                                class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="modal center-modal fade" ref="supplierFormModal" id="supplierFormModal" tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form @submit.prevent="saveSupplier" class="form-horizontal">
                            <div class="modal-header bg-pale-purple">
                                <h5 class="modal-title">
                                    <span v-if="!!activeSupplier.id">Edit Supplier ({{activeSupplier.name}})</span>
                                    <span v-else>New Supplier</span>
                                </h5>
                                <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label >Name</label>
                                        <input v-model="activeSupplier.name" placeholder="Enter name .." type="text" class="form-control" required>
                                    </div>
                                    <div class="col-4">
                                        <label >Phone</label>
                                        <input v-model="activeSupplier.phone" placeholder="Enter phone number .." type="number" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label >Email</label>
                                        <input v-model="activeSupplier.email" placeholder="Enter email .." type="email" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label>Country</label>
                                        <app-select-box
                                        :select-options.sync="countryOptions"
                                        v-model="activeSupplier.country"
                                        :value="activeSupplier.country"
                                        placeholder="Select ..."
                                        />
                                    </div>
                                    <div class="col-4">
                                        <label >City</label>
                                        <input v-model="activeSupplier.city" placeholder="Enter city .." type="text" class="form-control">
                                    </div>
                                    <div class="col-4">
                                        <label >Address</label>
                                        <input v-model="activeSupplier.address" placeholder="Enter address .." type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-footer-uniform">
                                <button
                                        type="submit"
                                        :disabled="isSending || !(!!activeSupplier.phone && !!activeSupplier.email && !!activeSupplier.name && !!activeSupplier.address)"
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
    import Supplier from "../../../models/Supplier";
    import {deepClone} from "../../../utils";
    import {EventBus} from "../../../app";
    export default {
        created() {
            this.getSuppliers();
            EventBus.$on(['SAVE_SUPPLIER', 'DELETE_SUPPLIER'], this.getSuppliers);
        },
        computed: {
            ...mapGetters({
                suppliers: 'GET_SUPPLIERS',
                countries: 'GET_COUNTRIES',
            }),
            countryOptions(){
                return this.countries.map((item)=>{
                    return {
                        value:item.code,
                        text:item.name
                    }
                })
            },
        },
        data() {
            return {
                isLoading: false,
                isSending: false,
                activeSupplier: new Supplier(),
            }
        },
        methods:{
            async getSuppliers() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_SUPPLIERS');
                    this.isLoading = false;
                } catch (error) {
                    this.isLoading = false;
                    await swal({title: error, icon: 'error'});
                }
            },
            async saveSupplier() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_SUPPLIER', this.activeSupplier);
                    this.isSending = false;
                    swal({title: response, icon: 'success'});
                    this.closeModal();
                    EventBus.$emit('SAVE_SUPPLIER');
                } catch (error) {
                    this.isSending = false;
                    let content = document.createElement('div');
                    content.innerHTML = error;
                    swal({content: content, icon: 'error'});
                }
            },
            async deleteSupplier(supplier) {
                console.log(supplier)
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will delete ${supplier.name}!`,
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
                    let response = await this.$store.dispatch('DELETE_SUPPLIER', supplier);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('DELETE_SUPPLIER');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },
            createSupplier(supplier = null) {
                this.$store.dispatch('GET_COUNTRIES');
                if (supplier) {
                    this.activeSupplier = deepClone(supplier);
                }
                $(this.$refs.supplierFormModal).modal('show');
            },
            closeModal() {
                this.activeSupplier = new Supplier();
                $(this.$refs.supplierFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
