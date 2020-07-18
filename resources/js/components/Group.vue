<template>
    <div class="container">
        <div class="col-md-8">
            <div v-if="create || editable">
                <input type="text" v-model="group.name" placeholder="name">

                <img :src="preview_image" class="w-100" id="banner_preview">
                <input @change="updatePreview" type="file">

                <label for="private_ch">private group</label>
                <input id="private_ch" type="checkbox" v-model="private">

                <br />

                <button class="btn btn-primary" @click="applyChanges">
                    <span v-if="create">Create group</span>
                    <span v-else>Apply changes</span>
                </button>
            </div>
            <div v-else>
                <p>{{ group.name }}</p>
                <img :src="group.banner_picture" class="w-100">
                <p>{{ private? "private group" : "public group" }}</p>
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
                private: false,
            }
        },

        methods: {
            updatePreview(event) {
                let files = event.target.files;
                this.preview_image = window.URL.createObjectURL(files[0]);
            },

            applyChanges() {
                console.log('post to /groups');
            },
        },

        mounted() {
            console.log(this.group, this.editable, this.action);
        },
    }
</script>
