<template>
    <div class="container">
        <div class="d-flex pb-2">
            <div class="pr-2">
                <img :src="comment.user.profile.profile_picture"
                    class="rounded-circle w-100"
                    style="max-width: 40px"
                >
            </div>
            <div class="card w-100">
                <p class="card-header hover d-flex">
                    <span class="font-weight-bold text-dark pr-2">
                        {{ comment.user.username }}
                    </span>
                    {{ relativeTime }}
                    <span v-if="comment.user_id == user_id || is_group_admin"
                        class="ml-auto show"
                    >
                        <span @click="deleteCommentEvent" role="button"
                            class="point fa fa-times"
                        />
                    </span>
                </p>
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
            comment: Object,
            user_id: Number,
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
