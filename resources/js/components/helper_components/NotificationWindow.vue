<template>
    <div class="notifications">
        <div class="button" @click="toggleOpened">
            <i class="far fa-envelope"></i>
            <span class="badge badge-danger unseen-count"
                v-if="unseen_count != 0"
            >{{ unseen_count }}</span>
        </div>
        <div class="box box-position" :class="{'invisible': !box_opened}">
            <div class="card">
                <div class="card-header">
                    Notifications
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item" v-if="feedback">
                        <div class="alert alert-danger">
                            {{ feedback }}
                        </div>
                    </li>
                    <li class="list-group-item"
                        v-for="notification in notifications"
                        :key="notification.id"
                        style="white-space: pre-line;"
                    >
                        <a :href="notification.link"
                        >{{ notification.message }}</a>
                    </li>
                </ul>
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
                box_opened: false,
                fetched: false,
                feedback: '',
            }
        },

        methods: {
            toggleOpened() {
                this.box_opened = !this.box_opened;

                if ( !this.fetched ) {
                    this.getNotifications();
                    this.fetched = true;
                }
            },

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
                    this.next_page_url = res.data.notifications.next_page_url;
                    this.notifications = res.data.notifications.data;
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

    .button {
        cursor: pointer;
    }

    .unseen-count {
        position: absolute;
        top: 0;
        left: 1.4rem;

        font-size: 0.8rem;
    }

    .box {
        font-size: 1rem;

        z-index: 2;

        width: 30vw;

        position: absolute;
        top: 2.3rem;
    }

    .box-position {
        right: 0;
    }

    @media (max-width: 768px) {
        .box-position {
            left: 0;

            width: 80vw;
        }
    }
</style>
