<template>
    <div class="container">
        <div class="d-flex pb-2">
            <!-- profile picture outside card -->
            <div class="pr-2 pt-1">
                <img :src="comment.user.profile.profile_picture"
                    class="rounded-circle w-100"
                    style="max-width: 30px"
                >
            </div>

            <!-- card -->
            <div class="card w-100">
                <span class="card-header">
                    <user-header
                        :user="comment.user"
                        :date="comment.created_at"
                        :is_owner="owner.id == comment.user.id"
                        :is_author="comment.user_id == post_author_id"
                        :disable_profile_picture="true"
                        class="hover"
                    >
                        <span v-if="comment.user_id == user_id || is_group_admin"
                            class="ml-auto show"
                        >
                            <span @click="deleteCommentEvent" role="button"
                                class="point fa fa-times"
                            />
                        </span>
                    </user-header>
                </span>
                <div v-if="comment.content" class="card-body">
                    <p class="card-text">
                        {{ comment.content }}
                    </p>
                </div>
                <img v-if="comment.picture"
                    class="w-100 p-1" :src="comment.picture" alt=""
                >
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: {
            owner: Object,
            comment: Object,
            user_id: Number,
            post_author_id: Number,
            is_group_admin: Boolean,
        },

        methods: {
            deleteCommentEvent() {
                if ( confirm('Are you sure?') ) {
                    this.$emit('delete-comment', this.comment);
                }
            },
        },

        computed: {
            relativeTime: function () {
                return moment(this.comment.created_at).fromNow();
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
