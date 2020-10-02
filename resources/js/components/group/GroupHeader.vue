<template>
    <div class="container">
        <div class="viewport w-100"
            :style="{
                'background-image': `url(${preview_image})`,
                'height': viewport_height+'vh',
            }"
        >
            <div class="controls w-100 h-100 d-flex flex-column hover">
                <div class="c-top h-50 d-flex justify-content-between">
                    <!-- lock icon -->
                    <div class="">
                        <span :class="lockIcon" class="text-shadow" />
                        <input type="checkbox"
                            v-model="group_private"
                            class="lock-checkbox btn"
                            v-if="edit_mode"
                        >

                        <span v-if="edit_mode" class="tooltiptext"
                        >Change group privacy</span>
                    </div>

                    <!-- buttons -->
                    <div class="ml-auto mr-4" v-if="edit_mode">
                        <button type="button" class="btn btn-warning"
                            @click="removePicture"
                        >
                            <span class="fas fa-times" />
                        </button>

                        <input @change="updatePreview" type="file"
                            class="inputfile" :id="'file_input'+_uid"
                        >
                        <label :for="'file_input'+_uid"
                            class="btn btn-info mt-2"
                        >
                            <span class="far fa-image text-light" />
                        </label>

                        <button type="button" class="btn btn-success"
                            @click="applyChanges"
                        >
                            <span class="fas fa-check" />
                        </button>

                        <button type="button" class="btn btn-danger d-block
                            ml-auto"
                            @click="deleteGroup"
                            v-if="!create"
                        >
                            Remove Group
                        </button>
                    </div>

                    <!-- edit icon -->
                    <div class="show" v-if="create || editable">
                        <input type="checkbox"
                            class="edit-checkbox btn"
                            v-model="edit_mode"
                        >
                        <span :class="editIcon" class="text-shadow" />
                    </div>
                </div>

                <div class="alert alert-danger" v-if="feedback">
                    {{ feedback }}
                </div>
                <div class="alert alert-success" v-if="edit_mode && success">
                    Group updated!
                </div>

                <div class="c-bottom h-50 d-flex">
                    <!-- group name -->
                    <a :href="group_link" class="title"
                        v-if="!edit_mode"
                    >{{ group_name }}</a>
                    <input type="text" placeholder="Group name"
                        class="form-control title"
                        v-model="group_name"
                        v-if="edit_mode"
                    >

                    <!-- group stats -->
                    <div class="counts">
                        <span class="fas fa-user">
                            {{ group.user_count }}
                        </span>
                        <span class="ml-3 fas fa-align-left">
                            {{ group.post_count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            create: Boolean,
            editable: Boolean,
            group: Object,
            viewport_height: {default: 40, type: Number},
        },

        data: function () {
            return {
                group_link: '/groups/'+this.group.id_string,
                group_private: this.group.private,
                group_name: this.group.name,
                group_banner_picture: this.group.banner_picture,
                preview_image: this.group.banner_picture,
                files: [],
                feedback: '',
                edit_mode: this.create,
                group_banner_remove: false,
                success: false,
            }
        },

        methods: {
            removePicture() {
                this.group_banner_remove = true;

                this.preview_image =
                    process.env.MIX_DEFAULT_BANNER_PICTURE_PATH;
            },

            updatePreview(event) {
                this.group_banner_remove = false;

                this.files = event.target.files;
                this.preview_image = window.URL.createObjectURL(this.files[0]);
            },

            applyChanges() {
                let headers = {headers:{'content-type':'multipart/form-data'}};

                let url = '/groups';
                let post_data = new FormData();

                if ( !this.create ) {
                    url += '/'+this.group.id_string;
                }

                // check if a different image has been selected
                if (
                    this.group_banner_picture != this.preview_image
                    && !this.group_banner_remove
                ) {
                    post_data.append('banner_picture', this.files[0])
                }

                post_data.append('name', this.group_name);
                post_data.append('private', this.group_private?'1':'0');
                post_data.append(
                    'remove_banner_picture', this.group_banner_remove?'1':'0'
                );

                axios.post(url, post_data, headers).then((res) => {
                    if ( this.create ) {
                        window.location.href = '/groups/'+res.data.string_id;
                    }
                    this.success = true;
                    setTimeout(() => {
                        this.success = false;
                    }, 1500);

                    this.feedback = '';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },

            deleteGroup() {
                if ( !confirm('Are you sure?') ) {
                    return;
                }

                axios.delete(
                    '/groups/'+this.group.id_string
                ).then((res) => {
                    window.location.href = '/';
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },
        },

        computed: {
            lockIcon() {
                let classes = "fas fa-lock";

                if ( !this.group_private ) {
                    classes += "-open";
                }

                return classes;
            },

            editIcon() {
                return this.edit_mode?'fas fa-edit':'far fa-edit';
            },
        },

        mounted() {
            if ( typeof this.group_name == 'undefined' ) {
                this.group_name = '';
            }
            if ( typeof this.group_private == 'undefined' ) {
                this.group_private = false;
            }
            if ( typeof this.group_banner_picture == 'undefined' ) {
                this.group_banner_picture =
                    process.env.MIX_DEFAULT_BANNER_PICTURE_PATH;
                this.preview_image = this.group_banner_picture;
            }

            // cast laravel's bool to js bool
            this.group_private = Boolean(Number(this.group_private));
        },
    }
</script>

<style scoped>
    a {
        color: white;
    }

    .text-shadow {
        text-shadow: 2px 2px 5px black;
    }

    .lock-checkbox {
        position: absolute;
        opacity: 0;
        left: 0;

        width: 1.2rem;
        height: 1.2rem;
    }

    .edit-checkbox {
        position: absolute;
        opacity: 0;

        width: 1.2rem;
        height: 1.2rem;
    }

    .viewport {
        color: white;
        background-size: cover;
        background-position: center;

        border-top-right-radius: 0.4rem;
        border-top-left-radius: 0.4rem;
    }

    .controls {
        background: linear-gradient(
            0deg,
            rgba(0,0,0,0.48) 0%,
            rgba(255,255,255,0) 31%
        );

        padding: 1rem;
    }

    .c-top {
        position: relative;
    }

    .title {
        font-size: 1.5rem;
        align-self: flex-end;

        padding-left: 0.5rem;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        max-width: 65%;
    }

    .counts {
        margin-left: auto;
        align-self: flex-end;
    }

    .show {
        visibility: hidden;
    }

    .hover:hover > div > .show {
        visibility: visible;
    }

    .tooltiptext {
        width: 30%;
        background-color: black;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;

        position: absolute;
        z-index: 1;

        left: -0.2rem;
        top: 1.8rem;
    }

    .tooltiptext::after {
        content: " ";
        position: absolute;
        bottom: 100%;
        left: 5%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent black transparent;
    }

    .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
</style>
