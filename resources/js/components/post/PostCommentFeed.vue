<template>
    <div class="card-footer"
        v-if="canComment || reversedComments.length > 0"
    >
        <button @click="loadComments" v-if="next_page_url"
            class="btn btn-link mb-3 mx-auto d-block"
        >
            Load more replies
        </button>
        <post-comment-item
            v-for="comment in reversedComments" :key="comment.id"
            :post="post"
            :owner="owner"
            :comment="comment"
            :user_id="user_id"
            :is_member="is_member"
            :post_author_id="parseInt(post.user_id)"
            :is_group_admin="is_group_admin"
            @delete-comment="deleteComment"
        />
        <div v-if="feedback" class="alert alert-danger">{{ feedback }}</div>
        <post-add-comment v-if="canComment" @add-comment="addComment"/>
    </div>
</template>

<script>
    export default {
        props: {
            post: Object,
            owner: Object,
            user_id: Number,
            is_member: Boolean,
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
                from_date: this.post.comments?this.post.comments[0].created_at:null,
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

                axios.get(this.next_page_url, {
                    params: {'from_date': this.from_date}
                }).then((res) => {
                    this.next_page_url = res.data.next_page_url;
                    this.comments_to_render.push(...res.data.data);
                    this.feedback = '';

                    if (this.from_date == null && this.comments_to_render[0]) {
                        this.from_date = this.comments_to_render[0].created_at;
                    }
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
                    let index = this.comments_to_render.findIndex((comment) => {
                        return comment.id == res.data.comment.id
                    });

                    this.comments_to_render.splice(index, 1);
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
            },

            canComment() {
                return this.is_member || this.is_group_admin;
            },
        },

        created() {
            this.loadComments();
        }
    }
</script>
