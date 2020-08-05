<template>
    <div class="d-flex align-items-center w-100">
        <div class="background mr-1 rounded w-100"
            :style="{
                'background-image': `url(${group.banner_picture})`,
                'height': `${banner_picture_size}px`
            }"
        >
            <div class="d-flex justify-content-center align-items-center
                h-100 px-2"
            >
                <a class="font-weight-bold title mr-2"
                    :href="group_link"
                >
                    {{ group.name }}
                </a>

                <span v-if="group.private == '1'" class="fas fa-lock" />
            </div>
        </div>

        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
            banner_picture_size: {default: 37, type: Number},
        },

        data: function () {
            return {
                group_link: '/groups/'+this.group.id_string,
            }
        },
    }
</script>


<style scoped>
    a {
        color: white;
    }

    .background {
        color: white;
        background-size: cover;
        background-position: center;

        min-width: 0;
    }

    .title {
        text-shadow: 2px 2px 5px black;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
