<template>
    <div class="m-0">
        <div class="modal fade show" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info">
                        <div class="h4  w-100">
                            <slot name="header"></slot>
                        </div>
                        <button type="button" @click="$emit('closed')"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span class="text-white text-bold" aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot name="body"></slot>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <slot name="footer"></slot>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
export default {
    name: "MainModal",
}
</script>

<style scoped>

</style>
