<template>
    <form v-if="isEditing" @submit.prevent="saveUser()" autocomplete="off">
        <MainModal @closed="resetForm()">
            <template slot="header">User Form</template>
            <template slot="body">
                <div class="card card-outline card-tabs border-0">
                    <div class="card-header p-0 pt-1 border-bottom-0">
                        <ul class="nav nav-tabs" id="user-form-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#user-info-tab" role="tab"
                                   aria-controls="user-form-tabs" aria-selected="true">User Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#user-permissions-tab" role="tab"
                                   aria-controls="user-form-tabs" aria-selected="false">Permissions</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="users-tab-ontent">
                            <div class="tab-pane fade show active" id="user-info-tab" role="tabpanel">
                                <div class="row">
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="First Name" required/>
                                        <TextInput
                                            v-model.trim="user.firstName"
                                            placeholder="First Name"
                                            required
                                        />
                                        <div class="text-red" v-if="errors.firstName">{{errors.firstName}}</div>
                                    </div>
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="Last Name" required/>
                                        <TextInput
                                            v-model.trim="user.lastName"
                                            placeholder="Last Name"
                                            required
                                        />
                                        <div class="text-red" v-if="errors.lastName">{{errors.lastName}}</div>
                                    </div>
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="Email Address"/>
                                        <TextInput
                                            type="email"
                                            v-model.trim="user.email"
                                            placeholder="Email address"
                                        />
                                        <div class="text-red" v-if="errors.email">{{errors.email}}</div>
                                    </div>
                                    <div class="col-12 text-bold">
                                        <p class="border-bottom d-block w-100">User Login Credentials</p>
                                    </div>
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="Username" required/>
                                        <TextInput
                                            :value="user.username"
                                            v-model="user.username"
                                            placeholder="Username"
                                            required
                                            :readonly="!!user.id"
                                        />
                                        <div class="text-red" v-if="errors.username">{{errors.username}}</div>
                                    </div>
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="Password" :required="!user.id"/>
                                        <TextInput
                                            type="password"
                                            v-model="user.password"
                                            placeholder="****"
                                            :required="!user.id"
                                        />
                                        <div class="text-red" v-if="errors.password">{{errors.password}}</div>
                                    </div>
                                    <div class="form-group  col-xl-4 col-lg-6 col-12">
                                        <FormLabel text="Confirm Password" :required="!user.id"/>
                                        <TextInput
                                            type="password"
                                            v-model="user.cpassword"
                                            placeholder="****"
                                            :required="!user.id"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade"
                                 id="user-permissions-tab"
                                 role="tabpanel">
                                <div class="row">
                                    <template v-for="permission in permissions">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="row">
                                                <div class="col-2">
                                                    <input
                                                        type="checkbox"
                                                        v-model="user.permissions[permission.slug]"
                                                        class="custom-checkbox"
                                                        @change="toggleChildren(permission)">
                                                </div>
                                                <div class="col-10">
                                                    <template v-if="permission.children.length > 0">
                                                        <div class="float-left mr-2">
                                                            <span class="fas fa-caret-right"></span>
                                                        </div>
                                                        <a :href="`#child-permissions-${permission.id}`"
                                                           class="collapsed"
                                                           role="button"
                                                           data-toggle="collapse"
                                                           data-parent="#user-permissions-tab"
                                                           aria-expanded="false">
                                                            {{ permission.name }} ({{ permission.children.length }})
                                                        </a>
                                                    </template>
                                                    <template v-else>
                                                        {{ permission.name }}
                                                    </template>
                                                    <template v-if="permission.children.length > 0">
                                                        <div :id="`child-permissions-${permission.id}`" class="panel-collapse in collapse">
                                                        <template v-for="child in permission.children">
                                                            <div class="row">
                                                                <div class="col-2">
                                                                    <input
                                                                        type="checkbox"
                                                                        v-model="user.permissions[child.slug]"
                                                                        class="custom-checkbox">
                                                                </div>
                                                                <div class="col-10">{{ child.name }}</div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template slot="footer">
                <SubmitButton
                    text="Save"
                    :is-sending="isSending"
                    :disabled="formInvalid"
                />
            </template>
        </MainModal>
    </form>
</template>

<script>
import {mapGetters} from "vuex";
import MainModal from "@/shared/components/MainModal";
import SelectBoxInput from "@/shared/components/SelectBoxInput";
import SubmitButton from "@/shared/components/SubmitButton";
import FormLabel from "@/shared/components/FormLabel";
import TextInput from "@/shared/components/TextInput";
import {EventBus} from "@/utils/httpClient";
import Admin from "../../../models/Admin";

export default {
    name: "UserForm",
    components: {
        SelectBoxInput,
        TextInput,
        FormLabel,
        SubmitButton,
        MainModal,
    },
    mounted() {
        EventBus.$on('EDIT_USER', this.editUser);
        this.$store.dispatch("GET_FORM_OPTIONS", {
            options: "permissions",
        });
    },
    computed: {
        ...mapGetters({
            formOptions: "FORM_OPTIONS",
        }),
        permissions(){
            if(!!!this.formOptions) {
                return [];
            }
            return this.formOptions.permissions;
        },
        formInvalid() {
            return this.isSending ||
                !this.user.firstName ||
                !this.user.lastName ||
                !this.user.username ||
                (!this.user.id && !this.user.password) ||
                (!!this.user.password && this.user.password !== this.user.cpassword);
        },
        hasSomePermissions() {
            return Object.keys(this.user.permissions).some((permission) => {
                return !!this.user.permissions[permission]
            });
        },
    },
    data() {
        return {
            user: new Admin(),
            isEditing: false,
            isSending: false,
            errors: {},
            errorMessage: null,
        }
    },
    methods: {
        editUser(user = null) {
            if (user) {
                this.user = new Admin(user);
                this.setMissingPermissionsToUser();
            } else {
                this.user = new Admin();
            }
            this.isEditing = true;
        },
        resetForm() {
            this.user = new Admin();
            this.isEditing = false;
        },
        toggleChildren(permission) {
            for (let child of permission.children) {
                this.user.permissions[child.slug] = !!this.user.permissions[permission.slug];
            }
        },
        async saveUser() {
            try {
                if (!this.hasSomePermissions) {
                    await this.$store.dispatch("SET_SNACKBAR", {
                        title: "Admin users must have some permissions set in the permissions tab!",
                        icon: 'error'
                    });
                    return;
                }
                this.errorMessage = null;
                this.errors = {};
                this.isSending = true;
                let response = await this.$store.dispatch('SAVE_USER', this.user);
                await this.$store.dispatch("SET_SNACKBAR", {title: response, icon: 'success'});
                this.isSending = false;
                this.resetForm();
                EventBus.$emit('USER_SAVED');
            } catch (error) {
                console.log(error);
                this.isSending = false;
                this.errors = error.errors;
                this.errorMessage = error.message || error;
                await this.$store.dispatch("SET_SNACKBAR", {title: this.errorMessage, icon: 'error'});
            }
        },
        setMissingPermissionsToUser() {
            let permissions = this.deepClone(this.user.permissions);
            this.permissions.forEach((permission) => {
                permissions[permission.slug] = permissions[permission.slug] || false;
            });
            this.$set(this.user, "permissions", permissions);
        },
    },
}
</script>

<style scoped>

</style>
