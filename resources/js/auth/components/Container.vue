<template>
    <div class="card card-outline">
        <div class="card-header text-center">
            <a href="/" class="h1 text-decoration-none"><b>{{ companyName }}</b></a>
        </div>
        <form @submit.prevent="login()" autocomplete="off">
            <div class="card-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-user"></span>
                        </div>
                    </div>
                    <TextInput
                        type="text"
                        v-model.trim="credentials.loginName"
                        :value="credentials.loginName"
                        placeholder="Username"
                    />
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-lock"></span>
                        </div>
                    </div>
                    <TextInput
                        :type="showPassword ? 'text' : 'password'"
                        v-model.trim="credentials.loginPassword"
                        :value="credentials.loginPassword"
                        placeholder="Password"
                    />
                    <div class="input-group-append" @click="showPassword = !showPassword">
                        <div class="input-group-text">
                            <span v-if="showPassword" class="fas fa-eye-slash"></span>
                            <span v-else class="fas fa-eye"></span>
                        </div>
                    </div>
                </div>
                <p class="text-danger mt-3" v-if="errorMessage">{{ errorMessage }}</p>
            </div>
            <div class="card-footer border-top bg-white">
                <SubmitButton
                    text="Login"
                    :disabled="formInvalid"
                    :is-sending="isSending"
                />
            </div>
        </form>
    </div>
</template>

<script>

import SubmitButton from "../../shared/components/SubmitButton";
import TextInput from "../../shared/components/TextInput";
import Credentials from "../../models/Credentials";

export default {
    name: "Container",
    props: ["companyName"],
    components: {TextInput, SubmitButton},
    data() {
        return {
            errorMessage: null,
            isSending: false,
            showPassword: false,
            credentials: new Credentials(),
        }
    },
    computed: {
        formInvalid() {
            return this.isSending || !this.credentials.loginName || !this.credentials.loginPassword;
        }
    },
    methods: {
        async login() {
            try {
                this.isSending = true;
                let response = await this.$store.dispatch("LOGIN", this.credentials);
                this.isSending = false;
                if (response && response.url) {
                    location.href = response.url; // redirect to intended
                } else {
                    location.reload(); // just reload
                }
            } catch (error) {
                this.isSending = false;
                console.log(error);
                this.errorMessage = error;
            }
        }
    },
}
</script>

<style scoped>

</style>
