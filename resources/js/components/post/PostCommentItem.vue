<template>
    <div class="container">
        <div class="d-flex pb-2">
            <!-- profile picture outside card -->
            <div class="pr-2 pt-2">
                <img :src="comment.user.profile.profile_picture"
                    class="rounded-circle"
                    style="max-width: 30px"
                    :alt="comment.user.username+'_avatar'"
                >
            </div>

            <!-- card -->
            <div class="card" style="max-width: 75%;">
                <span class="card-header">
                    <user-list-item
                        :user="comment.user"
                        :date="comment.created_at"
                        :is_owner="owner.id == comment.user.id"
                        :is_author="comment.user_id == post_author_id"
                        :disable_profile_picture="true"
                        class="hover"
                    >
                        <span v-if="comment.user_id == user_id || is_group_admin"
                            class="pl-3 ml-auto show"
                        >
                            <span @click="deleteCommentEvent" role="button"
                                class="point fa fa-times"
                            />
                        </span>
                    </user-list-item>
                </span>
                <div class="card-body d-flex flex-column">
                    <p class="card-text" v-if="comment.content">
                        <read-more
                            :content="comment.content"
                            :char_cutoff="char_cutoff"
                        />
                    </p>

                    <img v-if="comment.picture"
                        class="mb-2 rounded mw-100"
                        :src="comment.picture"
                        :alt="'comment'+comment.id+'_picture'"
                        style="max-height: 30vh;"
                    >

                    <button class="btn w-auto"
                        :class="likeButtonClass"
                        @click="toggleLike"
                        :disabled="!is_member"
                    >
                        <span class="font-weight-bold">{{likes}}</span>
                        <span :class="likeIconClass"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            post: Object,
            owner: Object,
            comment: Object,
            user_id: Number,
            is_member: Boolean,
            post_author_id: Number,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                like: this.comment.likes,
                likes_link: '/groups/'+this.post.group.id_string+'/posts/'
                    +this.post.id+'/comments/'+this.comment.id+'/likes',


                likes: this.comment.like_count,
                feedback: '',

                char_cutoff: parseInt(process.env.MIX_READ_MORE_CHAR_CUTOFF),

                like_users: this.comment.likes,
                userLiked: false

            }
        },

        methods: {
            deleteCommentEvent() {
                if ( confirm('Are you sure?') ) {
                    this.$emit('delete-comment', this.comment);
                }
            },

            loadLikes() {
                axios.get(this.likes_link).then((res) => {
                    this.likes = res.data.likes;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                })
            },




            toggleLike() {
                axios.post(this.likes_link).then((res) => {
                    if ( res.data.state == 'liked' ) {
                        this.likes = this.likes + 1;
                        this.userLiked = true;
                    } else {
                        this.likes = this.likes - 1;
                        this.userLiked = false;
                    }
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                })
            },
        },

        computed: {
            isLiked() {
                return this.userLiked;
            },

            likeIconClass() {
                return this.isLiked?'fas fa-heart':'far fa-heart';
            },

            likeButtonClass() {
                return this.isLiked?'btn-primary':'btn-outline-primary';
            },
        },

        created() {
            if ( typeof this.comment.likes == 'undefined' ) {
                this.loadLikes();
            }

        }
    }
</script>

<style scoped>
    .show {
        visibility: hidden;
    }

    .hover:hover > .show {
        visibility: visible;
    }
</style>
