<template>
    <div class="notifications">
        <div class="button">
            <i class="far fa-envelope"></i>
            <span class="badge badge-danger unseen-count"
                v-if="unseen_count != 0"
            >{{ unseen_count }}</span>
        </div>
        <div class="box">
            <div class="">

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
                notifications: [],
                next_page_url: '',
                unseen_count: 0,
                url: '/notifications',
            }
        },

        methods: {
            getUnseenCount() {
                let get_url = this.url + '/count';

                axios.get(get_url).then((res) => {
                    this.unseen_count = res.data.count;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            getNotifications() {
                axios.get(this.url).then((res) => {
                    this.notificatinos = res.data.notificatinos;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },
        },

        created() {
            this.getUnseenCount();
        },
    }
</script>

<style scoped>
    .notifications {
        position: relative;

        width: 3rem;
        height: 100%;

        font-size: 1.3rem;
        color: grey;

        display: flex;

        align-items: center;
        justify-content: center;
    }

    .unseen-count {
        position: absolute;
        top: 0;
        left: 1.4rem;

        font-size: 0.8rem;
    }
</style>
