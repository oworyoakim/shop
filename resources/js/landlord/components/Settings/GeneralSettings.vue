<template>
    <div class="card my-2">
        <div class="card-header bg-gradient-secondary">
            <h3 class="card-title">General Settings</h3>
        </div>
        <form @submit.prevent="saveSettings()">
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-4">
                        <FormLabel text="Company Name" required/>
                        <TextInput v-model="settings.company_name" required small/>
                    </div>
                    <div class="col-lg-4">
                        <FormLabel text="Company Short Name" required/>
                        <TextInput v-model="settings.company_short_name" required small/>
                    </div>
                    <div class="col-lg-4">
                        <FormLabel text="Admin Email" required/>
                        <TextInput type="email" v-model="settings.admin_email" required small/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <FormLabel text="Enable Automatic Odds Update?"/>
                        <select v-model="settings.enable_auto_odds_update" class="form-control form-control-sm">
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <FormLabel text="Games and Results URI"/>
                        <TextInput v-model="settings.games_uri" small readonly/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <FormLabel text="Last time odds were automatically updated successfully"/>
                        <TextInput v-model="settings.last_odds_auto_update_time" readonly small/>
                    </div>
                    <div class="col-md-6">
                        <FormLabel text="Last time results were automatically updated successfully"/>
                        <TextInput v-model="settings.last_results_auto_update_time" readonly small/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <FormLabel text="I Love PDF project ID"/>
                        <TextInput v-model="settings.ilovepdf_project_id" small/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <FormLabel text="I Love PDF Secret Key"/>
                        <TextInput v-model="settings.ilovepdf_api_secret_key" small/>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <SubmitButton
                    text="Save Changes"
                    :is-sending="isSending"
                    :disabled="hasNoChanges"
                />
            </div>
        </form>
    </div>
</template>

<script>
import FormLabel from "@/shared/components/FormLabel";
import TextInput from "@/shared/components/TextInput";
import SubmitButton from "@/shared/components/SubmitButton";
export default {
    name: "GeneralSettings",
    components: {SubmitButton, TextInput, FormLabel},
    mounted() {
        this.getSettings();
    },
    data(){
        return {
            settings: {},
            clonedSettings: {},
            companyLogo: null,
            imgUrl: null,
            isSending: false,
        }
    },
    computed: {
        hasNoChanges(){
            return this.isEqual(this.settings, this.clonedSettings);
        },
    },
    methods: {
        handleFileChange(files){
            const file = files[0];
            this.imgUrl = URL.createObjectURL(file);
            this.companyLogo = file;
        },
        async getSettings(){
            try {
                let response = await this.$store.dispatch("GET_SETTINGS");
                this.clonedSettings = this.deepClone(response);
                this.settings = response;
            }catch (error) {
                await this.$store.dispatch("SET_SNACKBAR", {
                    title: error,
                    icon: 'error'
                });
            }
        },

        async saveSettings(){
            try {
                if(!!this.companyLogo){
                    this.settings.logo = this.companyLogo;
                }
                this.isSending = true;
                let response = await this.$store.dispatch("SAVE_SETTINGS", this.settings);
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {
                    title: "Response",
                    text: response,
                });
            }catch (error) {
                this.isSending = false;
                await this.$store.dispatch("SET_SNACKBAR", {
                    title: error,
                    icon: 'error'
                });
            }
        },
    },
}
</script>

<style scoped>

</style>
