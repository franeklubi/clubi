<template>
    <div class="container">
        <group-add-post
            @add-post="addPost"
        />
        {{ feedback }}
        <div v-for="post in posts_render" :key="post.id">
            <group-post
                class="pb-3"
                :post="post"
            />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            posts: Array,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                feedback: '',
                posts_render: this.posts,
            }
        },

        methods: {
            addPost(new_post) {
                const { text } = new_post;

                let post_data = new FormData();

                post_data.append('content', text);

                // assuming you're in /groups/*/
                axios.post('posts', post_data).then((res) => {
                    this.posts_render.unshift(res.data);
                }).catch((err) => {
                    console.log('errorr kurwaawa', err);
                    this.feedback = this.handleAxiosError(err);
                });
            }
        },

        mounted() {
        },
    }
</script>
