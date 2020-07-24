<template>
    <div class="container">
        <div class="">
            <post-comment-item
                v-for="comment in reversedComments" :key="comment.id"
                :comment="comment"
            />
            <post-add-comment @add-comment="addComment"/>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            comments: Array,
        },

        data: function () {
            return {
                comments_per_page: process.env.MIX_COMMENTS_PER_PAGE,
                comments_to_render: this.comments,
                current_page: 1,
            }
        },

        methods: {
            addComment(new_comment) {
                const { text, picture_file } = new_comment;

                console.log(text, picture_file);
            },
        },

        computed: {
            reversedComments() {
                return this.comments_to_render.slice().reverse();
            }
        },

        mounted() {
            console.log(this.comments);
        }
    }
</script>
