<template>
    <div class="d-flex align-items-baseline">
        <div class="pr-3" v-if="!disable_profile_picture">
            <img :src="user.profile.profile_picture"
                class="rounded-circle w-100"
                :style="{'max-width': profile_picture_size+'px'}"
            >
        </div>
        <div>
            <span>
                <span class="font-weight-bold text-dark">
                    {{ user.username }}
                </span>

                <span v-if="is_group_admin" class="fa fa-shield-alt" />

                <span class="pl-1" />

                <a :href="link" v-if="link">
                    {{ relativeTime }}
                </a>
                <span v-else>{{ relativeTime }}</span>
            </span>
        </div>

        <!-- slot for buttons or whatever -->
        <slot></slot>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props: {
            user: Object,
            date: String,
            link: String,
            is_group_admin: Boolean,
            profile_picture_size: {default: 40, type: Number},
            disable_profile_picture: Boolean,
        },

        computed: {
            relativeTime: function () {
                if ( this.date ) {
                    return moment(this.date).fromNow();
                }
                return '';
            }
        },
    }
</script>
