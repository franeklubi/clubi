<template>
    <div class="d-flex align-items-baseline">
        <div class="background w-50">
            <img :src="group.banner_picture"
                class="w-100 rounded"
                :style="{'max-height': banner_picture_size+'px'}"
            >
            <div class="centered">
                <span class="text-light d-flex align-items-baseline">
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
            banner_picture_size: {default: 40, type: Number},
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
        position: relative;
        text-align: center;
        color: white;
        filter: grayscale(50%);
    }

    .centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
