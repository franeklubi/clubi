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
                        >
                            Invite
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <user-header
                    v-for="invitation in invitations" :key="invitation.id"
                    :user="invitation.user"
                    :profile_picture_size="30"
                    class="border-bottom mb-2 pb-1 px-1"
                >
                    <p class="pl-2">{{ invitation.named_state }}</p>
                    <button class="btn btn-light ml-auto">
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
        },

        data: function () {
            return {
                invitations: [],
                feedback: '',
                username_input: '',
            }
        },

        methods: {
        },

        created() {
            let get_url = '/groups/'+this.group.id_string+'/invitations';

            axios.get(get_url).then((res) => {
                this.invitations = res.data.invitations;
            }).catch((err) => {
                this.feedback = this.handleAxiosError(err);
            });
        },

        computed: {
            canInvite() {
                return this.username_input.length > 1;
            },

            inviteClass() {
                return this.canInvite?'btn-success':'btn-secondary';
            },
        },
    }
</script>
