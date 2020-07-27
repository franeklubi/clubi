<template>
    <div class="container">
        <div class="">
            <button @click="loadComments" v-if="next_page_url" class="btn">
                Load more replies
            </button>
            <post-comment-item
                v-for="comment in reversedComments" :key="comment.id"
                :comment="comment"
                :user_id="user_id"
                :is_group_admin="is_group_admin"
                @delete-comment="deleteComment"
            />
            <div v-if="feedback" class="alert alert-danger">{{ feedback }}</div>
            <post-add-comment @add-comment="addComment"/>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            post: Object,
            user_id: Number,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                comments_per_page: process.env.MIX_COMMENTS_PER_PAGE,
                comments_to_render: this.post.comments?this.post.comments:[],
                comments_url: `/groups/${this.post.group.id_string}/`
                    +`posts/${this.post.id}/comments`,
                next_page_url: `/groups/${this.post.group.id_string}/`
                    +`posts/${this.post.id}/comments`,
                feedback: '',
            }
        },

        methods: {
            addComment(new_comment) {
                const { text, picture_file } = new_comment;

                let post_data = new FormData();

                post_data.append('content', text);

                if ( picture_file != null ) {
                    post_data.append('picture', picture_file);
                }

                axios.post(this.comments_url, post_data).then((res) => {
                    this.comments_to_render.unshift(res.data);
                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            loadComments() {
                if ( this.next_page_url == null ) {
                    return;
                }
                axios.get(this.next_page_url).then((res) => {
                    this.next_page_url = res.data.next_page_url;
                    this.comments_to_render.push(...res.data.data);
                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            deleteComment(comment) {
                axios.delete(
                    '/groups/'+this.post.group.id_string
                    +'/posts/'+this.post.id
                    +'/comments/'+comment.id
                ).then((res) => {
                    this.comments_to_render.splice(
                        this.comments_to_render.indexOf(res.data.comment), 1
                    );
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            }
        },

        computed: {
            reversedComments() {
                return this.comments_to_render?
                    this.comments_to_render.slice().reverse()
                    : [];
            }
        },

        created() {
            this.loadComments();
        }
    }
</script>
