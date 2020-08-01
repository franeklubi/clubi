<template>
    <div class="">
        <group-add-post
            v-if="(is_member || is_group_admin) && group_id_string"
            @add-post="addPost"
        />
        <div class="container mt-3">
            <div class="alert alert-danger" v-if="feedback">
                {{ feedback }}
            </div>
            <div v-for="post in posts_to_render" :key="post.id">
                <post-item
                    class="mb-2"
                    :post="post"
                    :user_id="user_id"
                    :is_member="is_member"
                    :display_group="display_group"
                    :is_group_admin="is_group_admin"
                    @delete-post="deletePost"
                />
            </div>
            <button @click="loadPosts" v-if="next_page_url"
                class="btn btn-outline-secondary mx-auto d-block mt-3"
            >
                Load more posts
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            posts: Array,
            user_id: Number,
            is_member: Boolean,
            display_group: Boolean,
            is_group_admin: Boolean,
            group_id_string: String,
            passed_next_page_url: String,
        },

        data: function () {
            return {
                feedback: '',
                posts_to_render: this.posts,
                next_page_url: this.passed_next_page_url,
                from_date: this.posts[0]?this.posts[0].created_at:null,
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

                axios.get(this.next_page_url, {
                    params: {'from_date': this.from_date}
                }).then((res) => {
                    this.next_page_url = res.data.next_page_url;
                    this.posts_to_render.push(...res.data.data);
                    this.feedback = '';

                    if ( this.from_date == null ) {
                        this.from_date = posts_to_render[0].created_at;
                    }
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
