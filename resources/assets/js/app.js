
require('./bootstrap');

window.Vue = require('vue');

import InstantSearch from 'vue-instantsearch';

window.events = new Vue();

let authorizations = require('./authorizations');

Vue.prototype.authorize = function (...params) {
    // for a test purpose, if you want to bypass authorization
    // return true; 
    
    if (!window.App.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1]);
    }
    
    return params[0](window.App.user);
}

Vue.prototype.signedIn = window.App.signedIn;

window.flash = function (message, level = 'success') {
    window.events.$emit( 'flash', { message, level });
};

Vue.component('flash', require('./components/Flash.vue'));
Vue.component('paginator', require('./components/Paginator.vue'));
Vue.component('user-notifications', require('./components/UserNotifications.vue'));
Vue.component('avatar-form', require('./components/AvatarForm.vue'));
Vue.component('wysiwyg', require('./components/Wysiwyg.vue'));

Vue.component('thread-view', require('./pages/Thread.vue'));

Vue.use(InstantSearch);

const app = new Vue({
    el: '#app'
});
