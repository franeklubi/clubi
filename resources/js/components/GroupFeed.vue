<template>
    <div class="container">
        <group-add-post
            v-if="is_member"
            @add-post="addPost"
        />
        {{ feedback }}
        <div v-for="post in posts_to_render" :key="post.id">
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
                posts_to_render: this.posts,
            }
        },

        methods: {
            addPost(new_post) {
                const { text, picture_file } = new_post;

                let post_path = '/groups/'+this.group_id_string+'/posts';

                let post_data = new FormData();

                post_data.append('content', text);
                if ( picture_file != null ) {
                    post_data.append('picture', picture_file);
                }

                axios.post(post_path, post_data).then((res) => {
                    this.posts_to_render.unshift(res.data);
                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            }
        },

        mounted() {
            console.log(this.posts_to_render);
        },
    }
</script>
