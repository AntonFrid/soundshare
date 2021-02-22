/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue/dist/vue';
import VueRouter from 'vue-router';
// import vueDebounce from 'vue-debounce';

//Components
import Main from "./components/Main.vue";
import AudioPlayer from './components/AudioPlayer.vue';
import Register from './components/Register.vue';
import Login from './components/Login.vue';
import Home from './components/Home.vue';
import Profile from './components/Profile.vue';
import Favorites from './components/Favorites.vue';
import PageNotFound from './components/PageNotFound.vue';




require("./bootstrap");
window.Vue = require("vue");
Vue.use(VueRouter);
// Vue.use(vueDebounce);

const routes = [
    { path: "/", component: Main,
        children: [
            { path: "player/:uuid", component: AudioPlayer },
            { path: "register", component: Register },
            { path: "login", component: Login },
            // { path: "home", component: Home },
            { path: "profile/:uuid", component: Profile },
            { path: "favorites", component: Favorites },
            // { path: "*", component: PageNotFound },
        ]
    }
];

const router = new VueRouter({
    mode: "history",
    linkActiveClass: "active",
    linkExactActiveClass: "exact-active",
    routes
});

const app = new Vue({
    router
}).$mount("#app");
