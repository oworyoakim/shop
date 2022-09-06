import SweetAlert from "sweetalert2";

export default {
    async SET_SNACKBAR({commit}, payload = {}){
        return SweetAlert.fire({
            title: "Response Status",
            ...payload
        });
    },
    async CONFIRM_ACTION({commit}, payload = {}){
        let result = await SweetAlert.fire({
            ...payload,
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
        return result.isConfirmed || false;
    },
    async PROMPT_INPUT_ACTION({commit}, payload = {}){
        let result = await SweetAlert.fire({
            ...payload,
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
        return result.value;
    },
}
