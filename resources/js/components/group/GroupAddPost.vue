<template>
    <div class="container">
        <div class="card no-border-top">
            <div class="card-body">
                <div class="input-group mb-3">
                    <textarea class="form-control" rows="4" id="post"
                        v-model="post_text"
                        placeholder="New post"
                    ></textarea>
                    <div class="input-group-append">
                        <div class="viewport" v-if="preview_image"
                            :style="{
                                'background-image': `url(${preview_image})`,
                                'width': '5vw',
                            }"
                        >
                            <span class="text-light fas fa-times cancel"
                                @click="deletePicture"
                            />
                        </div>
                        <label v-else for="file_input"
                            class="btn btn-outline-success h-100 d-flex"
                        >
                            <div class="far fa-image my-auto" />
                        </label>
                    </div>
                </div>

                <!-- hidden input -->
                <input @change="updatePreview" type="file"
                    class="inputfile" id="file_input" ref="fileupload"
                >

                <button type="button" class="btn btn-outline-primary w-100"
                    @click="addPost"
                    :disabled="!postReady"
                >Post</button>
            </div>
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
            deletePicture() {
                this.picture_file = null;
                this.preview_image = '';
                console.log('success');
            },

            updatePreview(event) {
                this.picture_file = event.target.files[0];
                this.preview_image = window.URL.createObjectURL(
                    this.picture_file
                );
                console.log('updated');
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

<style media="screen">
    .no-border-top {
        border-top: 0;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .viewport {
        position: relative;

        background-size: cover;
        background-position: center;

        border-top-right-radius: 0.3rem;
        border-bottom-right-radius: 0.3rem;
    }

    .cancel {
        position: absolute;
        top: 0.2rem;
        right: 0.4rem;
        cursor: pointer;
        text-shadow: 2px 2px 4px black;
    }

    .inputfile {
        display: none;
    }
</style>
