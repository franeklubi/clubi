<template>
    <div class="container">
        <group-add-post
            v-if="is_member || is_group_admin"
            @add-post="addPost"
        />
        {{ feedback }}
        <div v-for="post in posts_to_render" :key="post.id">
            <post-item
                class="pb-3"
                :post="post"
                :owner="owner"
                :user_id="user_id"
                :is_member="is_member"
                :is_group_admin="is_group_admin"
                @delete-post="deletePost"
            />
        </div>
        <button @click="loadPosts" v-if="next_page_url" class="btn">
            Load more posts
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            posts: Array,
            owner: Object,
            user_id: Number,
            is_member: Boolean,
            is_group_admin: Boolean,
            group_id_string: String,
            passed_next_page_url: String,
        },

        data: function () {
            return {
                feedback: '',
                posts_to_render: this.posts,
                next_page_url: this.passed_next_page_url,
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
            },

            loadPosts() {
                if ( this.next_page_url == null ) {
                    return;
                }
                axios.get(this.next_page_url).then((res) => {
                    this.next_page_url = res.data.next_page_url;
                    this.posts_to_render.push(...res.data.data);
                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            deletePost(post) {
                axios.delete('/groups/'+post.group.id_string+'/posts/'+post.id)
                    .then((res) => {
                        let index = this.posts_to_render.findIndex((post) => {
                            return post.id == res.data.post.id
                        });

                        this.posts_to_render.splice(index, 1);
                    }).catch((err) => {
                        this.feedback = this.handleAxiosError(err);
                    });
            },
        },
    }
</script>
