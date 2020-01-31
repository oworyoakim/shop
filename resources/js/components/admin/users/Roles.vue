<template>
    <span v-if="isLoading" class="fa fa-spinner fa-spin fa-5x"></span>
    <div v-else class="row">
        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <button @click="editRole" type="button" class="btn btn-info btn-purple btn-block mb-3">
                <i class="fa fa-plus"></i> Add Role
            </button>
            <ul class="list-group">
                <li class="list-group-item" v-for="rl in roles"
                    v-bind:class="{'bg-info': !!activeRole && rl.id === activeRole.id}" style="cursor: pointer;">
                    <span @click="activeRole = rl">{{rl.name}}</span>
                </li>
            </ul>
        </div>
        <div class="col-sm-6 col-md-8 col-lg-9 col-xl-10">
            <template v-if="!!activeRole">
                <div class="box box-body">
                    <ul class="list-unstyled">
                        <li class="row mb-3">
                            <span class="text-bold col-4">Name: </span>
                            <span class="col-8">{{activeRole.name}}</span>
                        </li>
                        <li class="row mb-3">
                            <span class="text-bold col-4">Description: </span>
                            <span class="col-8">{{activeRole.description}}</span>
                        </li>
                        <li class="mb-3 text-right">
                            <button type="button" @click="editRole(activeRole)"
                                    class="btn  btn-info btn-sm mr-2">
                                <i class="fa fa-pencil"></i> Edit
                            </button>
                            <button type="button" @click="deleteRole(activeRole.id)"
                                    class="btn  btn-danger btn-sm"><i
                                class="fa fa-trash"></i> Delete
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Role Permissions</h3>
                    </div>
                    <div class="box-body">
                        <form @submit.prevent="updatePermissions" class="form-horizontal">
                            <table class="table table-borderless" width="100%">
                                <tr v-for="permission in permissions" class="">
                                    <td v-if="permission.parent_id == 0" colspan="2" class="parent-role">
                                        {{permission.name}}
                                    </td>
                                    <template v-else>
                                        <td class="w-2">____</td>
                                        <td>{{permission.name}}</td>
                                    </template>
                                    <td class="text-muted">{{permission.description}}</td>
                                    <td class="w-2">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   @change="handleCheckbox"
                                                   :value="permission.slug"
                                                   v-model="activeRole.perms"
                                                   :data-parent="permission.parent_id"
                                                   :id="permission.id">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <button type="submit" class="btn btn-info btn-sm pull-right">Update</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </template>
            <!-- Add Role Modal -->
            <div ref="roleFormModal" id="roleFormModal" class="modal custom-modal fade" role="dialog"  tabindex="-1"
                 data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-pale-purple">
                            <h5 class="modal-title">Role Information</h5>
                            <button @click="closePreview()" type="button" class="close" data-dismiss="modal"
                                    aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="saveRole" class="form-horizontal">
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-sm-4">Role Name<span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <input v-model="role.name"
                                               class="form-control"
                                               :disabled="!!role.id"
                                               type="text">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4">Description </label>
                                    <div class="col-sm-8">
                                        <textarea v-model="role.description" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button :disabled="isSending" class="btn btn-info pull-right">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Add Role Modal -->
        </div>
    </div>
</template>

<script>
    import {deepClone, Errors} from "../../../utils";
    import Role from "../../../models/Role";
    import {mapGetters} from "vuex";

    export default {
        created() {
            this.getRoles();
        },
        data() {
            return {
                activeRole: null,
                role: new Role(),
                isLoading: true,
                isSending: false,
            };
        },
        computed: {
            ...mapGetters({
                roles: 'GET_ROLES',
                permissions: 'GET_PERMISSIONS',
            }),
        },
        methods: {
            async getRoles() {
                try {
                    this.isLoading = true;
                    await this.$store.dispatch('GET_ROLES');
                    this.isLoading = false;
                } catch (error) {
                    console.log(error);
                    toastr.error(error);
                    this.isLoading = false;
                }
            },
            async deleteRole(id) {
                try {
                    let isConfirm = await swal({
                        title: 'Are you sure?',
                        text: "You will delete this record!",
                        icon: 'warning',
                        buttons: [
                            'No',
                            'Yes'
                        ],
                        closeOnClickOutside: false
                    });
                    if (isConfirm) {
                        let response = await this.$store.dispatch('DELETE_ROLE', {id: id});
                        toastr.success(response);
                        this.getRoles();
                    }
                } catch (error) {
                    console.log(error);
                    toastr.error(error);
                }
            },
            editRole(role = null) {
                if(!!role){
                    this.role = deepClone(role);
                }
                console.log(this.role);
                $(this.$refs.roleFormModal).modal('show');
            },
            async saveRole() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('SAVE_ROLE', this.role);
                    toastr.success(response);
                    this.isSending = false;
                    this.getRoles();
                    this.closePreview();
                } catch (error) {
                    console.log(error);
                    toastr.error(error);
                    this.isSending = false;
                    //this.closePreview();
                }
            },

            handleCheckbox(event) {
                let parentId = event.target.getAttribute('data-parent');
                if (parentId == 0) {
                    const id = event.target.getAttribute('id');
                    const checked = !!event.target.checked;
                    const children = $(":checkbox[data-parent=" + id + "]");
                    children.each((index) => {
                        let child = children[index];
                        let value = child.getAttribute('value');
                        //console.log(value);
                        if (!!checked) {
                            if (!this.activeRole.perms.includes(value)) {
                                this.activeRole.perms.push(value);
                            }
                        } else {
                            let permissions = this.activeRole.perms.filter((permission) => {
                                return permission != value;
                            });

                            this.activeRole.perms = permissions;
                        }
                    });
                }
            },
            async updatePermissions() {
                try {
                    // update
                    let response = await this.$store.dispatch('UPDATE_ROLE_PERMISSIONS', {
                        role_id: this.activeRole.id,
                        permissions: this.activeRole.perms,
                    });
                    toastr.success(response);
                    this.getRoles();
                } catch (error) {
                    console.log(error);
                    toastr.error(error);
                }
            },
            closePreview() {
                this.role = new Role();
                $(this.$refs.roleFormModal).modal('hide');
                $('.modal-backdrop').remove();
            },
        },
    }
</script>

<style scoped>
    .parent-role {
        font-weight: bolder !important;
        text-align: left !important;
        font-size: 13pt !important;
    }

    input[type="checkbox"]:not(:checked) {
        left: auto;
        display: block;
        opacity: 5.0;
    }

    input[type="checkbox"]:checked {
        left: auto;
        display: block;
        opacity: 5.0;
    }

    .w-2 {
        width: 2% !important;
    }
</style>
