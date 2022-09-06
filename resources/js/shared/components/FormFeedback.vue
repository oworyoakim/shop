<template>
    <span class="form-feedback">{{text}}</span>
</template>

<script>
export default {
    name: "FormFeedback",
    props: {
        text: {type: String, required: true}
    }
}
</script>

<style scoped>
    .form-feedback {
        width: 100%;
        margin-top: .25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
