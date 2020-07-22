<template>
    <div class="container">
        <div class="col-8 card">
            <div class="pt-4">
                <div class="d-flex align-items-baseline">
                    <div class="">
                        <img :src="user.profile.profile_picture"
                        class="rounded-circle w-100"
                        style="max-width: 40px"
                        >
                    </div>
                    <div class="pl-3">
                        <p>
                            <a :href="user.profile.profile_picture">
                                <span class="font-weight-bold text-dark">
                                    {{ user.username }}
                                </span>
                            </a>
                        </p>
                    </div>
                </div>

                <hr>

                <p>
                    {{ content }}
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
            content: String,
        },
    }
</script>
