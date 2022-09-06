<template>
    <button type="button"
            class="btn btn-info btn-block"
            @click="$emit('clicked')"
            :disabled="disabled">
        <Spinner v-if="isSending" />
        <slot v-else></slot>
    </button>
</template>

<script>
import Spinner from "./Spinner";
export default {
    name: "Button",
    components: {Spinner},
    props: {
        isSending: {type: Boolean, default: false},
        disabled: {type: Boolean, default: false},
    }
}
</script>

<style scoped>

</style>
