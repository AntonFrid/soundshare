<template>
    <div class="Main">
        <Header @update-authUser="updateAuthUser" v-bind:authUser="authUser" @volume="setVolume" v-bind:onUpload="onUpload"/>
        <UploadModal v-bind:showSelf="showUpload" v-bind:closeSelf="onUpload"/>
        <div class="Main__container">
            <router-view v-bind:authUser="authUser" @update-authUser="updateAuthUser" v-bind:volume="volume"></router-view>
        </div>
    </div>
</template>

<script>
    import Header from './Header.vue';
    import UploadModal from './UploadModal.vue';

    export default {
        components: {
            Header,
            UploadModal,
        },
        data() {
            return {
                showUpload: false,
                volume: 10,
                authUser: window.authUser,
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
            setVolume(value) {
                this.volume = parseInt(value);
            },
            updateAuthUser(data) {
                this.authUser = data;
            }
        },
        mounted() {
            if(window.authUser === null) {
                this.$router.push('/login');
            }
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
</style>
