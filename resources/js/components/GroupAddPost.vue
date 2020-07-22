<template>
    <div class="container">
        <div class="form-group">
            <label for="post">New post:</label>
            <textarea class="form-control" rows="4" id="post"
                v-model="post_text"
            ></textarea>
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
                post_text: ''
            }
        },

        methods: {
            addPost() {
                const new_post = {
                    text: this.post_text
                }

                this.$emit('add-post', new_post);

                this.post_text ='';
            },
        },

        computed: {
            postReady() {
                return this.post_text.length > 4;
            }
        },

        mounted() {
        },
    }
</script>
