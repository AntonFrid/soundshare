<template lang="html">
    <div class="Profile">
        <div class="Profile__header">
            <div class="Profile__header__inner">
                <div class="Profile__header__inner__img__container">
                    <img class="Profile__header__inner__img" :src="userData.img_url">
                    <label for="file-input" v-if="userData.id === authUser.id">
                        <span class="material-icons-round md-32">
                            camera_alt
                        </span>
                        Change photo
                    </label>
                    <input type="file" id="file-input" accept=".png, .jpg, .jpeg" @change="onFileSelect">
                </div>
                <div class="Profile__header__inner__info">
                    <h1>{{ userData.name }}</h1>
                    <p class="Profile__header__inner__uploadAmount"><b>{{ userData.upload_amount }}</b> Uploads</p>
                </div>
            </div>
        </div>
        <div class="Profile__body">
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 24px;" v-if="userData.upload_amount < 1">No uploads yet.</p>
            <AudioPlayerMini
                @update-audioData="getUserAudio"
                @update-userData="getUserData"
                v-bind:isFavorites="false"
                v-bind:volume="volume"
                v-bind:audioData="audioData"
                v-bind:user_id="authUser.id"
                v-for="audioData in AudioDataArr"
                :key="audioData.id"
            />
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    import AudioPlayerMini from './AudioPlayerMini.vue';

    export default {
        components: {
            AudioPlayerMini,
        },
        props: [
            'authUser',
            'volume'
        ],
        data() {
            return {
                userData: {},
                AudioDataArr: [],
            }
        },
        methods: {
            getUserData() {
                axios.get(`/api/user/${this.$route.params.uuid}`)
                    .then(res => {
                        this.userData = res.data;
                    });
            },
            onFileSelect(e) {
                let postParams = new FormData();
                postParams.append('file', e.target.files[0]);

                axios.post('/api/user/image', postParams)
                    .then(res => {
                        this.userData = res.data;

                        this.getUserAudio();
                        this.$emit('update-authUser', res.data);
                    });
            },
            getUserAudio() {
                axios.get(`/api/user/${ this.$route.params.uuid }/file/audio`)
                    .then(res => {
                        this.AudioDataArr = res.data;
                    });
            }
        },
        mounted() {
            this.getUserData();
            this.getUserAudio();
        },
        watch: {
            $route(to, from) {
                if(to !== from){
                    this.getUserData();
                    this.getUserAudio();
                    window.scrollTo(0, 0);
                }
            }
        }
    }
</script>

<style lang="css" scoped>
    .Profile {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .Profile__header {
        height: 210px;
        width: 100%;
        background: #2E2E2E;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #BEBEBE;
    }

    .Profile__header__inner {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 95%;
        max-width: 900px;
    }

    .Profile__header__inner__info > h1 {
        font-size: 28px;
        color: #BEBEBE;
        font-weight: 400;
        margin-left: 20px;
    }

    .Profile__header__inner__info > p {
        font-size: 14px;
        color: #BEBEBE;
        font-weight: 400;
        margin-left: 20px;
    }

    .Profile__header__inner__img__container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .Profile__header__inner__img__container > label {
        z-index: 1;
        position: absolute;
        height: 162.5px;
        width: 162.5px;
        margin: 0;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 12.5px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 18px;
        cursor: pointer;
        opacity: 0;
        transition: 0.2s;
    }

    .Profile__header__inner__img__container > label:hover {
        opacity: 1;
        transition: 0.2s;
    }


    .Profile__header__inner__img {
        height: 162.5px;
        width: 162.5px;
        object-fit: cover;
        border-radius: 12.5px;
        background: #BEBEBE;
        overflow: hidden;
    }

    .Profile__header__inner__btn {
        background-color: transparent;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 3px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
        margin-top: 12px;
        font-size: 16px;
    }

    .Profile__header__inner__btn:hover {
        border: 2px solid rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.45);
        cursor: pointer;
    }

    .Profile__body {
        width: 95%;
        max-width: 900px;
        margin-top: 25px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        flex-direction: column;
    }

    input[type="file"] {
        display: none;
    }

    .material-icons-round.md-32 { font-size: 32px;}
</style>
