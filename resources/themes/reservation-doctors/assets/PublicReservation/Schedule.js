import Vue from 'vue';
import axios from 'axios';
import moment from "moment";
import Schedule from "./Components/Schedule";

//vuex
import Vuex from 'vuex'
window.moment = moment;
window.axios = axios;

var VueScrollTo = require('vue-scrollto');
Vue.use(VueScrollTo);


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

if (window.base_url) {
    window.axios.defaults.baseURL = window.base_url;
}
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        isLoading: false
    },
    mutations: {
        setIsLoading(state, isLoading) {
            state.isLoading = isLoading;
        },
    },
    getters: {
        getIsLoading: state => state.isLoading
    }
});

window.axios.interceptors.request.use(function (config) {
    PublicReservationSchedule.$store.commit('setIsLoading', true);
    return config;
}, function (error) {
    PublicReservationSchedule.$store.commit('setIsLoading', false);
    Ladda.stopAll();
    return Promise.reject(error);
});

window.axios.interceptors.response.use(function (response) {
    PublicReservationSchedule.$store.commit('setIsLoading', false);
    Ladda.stopAll();
    return response;
}, function (error) {
    PublicReservationSchedule.$store.commit('setIsLoading', false);
    Ladda.stopAll();
    return Promise.reject(error);
});


let PublicReservationSchedule = new Vue({
    el: '#schedule',
    components: {
        Schedule
    }, store
});
