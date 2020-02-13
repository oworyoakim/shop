<template>
    <div class="login-box-body shadow p-3 mb-5">
        <p v-if="resettingPassword" class="login-box-msg text-uppercase">Recover password</p>
        <p v-else class="login-box-msg text-uppercase text-bold">Sign in to start your session</p>
        <div v-if="!!message" class="alert alert-danger text-center">{{message}}</div>
        <div v-else-if="!!errorMessage" class="alert alert-danger text-center">{{errorMessage}}</div>
        <template v-if="resettingPassword">
            <form @submit.prevent="resetPassword()" class="form-element">
                <div class="form-group has-feedback">
                    <input type="email"
                           v-model="credentials.passwordResetEmail"
                           class="form-control form-control-lg"
                           autocomplete="off"
                           placeholder="Email"
                           required>
                    <span class="fa fa-envelope fa-2x form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-6 text-center">
                        <button @click="resettingPassword = false" type="button" class="btn btn-dark btn-block text-uppercase">Back</button>
                    </div>
                    <!-- /.col -->
                    <div class="col-6 text-center">
                        <button type="submit" :disabled="!!!credentials.passwordResetEmail.trim()" class="btn btn-info btn-block text-uppercase">Reset</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </template>
        <template v-else>
            <form @submit.prevent="login()" class="form-element">
                <div class="form-group has-feedback">
                    <input v-model="credentials.loginName"
                           type="text"
                           class="form-control form-control-lg"
                           placeholder='Username'
                           autocomplete="off"
                           required>
                    <span class="fa fa-user-circle fa-2x form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input v-model="credentials.loginPassword"
                           type="password"
                           class="form-control form-control-lg"
                           placeholder='Password'
                           autocomplete="off"
                           required>
                    <span class="fa fa-lock fa-2x form-control-feedback"></span>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-12 text-center">
                        <button type="submit"
                                :disabled="isSending || !(!!credentials.loginName.trim() && !!credentials.loginPassword.trim())"
                                class="btn btn-info btn-lg btn-block margin-top-10 text-uppercase">Login
                        </button>
                    </div>
                    <!-- /.col -->
                    <div class="col-12 mt-2 text-right text-info">
                        <div class="fog-pwd">
                            <a @click="resettingPassword = true" class="text-info" href="javascript:void(0)"><i class="ion ion-locked"></i> Forgot password?</a><br>
                        </div>
                    </div>
                </div>
            </form>
        </template>

    </div>
    <!-- /.login-box-body -->
</template>

<script>
    export default {
        props: {
            returnUrl: String,
            message: String,
        },
        data() {
            return {
                credentials: {
                    loginName: '',
                    loginPassword: '',
                    passwordResetEmail: '',
                },
                isSending: false,
                resettingPassword: false,
                errorMessage: '',
            }
        },
        methods: {
            async login() {
                try {
                    this.isSending = true;
                    let response = await this.$store.dispatch('LOGIN', this.credentials);
                    toastr.success(response);
                    this.isSending = false;
                    if (this.returnUrl) {
                        window.location.href = this.returnUrl;
                    } else {
                        window.location.reload();
                    }
                } catch (error) {
                    console.log(error);
                    this.isSending = false;
                    this.errorMessage = error;
                    setTimeout(()=>{
                        this.errorMessage='';
                    },5000);
                }
            },
            async resetPassword(){

            }
        }
    }
</script>

<style scoped>

</style>
