<template>
    <form v-if="isEditing" @submit.prevent="saveTenant()" autocomplete="off">
        <MainModal @closed="resetForm()">
            <template slot="header">Tenant Form</template>
            <template slot="body">
                <div class="row">
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Name" required/>
                        <TextInput
                            v-model.trim="tenant.name"
                            placeholder="Name"
                            :is-invalid="fieldIsInvalid('name')"
                            required
                        />
                        <FormFeedback v-if="fieldIsInvalid('name')" :text="errors.name"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Email"/>
                        <TextInput
                            type="email"
                            v-model.trim="tenant.email"
                            placeholder="Email"
                            :is-invalid="fieldIsInvalid('email')"
                        />
                        <FormFeedback v-if="fieldIsInvalid('email')" :text="errors.email"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Phone" required/>
                        <TextInput
                            v-model.trim="tenant.phone"
                            placeholder="Phone"
                            :is-invalid="fieldIsInvalid('phone')"
                            required
                        />
                        <FormFeedback v-if="fieldIsInvalid('phone')" :text="errors.phone"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Subdomain" required/>
                        <TextInput
                            v-model.trim="tenant.subdomain"
                            placeholder="Subdomain"
                            :is-invalid="fieldIsInvalid('subdomain')"
                            required
                        />
                        <FormFeedback v-if="fieldIsInvalid('subdomain')" :text="errors.subdomain"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Country" required/>
                        <TextInput
                            v-model.trim="tenant.country"
                            placeholder="Country"
                            :is-invalid="fieldIsInvalid('country')"
                            required
                        />
                        <FormFeedback v-if="fieldIsInvalid('country')" :text="errors.country"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="City"/>
                        <TextInput
                            v-model.trim="tenant.city"
                            placeholder="City"
                            :is-invalid="fieldIsInvalid('city')"
                        />
                        <FormFeedback v-if="fieldIsInvalid('city')" :text="errors.city"/>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Address"/>
                        <TextInput
                            v-model.trim="tenant.address"
                            placeholder="Address"
                            :is-invalid="fieldIsInvalid('address')"
                        />
                        <FormFeedback v-if="fieldIsInvalid('address')" :text="errors.address"/>
                    </div>
                    <div class="col-12 text-bold">
                        <p class="border-bottom d-block w-100">Admin User Login Credentials</p>
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Username" required/>
                        <TextInput
                            v-model.trim="tenant.credentials.loginName"
                            placeholder="Admin login username"
                            :is-invalid="fieldIsInvalid('credentials.loginName')"
                            required
                        />
                        <FormFeedback
                            v-if="fieldIsInvalid('credentials.loginName')"
                            :text="errors['credentials.loginName']"
                        />
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Password" required/>
                        <TextInput
                            type="password"
                            v-model.trim="tenant.credentials.loginPassword"
                            placeholder="Admin login password"
                            :is-invalid="fieldIsInvalid('credentials.loginPassword')"
                            required
                        />
                        <FormFeedback
                            v-if="fieldIsInvalid('credentials.loginPassword')"
                            :text="errors['credentials.loginPassword']"
                        />
                    </div>
                    <div class="form-group col-xl-4 col-lg-6 col-12">
                        <FormLabel text="Confirm Password" required/>
                        <TextInput
                            type="password"
                            v-model.trim="tenant.credentials.loginPasswordConfirmation"
                            placeholder="Confirm password"
                            :is-invalid="fieldIsInvalid('credentials.loginPasswordConfirmation')"
                            required
                        />
                        <FormFeedback
                            v-if="fieldIsInvalid('credentials.loginPasswordConfirmation')"
                            :text="errors['credentials.loginPasswordConfirmation']"
                        />
                    </div>
                </div>
            </template>
            <template slot="footer">
                <SubmitButton
                    :disabled="formInvalid"
                    :is-sending="isSending"
                    :text="tenant.id ? 'Save Changes' : 'Create Tenant'"
                />
            </template>
        </MainModal>
    </form>
</template>

<script>
import MainModal from "../../../shared/components/MainModal";
import SubmitButton from "../../../shared/components/SubmitButton";
import TextInput from "../../../shared/components/TextInput";
import FormLabel from "../../../shared/components/FormLabel";
import {EventBus} from "../../../utils/httpClient";
import Tenant from "../../../models/Tenant";
import FormFeedback from "../../../shared/components/FormFeedback";

export default {
    components: {FormFeedback, TextInput, FormLabel, SubmitButton, MainModal},
    mounted() {
        EventBus.$on("EDIT_TENANT", this.editTenant);
    },
    computed: {
        formInvalid() {
            return this.isSending ||
                !this.tenant.name ||
                !this.tenant.phone ||
                !this.tenant.subdomain ||
                !this.tenant.country ||
                !this.tenant.credentials.loginName ||
                !this.tenant.credentials.loginPassword;
        },
        fieldIsInvalid() {
            return (field) => {
                return this.errors && this.errors[field];
            }
        },
    },
    data() {
        return {
            tenant: new Tenant(),
            isEditing: false,
            isSending: false,
            errors: null,
        }
    },
    methods: {
        editTenant(tenant = null) {
            this.tenant = new Tenant(tenant || {});
            this.isEditing = true;
        },
        resetForm() {
            this.isEditing = false;
            this.tenant = new Tenant();
            this.errors = null;
        },
        async saveTenant() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch("SAVE_TENANT", this.tenant);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: response,
                    icon: 'success'
                });
                this.resetForm();
                this.$emit("saved");
            } catch (errorResponse) {
                this.isSending = false;
                console.log(errorResponse);
                await this.$store.dispatch("SET_SNACKBAR", {
                    text: errorResponse.message,
                    icon: 'error',
                });
                this.errors = errorResponse.errors;
            }
        },
    }
}
</script>

<style scoped>

</style>
