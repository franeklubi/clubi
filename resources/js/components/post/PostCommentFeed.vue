<template>
    <div class="container">
        <div class="">
            <post-add-comment @add-comment="addComment"/>
        </div>
    </div>
</template>

<script>
    export default {
        props: {

        },

        data: function () {
            return {
                comment_text: '',
                picture_file: null,
                preview_image: '',
            }
        },

        methods: {
            updatePreview(event) {
                this.picture_file = event.target.files[0];
                this.preview_image = window.URL.createObjectURL(
                    this.picture_file
                );
            },

            addComment(new_comment) {
                const { text, picture_file } = new_comment;

                console.log(text, picture_file, 'chuj');
            },
        },

        computed: {
            commentReady() {
                return this.comment_text.length > 4
                    || this.picture_file != null;
            }
        },
    }
</script>
