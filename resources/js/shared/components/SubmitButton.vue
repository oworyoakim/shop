<template>
    <button type="submit"
            class="btn btn-info btn-block"
            @click="$emit('clicked')"
            :disabled="!!disabled">
        <Spinner v-if="!!isSending"/>
        <template v-else>{{text}}</template>
    </button>
</template>

<script>
import Spinner from "./Spinner";
export default {
    name: "SubmitButton",
    components: {Spinner},
    props: {
        text: {type: String, default: "Submit"},
        isSending: {type: Boolean, default: false},
        disabled: {type: Boolean, default: false},
    }
}
</script>

<style scoped>

</style>
