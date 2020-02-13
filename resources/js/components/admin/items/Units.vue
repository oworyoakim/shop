<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-3x align-center"></span>
    <div v-else class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-balance-scale"></i> &nbsp;Units Of Measurement</h3>
            <div class="box-tools pull-right">
                <button :disabled="isLoading" @click="createUnit" class="btn btn-info btn-sm pull-right"><i
                    class="fa fa-plus"></i> Add Unit
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table ref="unitsDatatable" class="table table-condensed table-sm text-sm" width="100%">
                <thead>
                <tr class="bg-pale-purple text-bold">
                    <th>Title</th>
                    <th>Short Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="unit in units">
                    <td>{{unit.title}}</td>
                    <td>{{unit.slug}}</td>
                    <td>{{unit.description}}</td>
                    <td>
                        <button v-if="unit.canBeEdited"
                                type="button"
                                title="Edit"
                                @click="createUnit(unit)"
                                class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button v-if="unit.canBeDeleted"
                                type="button"
                                title="Delete"
                                @click="deleteUnit(unit)"
                                class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="modal center-modal fade" ref="unitFormModal" id="unitFormModal" tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form @submit.prevent="saveUnit" class="form-horizontal">
                            <div class="modal-header bg-pale-purple">
                                <h5 class="modal-title">
                                    <span v-if="!!activeUnit.id">Edit Unit</span>
                                    <span v-else>New Unit</span>
                                </h5>
                                <button @click="closeModal" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-4">Title</label>
                                    <div class="col-8">
                                        <input v-model="activeUnit.title" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-4">Description</label>
                                    <div class="col-8">
                                            <textarea v-model="activeUnit.description" class="form-control"
                                                      rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-footer-uniform">
                                <button :disabled="isSending || !!!activeUnit.title"
                                        type="submit"
                                        class="btn btn-info float-right">
                                    <span v-if="isSending" class="fa fa-spinner fa-spin"></span>
                                    <span v-else>Save</span>
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
    import ItemUnit from "../../../models/ItemUnit";
    import {deepClone} from "../../../utils";
    import {EventBus} from "../../../app";

    export default {
        created() {
            this.getUnits();
            EventBus.$on(['SAVE_UNIT', 'DELETE_UNIT'], this.getUnits);
        },
        computed: {
            ...mapGetters({
                units: 'GET_UNITS',
            }),
        },
        data() {
            return {
                isLoading: false,
                isSending: false,
                activeUnit: new ItemUnit(),
            }
        },
        methods: {
            async getUnits() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_UNITS');
                    this.isLoading = false;
                    this.$nextTick(() => {
                        $(this.$refs.unitsDatatable).DataTable();
                    });
                } catch (error) {
                    this.isLoading = false;
                    swal({title: error, icon: 'error'});
                }
            },
            async saveUnit() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_UNIT', this.activeUnit);
                    this.isSending = false;
                    swal({title: response, icon: 'success'});
                    this.closeModal();
                    EventBus.$emit('SAVE_UNIT');
                } catch (error) {
                    this.isSending = false;
                    let content = document.createElement('div');
                    content.innerHTML = error;
                    swal({content: content, icon: 'error'});
                }
            },
            async deleteUnit(unit) {
                try {
                    let isConfirmed = await swal({
                        title: "Are you sure?",
                        text: `You will delete ${unit.title}!`,
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
                    let response = await this.$store.dispatch('DELETE_UNIT', unit);
                    swal({title: response, icon: 'success'});
                    EventBus.$emit('DELETE_UNIT');
                } catch (error) {
                    swal({title: error, icon: 'error'});
                }
            },
            createUnit(unit = null) {
                if (unit) {
                    this.activeUnit = deepClone(unit);
                }
                $(this.$refs.unitFormModal).modal('show');
            },
            closeModal() {
                this.activeUnit = new ItemUnit();
                $(this.$refs.unitFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        }
    }
</script>

<style scoped>

</style>
