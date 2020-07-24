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
                <p class="card-header">
                    <span class="font-weight-bold text-dark">
                        {{ comment.user.username }}
                    </span>
                    {{ relativeTime }}
                </p>
                <p class="card-body">
                    {{ comment.content }}
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: {
            comment: Object,
        },

        computed: {
            relativeTime: function () {
                return moment(this.comment.created_at).fromNow();
            },
        },
    }
</script>
