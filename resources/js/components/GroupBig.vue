<template>
    <div class="container">
        <div class="col-md-8">
            <!-- view if to update or create -->
            <div v-if="create || editable">
                <input type="text" v-model="group_name" placeholder="name">
                <p v-if="!group_name">
                    Name is required!
                </p>

                <img :src="preview_image" class="w-100" id="banner_preview">
                <input @change="updatePreview" type="file">

                <br />

                <label for="private_ch">private group</label>
                <input id="private_ch" type="checkbox" v-model="group_private">

                <label for="remove_banner">remove banner</label>
                <input type="checkbox" id="remove_banner"
                    v-model="group_banner_remove"
                >

                <br />

                <p v-if="feedback" style="color:red">{{ feedback }}</p>

                <br />


                <button class="btn btn-primary" @click="applyChanges"
                    :disabled="!group_name">
                    <span v-if="create">Create group</span>
                    <span v-else>Apply changes</span>
                </button>
            </div>

            <!-- view just to display data. it's all temporary anyway -->
            <div v-else>
                <p>{{ group.name }}</p>
                <img :src="group.banner_picture" class="w-100">
                <p>{{ group_private? "private group" : "public group" }}</p>
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
        },

        data: function () {
            return {
                preview_image: this.group.banner_picture,
                group_private: this.group.private,
                group_name: this.group.name,
                group_banner_remove: false,
                files: [],
                feedback: '',
            }
        },

        methods: {
            updatePreview(event) {
                this.files = event.target.files;
                this.preview_image = window.URL.createObjectURL(this.files[0]);
            },

            applyChanges() {
                let headers = {headers:{'content-type':'multipart/form-data'}};

                let post_path = '/groups';
                if ( !this.create ) {
                    post_path += '/'+this.group.name;
                }

                let post_data = new FormData();

                // check if a different image has been selected
                if (
                    this.group.banner_picture != this.preview_image
                    && !this.group_banner_remove
                ) {
                    post_data.append('banner_picture', this.files[0])
                }

                post_data.append('name', this.group_name);
                post_data.append('private', this.group_private?'1':'0');
                post_data.append(
                    'remove_banner_picture', this.group_banner_remove?'1':'0'
                );

                axios.post(post_path, post_data, headers).then((res) => {
                    if ( this.create ) {
                        window.location.href = '/groups/'+res.data.string_id;
                    }
                }).catch((err) => {
                    this.feedback = this.handleAxiosError(err);
                });
            },
        },

        mounted() {
            if ( typeof this.group_name == 'undefined' ) {
                this.group_name = '';
            }
            if ( typeof this.group_private == 'undefined' ) {
                this.group_private = false;
            }
            console.log(this.group_private, this.group_name, this.editable, this.create);
        },
    }
</script>
