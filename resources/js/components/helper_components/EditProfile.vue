<template>
    <div class="container d-flex flex-column align-items-center">
        <div class="alert alert-danger w-100" v-if="feedback">
            {{ feedback }}
        </div>
        <div class="alert alert-success w-100" v-if="success">
            Update successful!
        </div>

        <!-- picture viewport -->
        <div class="viewport"
            :style="{'background-image': `url(${preview_picture})`}"
        >
        </div>

        <!-- buttons -->
        <div class="d-flex buttons-sizing">
            <!-- change picture -->
            <label for="file_input"
                class="btn btn-info picture-button-left w-75"
            >
                <span class="far fa-image text-light" />
            </label>

            <div class="btn btn-danger picture-button-right w-25 h-100"
                @click="removePicture"
            >
                <span class="fas fa-times"></span>
            </div>
        </div>

        <input @change="updatePreview" type="file"
            class="inputfile" id="file_input"
        >


        <!-- change username -->
        <div class="input-group mt-2 w-100">
            <div class="input-group-prepend">
                <span class="input-group-text">Username</span>
            </div>
            <input id="username"
                class="form-control"
                v-model="temp_user.username"
            >
        </div>


        <!-- change description -->
        <div class="input-group mt-2 w-100">
            <div class="input-group-prepend">
                <span class="input-group-text">Description</span>
            </div>
            <textarea id="description" class="form-control"
                v-model="temp_user.profile.description"
                rows="5"
            />
        </div>

        <button class="btn btn-primary w-75 mt-5" @click="applyChanges">
            Submit
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
        },

        data: function () {
            return {
                temp_user: this.user,
                remove_profile_picture: false,
                files: [],
                preview_picture: this.user.profile.profile_picture,
                feedback: '',
                success: false,
            }
        },

        methods: {
            removePicture() {
                this.remove_profile_picture = true;

                this.preview_picture =
                    process.env.MIX_DEFAULT_PROFILE_PICTURE_PATH;
            },

            updatePreview(event) {
                this.remove_profile_picture = false;

                this.files = event.target.files;
                this.preview_picture = window.URL.createObjectURL(this.files[0]);
            },

            applyChanges() {
                let post_url = '/settings';

                let data = new FormData;

                data.append('_method', 'PATCH');

                data.append('username', this.temp_user.username);

                if ( this.temp_user.profile.description != null ) {
                    data.append('description', this.temp_user.profile.description);
                }

                // check if a different image has been selected
                if (
                    this.temp_user.profile.profile_picture != this.preview_picture
                    && !this.remove_profile_picture
                ) {
                    data.append('profile_picture', this.files[0])
                }


                if ( this.remove_profile_picture ) {
                    data.append('remove_profile_picture', 'on');
                }

                axios.post(post_url, data).then((res) => {
                    this.success = true;
                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                    this.success = false;
                });
            },
        },
    }
</script>

<style scoped>
    .viewport {
        width: 50vw;
        height: 50vw;
        max-width: 50vh;
        max-height: 50vh;

        color: white;
        background-size: cover;
        background-position: center;

        border-top-right-radius: 0.4rem;
        border-top-left-radius: 0.4rem;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .buttons-sizing {
        width: 50vw;
        max-width: 50vh;
    }

    .picture-button-left {
        border-top-right-radius: 0;
        border-top-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .picture-button-right {
        border-top-right-radius: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
</style>
