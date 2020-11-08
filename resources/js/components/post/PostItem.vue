<template>
    <div class="container">
        <div class="">
            <div class="card">
                <div class="alert alert-danger" v-if="feedback">
                    {{ feedback }}
                </div>
                <div class="card-body pt-3">
                    <user-list-item
                        :user="post.user"
                        :date="post.created_at"
                        :link="post_link"
                        :is_owner="owner.id == post.user.id"
                        class="hover"
                    >
                        <span v-if="display_group" class="truncate">
                            <span class="ml-2 fas fa-arrow-right"
                                :href="group_link"
                            >
                            </span>
                            <a :href="group_link"
                                class="ml-1 font-weight-bold"
                            >{{ post.group.name }}</a>
                        </span>

                        <span v-if="post.user_id == user_id || is_group_admin"
                            class="align-self-start ml-auto show"
                        >
                            <span @click="deletePostEvent" role="button"
                                class="point fa fa-times"
                            />
                        </span>
                    </user-list-item>

                    <hr>

                    <p class="card-text" v-if="post.content">
                        <read-more
                            :content="post.content"
                            :char_cutoff="char_cutoff"
                        />
                    </p>

                    <img style="max-height: 50vh;"
                        class="mb-2 rounded mw-100" :src="post.picture"
                        v-if="post.picture"
                        :alt="'post'+post.id+'_picture'"
                    >

                    <button class="btn w-100"
                        :class="likeButtonClass"
                        @click="toggleLike"
                        :disabled="!is_member"
                    >
                        <span class="font-weight-bold">{{ likes }}</span>
                        <span :class="likeIconClass"></span>
                    </button>
                </div>

                <post-comment-feed
                    :post="post"
                    :owner="owner"
                    :user_id="user_id"
                    :is_member="is_member"
                    :is_group_admin="is_group_admin"
                />
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: {
            post: Object,
            user_id: Number,
            is_member: Boolean,
            display_group: Boolean,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                group_link: '/groups/'+this.post.group.id_string,
                post_link: '/groups/'+this.post.group.id_string
                    +'/posts/'+this.post.id,
                likes_link: '/groups/'+this.post.group.id_string
                    +'/posts/'+this.post.id+'/likes',
                like_users_link: '/groups/'+this.post.group.id_string
                    +'/posts/'+this.post.id+'/likes/users',
                owner: this.post.group.owner,
                likes: this.post.like_count,
                feedback: '',
                char_cutoff: parseInt(process.env.MIX_READ_MORE_CHAR_CUTOFF),
                like_users: this.post.likes,
                userLiked: false
            }
        },

        methods: {
            deletePostEvent() {
                if ( !confirm('Are you sure?') ) {
                    return;
                }

                axios.delete(
                    '/groups/'+this.post.group.id_string+'/posts/'+this.post.id
                ).then((res) => {
                    this.$emit('delete-post', this.post);
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            loadLikes() {
                axios.get(this.likes_link).then((res) => {
                    this.likes = res.data.likes;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                })
            },

            loadLikeUsers() {
                axios.get(this.like_users_link).then((res) => {
                    if (res.data.likes) {
                        let index = res.data.likes.findIndex((like) => {
                            return like.user_id == this.user_id;
                        });
                        this.userLiked = index ? false : true;
                    }
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
            if ( typeof this.post.likes == 'undefined' ) {
                this.loadLikes();
            }

            this.loadLikeUsers();
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

    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
