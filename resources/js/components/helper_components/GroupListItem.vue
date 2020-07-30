<template>
    <div class="d-flex align-items-center">
        <div class="background mr-1 rounded w-100"
            :style="{
                'background-image': `url(${group.banner_picture})`,
                'height': `${banner_picture_size}px`
            }"
        >
            <div class="h-100">
                <span class="d-flex justify-content-center align-items-center
                    h-100 w-100"
                    style="text-shadow: 2px 2px 5px black;"
                >
                    <a class="font-weight-bold mr-1"
                        :href="group_link"
                    >{{ shortenedGroupName }}</a>

                    <span v-if="group.private == '1'" class="fas fa-lock" />
                </span>
            </div>
        </div>

        <!-- slot for buttons or whatever -->
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

        computed: {
            shortenedGroupName() {
                let name = this.group.name;
                return name.length>10?name.substr(0, 10)+'...':name;
            }
        }
    }
</script>


<style scoped>
    a {
        color: white;
    }

    .background {
        text-align: center;
        color: white;
        background-size: cover;
        background-position: center;
    }
</style>
