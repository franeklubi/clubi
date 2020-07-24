<template>
    <div class="container">
        <div class="row justify-content-center">
            {{ feedback }}
            <group-toggle-join
                :is_member="is_member_temp"
                @toggle="toggleJoin"
            />

            <group-header
                :editable="is_group_admin"
                :group="group"
            ></group-header>

            <group-feed
                :posts="posts"
                :is_member="is_member_temp"
                :is_group_admin="is_group_admin"
                :group_id_string="group.id_string"
            ></group-feed>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
            posts: Array,
            is_member: Boolean,
            is_group_admin: Boolean,
        },

        methods: {
            toggleJoin() {
                let post_path = '/groups/'+this.group.id_string+'/join';

                axios.post(post_path).then((res) => {
                    this.is_member_temp = !this.is_member_temp;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                    window.location.href = '/login';
                });
            },
        },

        data: function () {
            return {
                is_member_temp: this.is_member,
                feedback: '',
            }
        },

        mounted() {
        },
    }
</script>
