<template>
    <span style="white-space: pre-line;" v-html="sanitizedContent"><button class="btn btn-link" @click="setClicked" v-if="!clicked">Read more</button></span>
</template>

<script>
    import sanitizeHtml from 'sanitize-html';
    import anchorme from 'anchorme';

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

            sanitizedContent() {
                return anchorme(sanitizeHtml(this.tailoredContent, {
                    allowedTags: [],
                    allowedAttributes: {},
                }));
            },
        },
    }
</script>
