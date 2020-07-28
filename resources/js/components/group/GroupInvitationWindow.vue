<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="">Username</span>
                    </div>
                    <input type="text" class="form-control"
                        v-model="username_input"
                    >
                    <div class="input-group-append">
                        <button :class="['btn', inviteClass]"
                            :disabled="!canInvite"
                            @click="sendInvitation"
                        >
                            Invite
                        </button>
                    </div>
                </div>

                <div class="card-text alert alert-danger mt-2" role="alert"
                    v-if="feedback"
                >
                    {{ feedback }}
                </div>
            </div>

            <div class="card-body">
                <user-header
                    v-for="(invitation, index) in invitations"
                    :key="invitation.id"
                    :user="invitation.user"
                    :profile_picture_size="30"
                    class="border-bottom pb-1 px-1"
                    :class="{'mb-3': index != invitations.length-1}"
                >
                    <p class="pl-2">{{ invitation.named_state }}</p>
                    <button class="btn btn-light ml-auto"
                        v-if="user_id == invitation.from_id || is_group_admin"
                        @click="cancelInvitation(invitation)"
                    >
                        <span>cancel</span>
                    </button>
                </user-header>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
            user_id: Number,
            is_group_admin: Boolean,
        },

        data: function () {
            return {
                invitations: [],
                feedback: '',
                username_input: '',
            }
        },

        methods: {
            getInvitations() {
                let get_url = '/groups/'+this.group.id_string+'/invitations';

                axios.get(get_url).then((res) => {
                    this.invitations = res.data.invitations;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            cancelInvitation(invitation) {
                if ( !confirm('Are you sure?') ) {
                    return;
                }

                let delete_url = '/groups/'+this.group.id_string+'/invitations/'
                    +invitation.id;

                axios.delete(delete_url).then((res) => {
                    let index = this.invitations.findIndex((invitation) => {
                        return invitation.id == res.data.invitation.id;
                    });

                    this.invitations.splice(index, 1);

                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            sendInvitation() {
                let post_url = '/groups/'+this.group.id_string+'/invitations/';

                let post_data = new FormData();
                post_data.append('username', this.username_input);

                axios.post(post_url, post_data).then((res) => {
                    this.invitations.push(res.data.invitation);

                    this.feedback = '';
                    this.username_input = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },
        },

        created() {
            this.getInvitations();
        },

        computed: {
            canInvite() {
                return this.username_input.length >=
                    process.env.MIX_MIN_USERNAME_LENGTH;
            },

            inviteClass() {
                return this.canInvite?'btn-success':'btn-secondary';
            },
        },
    }
</script>
