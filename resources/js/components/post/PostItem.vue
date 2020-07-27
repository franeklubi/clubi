<template>
    <div class="container">
        <div class="col-8">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="d-flex align-items-baseline hover">
                        <div class="">
                            <img :src="post.user.profile.profile_picture"
                                class="rounded-circle w-100"
                                style="max-width: 40px"
                            >
                        </div>
                        <div class="pl-3">
                            <p>
                                <span class="font-weight-bold text-dark pr-1">
                                    {{ post.user.username }}
                                </span>
                                <a :href="
                                    '/groups/'+post.group.id_string
                                    +'/posts/'+post.id
                                ">
                                    {{ relativeTime }}
                                </a>
                            </p>
                        </div>
                        <span v-if="post.user_id == user_id || is_group_admin"
                            class="align-self-start ml-auto show"
                        >
                            <span @click="deletePostEvent" role="button"
                                class="point fa fa-times"
                            />
                        </span>
                    </div>

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

        methods: {
            deletePostEvent() {
                if ( confirm('Are you sure?') ) {
                    this.$emit('delete-post', this.post);
                }
            },
        },

        computed: {
            relativeTime: function () {
                return moment(this.post.created_at).fromNow();
            }
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
