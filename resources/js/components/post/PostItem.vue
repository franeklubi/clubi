<template>
    <div class="container">
        <div class="col-8">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="d-flex align-items-baseline">
                        <div class="">
                            <img :src="post.user.profile.profile_picture"
                                class="rounded-circle w-100"
                                style="max-width: 40px"
                            >
                        </div>
                        <div class="pl-3">
                            <p>
                                <span class="font-weight-bold text-dark">
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
                    </div>

                    <hr>

                    <p>
                        {{ post.content }}
                    </p>
                </div>
                <img class="w-100" :src="post.picture" alt="">
                <div class="card-footer">
                    <post-comment-feed
                        :post="post"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: {
            post: Object,
        },

        computed: {
            relativeTime: function () {
                return moment(this.post.created_at).fromNow();
            }
        },
    }
</script>
