<template>
    <div class="Main">
        <Header
            @update-authUser="updateAuthUser"
            v-bind:authUser="authUser"
            @volume="setVolume"
            v-bind:onUpload="onUpload"
            :onRandom="onRandom"
            :window_width="window_width"
        />
        <UploadModal v-bind:showSelf="showUpload" v-bind:closeSelf="onUpload"/>
        <div class="Main__container">
            <router-view v-bind:authUser="authUser" @update-authUser="updateAuthUser" v-bind:volume="volume"></router-view>
        </div>
        <BottomMenu
            v-if="window_width < 578"
            :onUpload="onUpload"
            :onRandom="onRandom"
            @volume="setVolume"
        />
    </div>
</template>

<script>
    import Header from './Header.vue';
    import UploadModal from './UploadModal.vue';
    import BottomMenu from './BottomMenu.vue';
    import axios from "axios";

    export default {
        components: {
            Header,
            UploadModal,
            BottomMenu
        },
        data() {
            return {
                showUpload: false,
                volume: 10,
                authUser: window.authUser,
                window_width: window.innerWidth
            }
        },
        methods: {
            onUpload() {
                if(!this.showUpload) {
                    this.showUpload               = true;
                    document.body.style.overflowY = "hidden";
                } else {
                    this.showUpload               = false;
                    document.body.style.overflowY = "scroll";
                }
            },
            onRandom() {
                axios.get('/api/file/random/audio/' + this.$route.params.uuid)
                    .then(res => {
                        this.$router.push('/player/' + res.data);
                    });
            },
            setVolume(value) {
                this.volume = parseInt(value);
            },
            updateAuthUser(data) {
                this.authUser = data;
            },
            onResize() {
                this.window_width = window.innerWidth;
            }
        },
        mounted() {
            if(window.authUser === null) {
                this.$router.push('/login');
            }

            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            });
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.onResize);
        },
        watch: {
            $route(to, from) {
                if(to !== from){
                    this.showUpload = false;
                }
            }
        }
    }
</script>

<style scoped>
    .Main {
        min-height: calc(100vh - 70px);
        width: 100%;
        background: #141414;
    }

    .Main__container {
        margin-top: 70px;
    }

    @media screen and (max-width: 578px) {
        .Main__container {
            margin-bottom: 50px;
        }
    }
</style>
