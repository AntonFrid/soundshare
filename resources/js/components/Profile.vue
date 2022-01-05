<template lang="html">
    <div class="Profile">
        <EditModal
            v-if="showEdit"
            :audioData="editData"
            :showSelf="showEdit"
            :close-self="onEdit"
            :isPlayer="false"
            @update-audioData="getUserAudio"
            @update-userData="getUserData"
        />

        <div class="Profile__header">
            <div class="Profile__header__inner">
                <div class="Profile__header__inner__img__container">
                    <img class="Profile__header__inner__img" :src="userData.img_url">
                    <template v-if="authUser">
                        <label @click="showUpload = !showUpload;" v-if="userData.id === authUser.id">
                            <span class="material-icons-round md-32">
                                camera_alt
                            </span>
                            Change photo
                        </label>
                    </template>

                    <my-upload
                        field="file"
                        @crop-success="cropSuccess"
                        @crop-upload-success="cropUploadSuccess"
                        @crop-upload-fail="cropUploadFail"
                        v-model="showUpload"
                        :width="200"
                        :height="200"
                        url="/api/user/image"
                        img-format="jpg"
                        langType="en"
                        :noCircle="true"
                        :headers="headers"
                    ></my-upload>
                </div>
                <div class="Profile__header__inner__info">
                    <h1>{{ userData.name }}</h1>
                    <p v-if="authUser && userData.id == authUser.id" class="Profile__header__inner__uploadAmount">
                        <b class="mr-2">{{ userData.public_upload_count }}</b>Public uploads
                    </p>
                    <p v-if="authUser && userData.id == authUser.id" class="Profile__header__inner__uploadAmount">
                        <b class="mr-2">{{ userData.private_upload_count }}</b>Private uploads
                    </p>
                    <p v-else class="Profile__header__inner__uploadAmount">
                        <b class="mr-2">{{ userData.public_upload_count }}</b>Uploads
                    </p>
                </div>
            </div>
        </div>
        <div class="Profile__body">
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 24px;" v-if="userData.upload_amount < 1">No uploads yet.</p>
            <AudioPlayerMini
                v-bind:isFavorites="false"
                v-bind:volume="volume"
                v-bind:audioData="audioData"
                v-bind:user_id="(authUser) ? authUser.id : null"
                v-for="audioData in AudioDataArr"
                :key="audioData.id"
                :onEdit="onEdit"
            />
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    import myUpload from 'vue-image-crop-upload/upload-2.vue';
    import AudioPlayerMini from './AudioPlayerMini.vue';
    import EditModal from "./EditModal";

    export default {
        components: {
            AudioPlayerMini,
            myUpload,
            EditModal
        },
        props: [
            'authUser',
            'volume'
        ],
        data() {
            return {
                userData: {},
                AudioDataArr: [],
                showUpload: false,
                imgDataUrl: '',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                showEdit: false,
                editData: null,
            }
        },
        methods: {
            getUserData() {
                axios.get(`/api/user/${this.$route.params.uuid}`)
                    .then(res => {
                        this.userData = res.data;
                    });
            },
            getUserAudio() {
                axios.get(`/api/user/${ this.$route.params.uuid }/file/audio`)
                    .then(res => {
                        this.AudioDataArr = res.data;
                    });
            },
            cropSuccess(imgDataUrl, field){
                this.imgDataUrl = imgDataUrl;
            },
            cropUploadSuccess(jsonData, field){
                this.userData = jsonData;

                this.getUserAudio();
                this.$emit('update-authUser', jsonData);

                $('.vicp-close').trigger('click');
            },
            cropUploadFail(status, field){
                console.log('-------- upload fail --------');
                console.log(status);
                console.log('field: ' + field);
            },
            onEdit(currentAudioData = null) {
                if(!this.showEdit) {
                    this.editData                   = currentAudioData;
                    this.showEdit                   = true;
                    document.body.style.overflowY   = "hidden";
                } else {
                    this.editData                   = null;
                    this.showEdit                   = false;
                    document.body.style.overflowY   = "scroll";
                }
            }
        },
        mounted() {
            this.getUserData();
            this.getUserAudio();

            let vm = this;

            $('.vue-image-crop-upload').on('click', function(event) {
                if(this !== event.target) return;
                vm.showUpload = false;
            });
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

    .Profile__header__inner__uploadAmount {

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
        background: transparent;
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

<style>
    .vue-image-crop-upload .vicp-wrap {
        background-color: #2D2D2D;
        border-radius: 5px;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step1 .vicp-drop-area {
        background-color: #252525;
        border-radius: 5px;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step1 .vicp-drop-area .vicp-icon1 .vicp-icon1-arrow {
        border-bottom: 14.7px solid #FFB74C;
        opacity: 0.8;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step1 .vicp-drop-area .vicp-icon1 .vicp-icon1-body {
        background-color: #FFB74C;
        opacity: 0.8;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step1 .vicp-drop-area .vicp-icon1 .vicp-icon1-bottom {
        border: 6px solid #FFB74C;
        opacity: 0.8;
        border-top: none;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-operate a {
        background-color: transparent;
        width: 96px;
        height: 32px;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 18px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-operate a:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-operate .vicp-operate-btn {
        background-color: #FFB74C;
        opacity: 1;
        color: white;
        border: none;
        outline: none;
        height: 32px;
        width: 96px;
        border-radius: 18px;
        margin-left: 12px;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-operate .vicp-operate-btn:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step3 {
        display: none;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-close {
        opacity: 0;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop .vicp-crop-right .vicp-preview .vicp-preview-item {
        border: none;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop .vicp-crop-right .vicp-preview .vicp-preview-item img {
        border-radius: 12.5px;
        padding: 0;
        border: none;
        background-color: transparent;
    }

    .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop .vicp-crop-left .vicp-img-container {
        border-radius: 12.5px;
    }

    @media screen and (max-width: 578px) {
        .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop {
            display: flex;
            justify-content: space-around;
            flex-direction: column;
            align-items: center;
        }

        .vue-image-crop-upload .vicp-wrap {
            width: 90%;
            height: 70%;
        }

        .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop .vicp-crop-right {
            margin-top: 20px;
        }

        .vue-image-crop-upload .vicp-wrap .vicp-operate {
            width: 100%;
            display: flex;
            flex-direction: column;
            position: static;
        }

        .vue-image-crop-upload .vicp-wrap .vicp-operate a {
            width: 100% !important;
            margin-bottom: 12px;
            margin-left: 0 !important;
        }

        .vue-image-crop-upload .vicp-wrap .vicp-step2 {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }

        .vue-image-crop-upload .vicp-wrap .vicp-step1 {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }
    }

    @media screen and (max-height: 750px) {
        .vue-image-crop-upload .vicp-wrap .vicp-step2 .vicp-crop .vicp-crop-right .vicp-preview {
            display: none;
        }
    }
</style>
