<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Invitations
                <div class="card-text alert alert-danger mt-2"
                    role="alert" v-if="feedback"
                >
                    {{ feedback }}
                </div>
            </div>

            <div class="card-body" v-if="invitations.length != 0">
                <group-list-item
                    v-for="(invitation, index) in invitations"
                    :key="invitation.id"
                    :group="invitation.group"
                    class="border-bottom pb-1 px-1"
                    :class="{'mb-3': index != invitations.length-1}"
                >
                    <!-- join button -->
                    <button
                        @click="joinGroup(invitation)"
                        class="ml-auto btn btn-success"
                    >
                        <span class="fas fa-check" />
                    </button>

                    <!-- cancel button -->
                    <button class="btn btn-light justify-content-center ml-1"
                        @click="denyInvitation(invitation)"
                    >
                        <span class="fas fa-times" />
                    </button>
                </group-list-item>
            </div>
            <div v-else class="card-text m-2 alert alert-info">
                No available invitations
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
        },

        data: function () {
            return {
                invitations: [],
                feedback: '',
            }
        },

        methods: {
            getInvitations() {
                let get_url = '/dashboard/invitations';

                axios.get(get_url).then((res) => {
                    this.invitations = res.data.invitations;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            denyInvitation(invitation) {
                if ( !confirm('Are you sure?') ) {
                    return;
                }

                let delete_url = '/groups/'+invitation.group.id_string
                    +'/invitations/'+invitation.id;

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

            joinGroup(invitation) {
                let post_path = '/groups/'+invitation.group.id_string+'/join';

                axios.post(post_path).then((res) => {
                    window.location.href = '/groups/'
                        +invitation.group.id_string;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },
        },

        created() {
            this.getInvitations();
        },
    }
</script>
