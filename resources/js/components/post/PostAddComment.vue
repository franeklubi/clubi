<template>
    <div class="container">
        <div class="d-flex-col">
            <textarea class="form-control" name="name"
                v-model="comment_text"
            ></textarea>

            <div class="d-flex justify-content-end pt-2">
                <input @change="updatePreview" ref="fileupload" type="file">
                <img v-if="picture_file != null"
                    :src="preview_image" style="max-width: 100px"
                >
                <button type="button" class="btn btn-primary"
                    @click="addComment"
                    :disabled="!commentReady"
                >Post</button>
            </div>
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

            addComment() {
                const new_comment = {
                    text: this.comment_text,
                    picture_file: this.picture_file,
                };

                this.$emit('add-comment', new_comment);

                this.comment_text = '';
                this.picture_file = null;
                this.preview_image = '';
                this.$refs.fileupload.value = null;
            }
        },

        computed: {
            commentReady() {
                return this.comment_text.length >= 4
                    || this.picture_file != null;
            }
        },
    }
</script>
