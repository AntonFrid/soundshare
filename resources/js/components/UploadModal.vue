<template lang="html">
    <div v-if="showSelf" @mousedown="onOverlayClick" class="UploadModal">
        <div id="waveform" class="UploadModal__container">
            <div class="UploadModal__container__header">
                <h2>Upload</h2>
            </div>
            <div class="UploadModal__container__body">
                <label for="">
                    <span>Title</span>
                    <input @input="() => { errorTitle = false; }" class="UploadModal__title" type="text" name="title" v-model="title"/>
                    <p v-if="errorTitle">*Please enter a title.</p>
                </label>
                <label for="">
                    <span>Description</span>
                    <textarea class="UploadModal__desc" name="desc" v-model="desc"/>
                </label>
                <label>
                    <input class="UploadModal__choose" type="file" name="file" accept=".mp3" @change="onFileSelect"/>
                    <p class="UploadModal__choose__error" v-if="errorFile">*Please choose a file</p>
                    <p class="UploadModal__choose__error" v-if="errorUpload">*Invalid file type, mp3 required (max size 20MB).</p>
                </label>
                <div class="UploadModal__buttons">
                    <button @click="onCancel" class="UploadModal__buttons__cancle">Cancel</button>
                    <button class="UploadModal__buttons__uploadButton" @click="onUpload">Upload</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'UploadModal',
        props: [
            'showSelf',
            'closeSelf'
        ],
        data() {
            return {
                selectedFile: null,
                title: "",
                desc: "",
                errorFile: false,
                errorUpload: false,
                errorTitle: false,
            }
        },
        methods: {
            onFileSelect(e) {
                this.selectedFile = e.target.files[0];
                this.errorFile    = false;
                this.errorUpload  = false;
            },
            onUpload() {
                if(!this.selectedFile) {
                    this.errorFile = true;
                    return;
                }
                else if(this.title.replace(' ', '') === '') {
                    this.errorTitle = true;
                    return;
                }

                let postParams = new FormData();
                postParams.append('file', this.selectedFile);
                postParams.append('title', this.title);
                postParams.append('description', this.desc);

                axios.post('/api/file/audio', postParams)
                    .then(res => {
                        this.title          = "";
                        this.desc           = "";
                        this.selectedFile   = null;

                        this.$router.push(`/player/${res.data.uuid}`)

                        this.closeSelf();
                    })
                    .catch(err => {
                        this.errorUpload = true;
                    })
            },
            onOverlayClick(e) {
                if(e.target !== e.currentTarget) return

                this.title          = "";
                this.desc           = "";
                this.selectedFile   = null;
                this.errorFile      = false;
                this.errorUpload    = false;
                this.errorTitle     = false;

                this.closeSelf();
            },
            onCancel() {
                this.title          = "";
                this.desc           = "";
                this.selectedFile   = null;
                this.errorFile      = false;
                this.errorUpload    = false;
                this.errorTitle     = false;

                this.closeSelf();
            }
        },
    }
</script>

<style lang="css" scoped>
    .UploadModal {
        height: 100vh;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 101;
        position: fixed;
        top: 0;
        animation-name: onPop;
        animation-duration: 0.15s;
        overflow: hidden;
    }

    @keyframes onPop {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    @keyframes onPopCont {
        from { margin-bottom: -200px; }
        to   { margin-bottom: 0px; }
    }

    .UploadModal__container {
        height: 60%;
        width: 80%;
        max-width: 600px;
        background: #2D2D2D;
        border-radius: 5px;
        overflow: hidden;
        position: fixed;
        animation-name: onPopCont;
        animation-duration: 0.15s;
    }

    .UploadModal__container__header {
        width: 100%;
        position: absolute;
        top: 0;
        text-align: center;
        background-color: #272727;
        color: #BEBEBE;
    }

    .UploadModal__container__body {
        position: absolute;
        bottom: 0;
        padding-left: 20px;
        padding-right: 20px;
        width: 100%;
        height: 80%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: space-around;
    }

    .UploadModal__container__body label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        position: relative;
    }

    .UploadModal__container__body label > span {
        color: #BEBEBE;
        margin: 0;
        background: #252525;
        padding: 2px;
        padding-left: 10px;
        padding-right: 10px;
        flex-grow: 1;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        opacity: 0.5;
        font-size: 14px;
    }

    .UploadModal__choose__error {
        color: rgba(255, 76, 76, 0.5);
        font-size: 14px;
        position: absolute;
        bottom: -38px;
        left: 0;
    }

    .UploadModal__container__body > label > p {
        color: rgba(255, 76, 76, 0.5);
        font-size: 14px;
        position: absolute;
        bottom: -38px;
        right: 0;
    }

    .UploadModal__buttons {
        display: flex;
        margin-left: calc(100% - 204px);
        flex-wrap: wrap;
    }

    .UploadModal__buttons__uploadButton {
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

    .UploadModal__buttons__uploadButton:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .UploadModal__buttons__cancle {
        background-color: transparent;
        width: 96px;
        height: 32px;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 18px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
    }

    .UploadModal__buttons__cancle:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    @media screen and (max-width: 578px) {
        .UploadModal__container {
            width: 90%;
            height: 70%;
            max-width: none;
        }

        .UploadModal__buttons {
            width: 100%;
            margin: 0;
        }

        .UploadModal__buttons button {
            width: 100%;
            margin: 0;
        }

        .UploadModal__buttons__uploadButton {
            margin-top: 12px !important;
        }
    }

    .UploadModal__title {
        width: 100%;
        height: 40px;
        padding: 5px;
        background: #252525;
        border-radius: 5px;
        border-top-left-radius: 0px;
        outline: none;
        font-size: 16px;
        color: rgba(255,255,255,0.5);
        border: solid 2px rgba(255, 255, 255, 0.0);
    }

    .UploadModal__title:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .UploadModal__title:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .UploadModal__desc {
        width: 100%;
        height: 80px;
        padding: 5px;
        background: #252525;
        border-radius: 5px;
        border-top-left-radius: 0px;
        outline: none;
        font-size: 16px;
        color: rgba(255,255,255,0.5);
        border: solid 2px rgba(255, 255, 255, 0.0);
        resize: none;
    }

    .UploadModal__desc:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .UploadModal__desc:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .UploadModal__choose {
        color: #BEBEBE;
    }
</style>
