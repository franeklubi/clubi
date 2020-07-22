<template>
    <div class="container">
        <group-add-post
            @add-post="addPost"
        />
        <div v-for="post in posts" :key="post.id">
            <group-post
                :user="post.user"
                :content="post.content"
            />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            posts: Array,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
            }
        },

        methods: {
            addPost(new_post) {
                const { text } = new_post;
            }
        },

        mounted() {
        },
    }
</script>
