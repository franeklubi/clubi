<template>
    <div class="container">
        <group-add-post
            @add-post="addPost"
        />
        <div v-for="post in posts" :key="post.id">
            <group-post
                :post="post"
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
                console.log(text);
            }
        },

        mounted() {
        },
    }
</script>
