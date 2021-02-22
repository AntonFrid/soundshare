<template>
    <div class="Header container-fluid disable-select">
        <Logo v-bind:authUser="authUser"/>
        <div v-if="authUser" class="Header__right">
            <button @click="onUpload" class="Header__uploadButton Header__right__icon">Upload</button>
            <span @click="onRandom" class="material-icons-round md-34 white Header__right__icon">
                repeat
            </span>
            <Volume class="Header__right__icon"/>
            <Notification class="Header__right__icon"/>
            <div ref="dropRef" @click="switchDropdown" class="Header__right__inner">
                <p class="Header__right__icon no-pointer">{{ authUser.name }}</p>
                <img class="Header__right__img no-pointer" :src="authUser.img_url" >
            </div>
            <Dropdown v-bind:authUser="authUser" @close-self="switchDropdown" v-bind:parentRef="$refs.dropRef" v-bind:logout="logout" v-if="showDropdown"/>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    import Logo from './Logo.vue';
    import Volume from './Volume.vue';
    import Notification from './Notification.vue';
    import Dropdown from './Dropdown.vue';

    export default {
        components: {
            Logo,
            Volume,
            Notification,
            Dropdown,
        },
        props: [
            'onUpload',
            'authUser',
        ],
        data() {
            return {
                userData: {},
                showDropdown: false,
            }
        },
        methods: {
            logout() {
                axios.post('/api/logout')
                .then(res => {
                    this.$emit('update-authUser', null);
                    this.showDropdown = false;
                    this.$router.push('/login');
                });
            },
            onRandom() {
                axios.get('/api/file/random/audio/' + this.$route.params.uuid)
                    .then(res => {
                        this.$router.push('/player/' + res.data);
                    });
            },
            switchDropdown() {
                if(this.showDropdown) {
                    this.showDropdown = false;
                } else {
                    this.showDropdown = true;
                }
            }
        },
    }
</script>

<style lang="css" scoped>

    .Header {
        height: 70px;
        background: #272727;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-left: 14px;
        padding-right: 14px;
        position: fixed;
        top: 0;
        z-index: 100;
        width: 100%;
        box-shadow: 0 2px 2px rgb(9 12 35 / 5%);
    }

    .Header__right__inner {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 70px;
        min-width: 100px;
    }

    .Header__right__inner:hover {
        cursor: pointer;
    }

    .Header__right__inner > p {
        color: #ffffff;
        opacity: 0.7;
        font-style: normal;
        font-weight: 300;
        font-size: 16px;
        line-height: 22px;
    }

    .Header__right {
        display: flex;
        flex-direction: row;
        align-items: center;
        position: relative;
    }

    .Header__right__icon {
        margin-right: 10px;
    }

    .Header__right__img {
        height: 38px;
        width: 38px;
        object-fit: cover;
        border-radius: 5px;
        background: #BEBEBE;
        overflow: hidden;
    }

    .Header__uploadButton {
        background-color: #FFB74C;
        opacity: 1;
        color: white;
        border: none;
        outline: none;
        height: 32px;
        width: 96px;
        border-radius: 18px;
    }

    .Header__uploadButton:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .no-pointer {
        pointer-events: none;
    }

    .disable-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .material-icons-round.md-34 { font-size: 34px; }
    .material-icons-round.white { color: white; opacity: 0.7;}
    .material-icons-round.white:hover { opacity: 0.8; cursor: pointer; }
</style>
