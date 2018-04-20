import "./bootstrap";

import Vue from 'vue';

import InstantSearch from 'vue-instantsearch';
import VModal from 'vue-js-modal';

window.events = new Vue();

import authorizations from "./authorizations";

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

Vue.component('flash', require('./components/Flash'));
Vue.component('paginator', require('./components/Paginator'));
Vue.component('user-notifications', require('./components/UserNotifications'));
Vue.component('avatar-form', require('./components/AvatarForm'));
Vue.component('wysiwyg', require('./components/Wysiwyg'));
Vue.component('dropdown', require('./components/Dropdown'));
Vue.component('channel-dropdown', require('./components/ChannelDropdown'));
Vue.component('logout-button', require('./components/LogoutButton'));
Vue.component('login', require('./components/Login'));
Vue.component('register', require('./components/Register'));

Vue.component('thread-view', require('./pages/Thread'));

Vue.component('highlight', require('./components/Highlight'));

Vue.use(InstantSearch);
Vue.use(VModal);

const app = new Vue({
    el: '#app',

    data: {
        searching: false
    },

    methods: {
        search() {
            this.searching = true;

            this.$nextTick(() => {
                this.$refs.search.focus();
            });
        }
    }
});
