<template>
    <div class="container">
        <div class="form-group">
            <button id="join_leave" type="button"
                :class="['btn', buttonColor]"
                class="w-100"
                @click="emitToggle"
            >
                {{ buttonText }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            is_member: Boolean
        },

        methods: {
            emitToggle() {
                this.$emit('toggle');
            },
        },

        computed: {
            buttonText() {
                return this.is_member?'Leave':'Join';
            },

            buttonColor() {
                return this.is_member?'btn-secondary':'btn-primary';
            }
        },

        mounted() {
        },
    }
</script>
