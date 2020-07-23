<template>
    <div class="container">
        <group-add-post
            v-if="is_member"
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
            is_member: Boolean,
            is_group_admin: Boolean,
            group_id_string: String,
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

                let post_path = '/groups/'+this.group_id_string+'/posts';

                let post_data = new FormData();

                post_data.append('content', text);

                axios.post(post_path, post_data).then((res) => {
                    this.posts_render.unshift(res.data);
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            }
        },

        mounted() {
        },
    }
</script>
