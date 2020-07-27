<template>
    <div class="container">
        <div class="col-8">
            <div class="card">
                <div class="card-body pt-3">
                    <user-header
                        :user="post.user"
                        :date="post.created_at"
                        :link="post_link"
                        :is_group_admin="is_group_admin"
                        class="hover"
                    >
                        <span v-if="post.user_id == user_id || is_group_admin"
                            class="align-self-start ml-auto show"
                        >
                            <span @click="deletePostEvent" role="button"
                                class="point fa fa-times"
                            />
                        </span>
                    </user-header>

                    <hr>

                    <p class="card-text">
                        {{ post.content }}
                    </p>
                </div>
                <img class="w-100" :src="post.picture">
                <post-comment-feed
                    :post="post"
                    :is_member="is_member"
                    :user_id="user_id"
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
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                post_link: '/groups/'+this.post.group.id_string
                    +'/posts/'+this.post.id,
            }
        },

        methods: {
            deletePostEvent() {
                if ( confirm('Are you sure?') ) {
                    this.$emit('delete-post', this.post);
                }
            },
        },
    }
</script>

<style scoped>
    .show {
        display: none;
    }

    .hover:hover > .show {
        display: block;
    }
</style>
