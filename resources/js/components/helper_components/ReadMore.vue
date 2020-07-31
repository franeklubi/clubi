<template>
    <span style="white-space: pre-line;">{{ tailoredContent }}<button class="btn btn-link" @click="setClicked" v-if="!clicked">Read more</button></span>
</template>

<script>
    export default {
        props: {
            content: String,
            char_cutoff: Number,
        },

        data: function () {
            return {
                clicked: this.content.length<this.char_cutoff,
            }
        },

        methods: {
            setClicked() {
                this.clicked = true;
            },
        },

        computed: {
            tailoredContent() {
                if ( this.clicked ) {
                    return this.content;
                }

                return this.content.substr(0, this.char_cutoff)+'...';
            },
        },
    }
</script>
