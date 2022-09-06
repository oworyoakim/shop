<template>
    <div class="m-0">
        <!-- Modal -->
        <div class="modal modal-default fade show"
             ref="fullScreenModal"
             data-keyboard="false"
             tabindex="-1"
             aria-hidden="true"
             aria-modal="true"
             role="dialog"
             style="display: block;">
            <!-- Full screen modal -->
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info">
                        <h4 class="modal-title text-bold">
                            <slot name="header"></slot>
                        </h4>
                        <button type="button"
                                @click="$emit('closed')"
                                class="close text-bold"
                                data-dismiss="modal"
                                aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body d-flex flex-column">
                        <slot name="body"></slot>
                    </div>
                    <div class="modal-footer">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
export default {
    name: "FullscreenModal"
}
</script>

<style scoped>
.modal-fullscreen {
    width: 100vw;
    max-width: none;
    height: 100%;
    margin: 0;
}

.modal-fullscreen .modal-content {
    height: 100%;
    border: 0;
    border-radius: 0;
    overflow: auto;
}

.modal-fullscreen .modal-header {
    border-radius: 0;
}

.modal-fullscreen .modal-body {

}

.modal-fullscreen .modal-footer {
    border-radius: 0;
}
</style>
