<template>
    <div class="container-fluid">
        <div class="row justify-content-start">
            <div class="col-lg-4 col-xl-3 mb-3">
                <group-toggle-join
                    :is_member="is_member_temp"
                    @toggle="toggleJoin"
                />

                <group-invitation-window
                    v-if="is_member_temp"
                    :group="group"
                    :user_id="user_id"
                    :is_group_admin="is_group_admin"
                />
            </div>

            <div class="col-lg-8 col-xl-6">
                <div v-if="feedback" class="alert alert-danger">
                    {{ feedback }}
                </div>
                <group-header
                    :editable="is_group_admin"
                    :group="group"
                ></group-header>

                <group-feed
                    :posts="posts"
                    :user_id="user_id"
                    :is_member="is_member_temp"
                    :is_group_admin="is_group_admin"
                    :group_id_string="group.id_string"
                    :passed_next_page_url="next_page_url"
                ></group-feed>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
            posts: Array,
            user_id: Number,
            next_page_url: String,
            is_member: Boolean,
            is_group_admin: Boolean,
        },

        methods: {
            toggleJoin() {
                let post_path = '/groups/'+this.group.id_string+'/membership';

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
    }
</script>
