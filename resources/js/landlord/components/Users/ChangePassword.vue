<template>
    <div class="card card-primary card-outline">
        <div class="card-header with-border">
            <h3>Change Password</h3>
        </div>
        <div class="card-body">
            <form class="form-horizontal" @submit.prevent="changePassword()" autocomplete="off">
                <div class="form-group">
                    <FormLabel text="Current Password" required/>
                    <TextInput
                        type="password"
                        v-model="credentials.currentPassword"
                        placeholder="Your current password"
                        required
                    />
                </div>
                <div class="form-group">
                    <FormLabel text="New Password" required/>
                    <TextInput
                        type="password"
                        v-model="credentials.loginPassword"
                        placeholder="Your new password"
                        required
                    />
                </div>
                <div class="form-group">
                    <FormLabel text="Confirm New Password" required/>
                    <TextInput
                        type="password"
                        v-model="credentials.loginPasswordConfirmation"
                        placeholder="Repeat your new password"
                        required
                    />
                </div>
                <div class="form-group">
                    <SubmitButton
                        text="Change Password"
                        :disabled="formInvalid"
                        :is-sending="isSending"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import FormLabel from "@/shared/components/FormLabel";
    import TextInput from "@/shared/components/TextInput";
    import SubmitButton from "@/shared/components/SubmitButton";
    import Credentials from "@/models/Credentials";

    export default {
        components: {SubmitButton, TextInput, FormLabel},
        data() {
            return {
                isSending: false,
                credentials: new Credentials(),
            };
        },
        computed: {
            formInvalid(){
                return this.isSending || !(
                    !!this.credentials.currentPassword &&
                    !!this.credentials.loginPassword &&
                    this.credentials.loginPassword === this.credentials.loginPasswordConfirmation
                );
            },
        },
        methods: {
            async changePassword() {
                try {
                    if (this.credentials.loginPassword !== this.credentials.loginPasswordConfirmation) {
                        return this.$store.dispatch("SET_SNACKBAR",{title: 'The two passwords do not match!', icon: 'error'});
                    }
                    this.isSending = true;
                    let response = await this.$store.dispatch("CHANGE_PASSWORD", this.credentials);
                    this.isSending = false;
                    await this.$store.dispatch("SET_SNACKBAR",{title: response, icon: 'success'});
                    location.reload();
                } catch (error) {
                    this.isSending = false;
                    await this.$store.dispatch("SET_SNACKBAR",{title: error, icon: 'error'});
                }
            }
        }
    }
</script>
