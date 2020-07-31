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
                        <span v-if="display_group" style="white-space: nowrap;">
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

                    <p class="card-text">
                        <read-more
                            :content="post.content"
                            :char_cutoff="char_cutoff"
                        />
                    </p>

                    <img style="max-height: 50vh;"
                        class="mb-2 rounded" :src="post.picture"
                        v-if="post.picture"
                    >

                    <button class="btn w-100"
                        :class="likeButtonClass"
                        @click="toggleLike"
                        :disabled="!is_member"
                    >
                        <span class="font-weight-bold">{{ likes.length }}</span>
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
                owner: this.post.group.owner,
                likes: this.post.likes,
                feedback: '',
                char_cutoff: parseInt(process.env.MIX_READ_MORE_CHAR_CUTOFF),
            }
        },

        methods: {
            deletePostEvent() {
                if ( confirm('Are you sure?') ) {
                    this.$emit('delete-post', this.post);
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
                        this.likes.push(res.data.like);
                    } else {
                        let index = this.likes.findIndex((like) => {
                            return like.id == res.data.like.id;
                        });

                        this.likes.splice(index, 1);
                    }
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                })
            },
        },

        computed: {
            isLiked() {
                let index = this.likes.findIndex((like) => {
                    return like.user_id == this.user_id;
                });

                return index>-1;
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
