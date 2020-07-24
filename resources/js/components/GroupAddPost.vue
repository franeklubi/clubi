<template>
    <div class="container">
        <div class="form-group">
            <label for="post">New post:</label>
            <textarea class="form-control" rows="4" id="post"
                v-model="post_text"
            ></textarea>

            <input @change="updatePreview" ref="fileupload" type="file">
            <img v-if="picture_file != null"
                :src="preview_image" style="max-width: 100px"
            >

            <button type="button" class="btn btn-primary"
                @click="addPost"
                :disabled="!postReady"
            >Post</button>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                post_text: '',
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

            addPost() {
                const new_post = {
                    text: this.post_text,
                    picture_file: this.picture_file,
                }

                this.$emit('add-post', new_post);

                this.post_text = '';
                this.picture_file = null;
                this.preview_image = '';
                this.$refs.fileupload.value = null;
            },
        },

        computed: {
            postReady() {
                return this.post_text.length > 4 || this.picture_file != null;
            }
        },

        mounted() {
        },
    }
</script>
