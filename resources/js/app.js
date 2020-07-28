/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.mixin({
    methods: {
        handleAxiosError: function(err) {
            let validation_errors = err.response.data.errors;
            let feedback = '';
            if ( typeof validation_errors != 'undefined' ) {
                for ( const key in validation_errors ) {
                    let add = '';
                    if ( typeof validation_errors[key] != 'string' ) {
                        add = validation_errors[key][0];
                    } else {
                        add = validation_errors[key];
                    }
                    feedback += add;
                }
            } else {
                feedback += err.response.data.message;
            }
            return feedback;
        }
    }
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// group components
Vue.component(
    'group-add-post',
    require('./components/group/GroupAddPost.vue').default
);

Vue.component(
    'group-container',
    require('./components/group/GroupContainer.vue').default
);

Vue.component(
    'group-feed',
    require('./components/group/GroupFeed.vue').default
);

Vue.component(
    'group-header',
    require('./components/group/GroupHeader.vue').default
);

Vue.component(
    'group-invitation-window',
    require('./components/group/GroupInvitationWindow.vue').default
);

Vue.component(
    'group-toggle-join',
    require('./components/group/GroupToggleJoin.vue').default
);


// helper components
Vue.component(
    'user-header',
    require('./components/helper_components/UserHeader.vue').default
);


// post components
Vue.component(
    'post-add-comment',
    require('./components/post/PostAddComment.vue').default
);

Vue.component(
    'post-comment-feed',
    require('./components/post/PostCommentFeed.vue').default
);

Vue.component(
    'post-comment-item',
    require('./components/post/PostCommentItem.vue').default
);

Vue.component(
    'post-item',
    require('./components/post/PostItem.vue').default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
