<template>
    <div class="container">
        <div class="input-group mb-2">
            <textarea class="form-control" rows="4"
                v-model="comment_text"
                placeholder="New comment"
            ></textarea>
            <div class="input-group-append">
                <div class="viewport" v-if="preview_image"
                    :style="{
                        'background-image': `url(${preview_image})`,
                        'width': '6vw',
                    }"
                >
                    <span class="text-light fas fa-times cancel"
                        @click="deletePicture"
                    />
                </div>
                <label v-else :for="'file_input'+_uid"
                    class="btn btn-outline-success h-100 d-flex"
                    style="width: 6vw; font-size: 1rem"
                >
                    <div class="far fa-image my-auto mx-auto" />
                </label>
            </div>
        </div>

        <!-- hidden input -->
        <input @change="updatePreview" type="file"
            class="inputfile" :id="'file_input'+_uid" ref="fileupload"
        >

        <button type="button" class="btn btn-outline-primary w-100"
            @click="addComment"
            :disabled="!commentReady"
        >Comment</button>
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
            deletePicture() {
                this.picture_file = null;
                this.preview_image = '';
            },

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
