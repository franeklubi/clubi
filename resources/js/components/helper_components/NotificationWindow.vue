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
                <div class="card-header d-flex justify-content-between">
                    Notifications
                    <button class="btn btn-link clear p-0 m-0 align-self-end"
                        @click="clearNotifications"
                    >
                            clear all
                    </button>
                </div>
                <div class="notification-list">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-if="feedback">
                            <div class="alert alert-danger m-0">
                                {{ feedback }}
                            </div>
                        </li>

                        <li class="list-group-item"
                            v-if="notifications.length == 0 && fetched"
                        >
                            <div class="alert alert-info m-0">
                                No notifications
                            </div>
                        </li>

                        <li class="list-group-item notification-list-item"
                            v-for="notification in notifications"
                            :key="notification.id"
                            style="white-space: pre-line;"
                            :class="{'unseen': notification.seen == '0'}"
                            @click="clickNotification(notification)"
                        >{{ notification.message }}</li>

                        <li v-if="next_page_url && fetched"
                            class="list-group-item"
                        >
                            <button
                                class="btn btn-link"
                                @click="loadNotifications"
                            >Load more</button>
                        </li>
                    </ul>
                </div>
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
                next_page_url: '/notifications',
                unseen_count: 0,
                url: '/notifications',
                box_opened: false,
                fetched: false,
                feedback: '',
                from_date: null,
            }
        },

        methods: {
            toggleOpened() {
                this.box_opened = !this.box_opened;

                if ( !this.fetched ) {
                    this.loadNotifications();
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

            loadNotifications() {
                axios.get(this.next_page_url, {
                    params: {'from_date': this.from_date}
                }).then((res) => {
                    this.next_page_url = res.data.notifications.next_page_url;
                    this.notifications.push(...res.data.notifications.data);

                    // set from_date
                    this.from_date = this.notifications[0]?
                        this.notifications[0].created_at:null;

                    if ( !this.fetched ) {
                        this.markReadNotifications();
                    }
                    this.fetched = true;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            clickNotification(notification) {
                let delete_url = this.url+'/'+notification.id;

                axios.delete(delete_url).then((res) => {
                    console.log(res);

                    let index = this.notifications.findIndex((n) => {
                        return n.id == res.data.notification.id
                    });

                    this.notifications.splice(index, 1);

                    if ( notification.link ) {
                        window.location.href = notification.link;
                    }
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            markReadNotifications() {
                let post_url = this.url + '/count';

                axios.post(post_url).then((res) => {
                    this.unseen_count = 0;
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            clearNotifications() {
                axios.delete(this.url).then((res) => {
                    this.notifications = [];
                }).catch((err) => {
                    console.log(err);
                    this.feedback = this.handleAxiosError(err);
                    console.log(this.feedback);
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

        z-index: 3;

        width: 30vw;

        position: absolute;
        top: 2.3rem;
    }

    .notification-list {
        max-height: 45vh;
        overflow-y: auto;
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

    .notification-list-item {
        cursor: pointer;
        color: #222222;
    }

    .notification-list-item:hover {
        filter: brightness(95%);
    }

    .unseen {
        background-color: #e2f0fb;
    }

    .clear {
        font-size: 0.8rem;
    }
</style>
