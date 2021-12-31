<template lang="html">
    <div v-if="showSelf" @mousedown="onOverlayClick" class="UploadModal">
        <div id="waveform" class="UploadModal__container">
            <!-- <div class="UploadModal__container__header">
                <h2>Upload</h2>
            </div> -->
            <div class="UploadModal__container__body">
                <label for="">
                    <span>Title</span>
                    <input
                        @input="() => { errorTitle = false; }"
                        class="UploadModal__title"
                        type="text"
                        name="title"
                        v-model="title"
                        :placeholder="title_placeholder"
                    />
                    <p v-if="errorTitle">*Please enter a title.</p>
                </label>
                <label for="">
                    <span>Description</span>
                    <textarea class="UploadModal__desc" name="desc" v-model="desc"/>
                </label>
                <div style="width: 100%; position: relative;" @dragover.prevent @drop.prevent>
                    <input style="display: none;" type="file" name="file" accept=".mp3" @change="onFileSelect"/>
                    <div
                        class="UploadModal__choose"
                        @click="triggerFileUpload"
                        @drop="onDragFile"
                        @mouseover="is_hover_upload = true"
                        @mouseleave="is_hover_upload = false"
                    >
                        <i v-show="!selectedFile" class="UploadModal__choose__icon">
                            <i class="UploadModal__choose__icon__arrow"></i>
                            <i class="UploadModal__choose__icon__body"></i>
                            <i class="UploadModal__choose__icon__bottom"></i>
                        </i>
                        <span v-show="!selectedFile" class="UploadModal__choose__hint">Click or drag the file here to upload</span>

                        <div v-show="selectedFile" class="UploadModal__choose__icon__done">
                            <span class="material-icons md-60" :class="is_hover_upload ? 'red' : 'green'">
                                {{ is_hover_upload ? 'delete_forever' : 'download_done' }}
                            </span>
                        </div>
                        <span v-if="selectedFile" class="UploadModal__choose__hint" style="padding-top: 0px;">{{ selectedFile.name.substring(0, 100) }}{{ selectedFile.name.length > 100 ? '...' : ''}}</span>
                    </div>
                    <span class="UploadModal__choose__error" v-if="errorFile">*Please choose a file</span>
                    <span class="UploadModal__choose__error" v-if="errorUpload">*Invalid file type, mp3 required (max size 20MB).</span>
                </div>

                <div class="UploadModal__switches">
                    <span class="UploadModal__switch__wrapper">
                        <label class="switch" for="checkbox_private">
                            <input v-model="private" type="checkbox" id="checkbox_private"/>
                            <div class="slider round"></div>
                        </label>
                        <div class="UploadModal__switch__label">Make Private</div>
                    </span>

                    <span class="UploadModal__switch__wrapper">
                        <label class="switch" for="checkbox_download">
                            <input v-model="download" type="checkbox" id="checkbox_download"/>
                            <div class="slider round"></div>
                        </label>
                        <div class="UploadModal__switch__label">Enable Download</div>
                    </span>
                </div>

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
                title_placeholder: "",
                desc: "",
                private: false,
                download: false,
                errorFile: false,
                errorUpload: false,
                errorTitle: false,
                is_hover_upload: false
            }
        },
        methods: {
            triggerFileUpload() {
                $('input[name="file"]').trigger('click');
            },
            onDragFile(e) {
                this.selectedFile       = null;
                this.selectedFile       = e.dataTransfer.files[0];
                this.errorFile          = false;
                this.errorUpload        = false;
                this.is_hover_upload    = false;

                $('.material-icons.md-60').animate({opacity: 0}, 1);

                setTimeout(function() {
                    $('.material-icons.md-60').animate({opacity: 0.8}, 100);
                }, 100);

                this.title_placeholder = this.selectedFile.name.split('.')[0] || this.selectedFile.name;
            },
            onFileSelect(e) {
                this.selectedFile       = null;
                this.selectedFile       = e.target.files[0];
                this.errorFile          = false;
                this.errorUpload        = false;
                this.is_hover_upload    = false;

                $('.material-icons.md-60').animate({opacity: 0}, 1);

                setTimeout(function() {
                    $('.material-icons.md-60').animate({opacity: 0.8}, 100);
                }, 100);

                this.title_placeholder = this.selectedFile.name.split('.')[0] || this.selectedFile.name;
            },
            onUpload() {
                if(!this.selectedFile) {
                    this.errorFile = true;
                    return;
                }

                let postParams = new FormData();

                postParams.append('file', this.selectedFile);
                postParams.append('description', this.desc);
                postParams.append('private', this.private);
                postParams.append('download', this.download);

                if(this.title.replace(' ', '') === '') {
                    postParams.append('title', this.title_placeholder);
                } else {
                    postParams.append('title', this.title);
                }

                axios.post('/api/file/audio', postParams)
                    .then(res => {
                        this.title              = "";
                        this.title_placeholder  = "";
                        this.desc               = "";
                        this.selectedFile       = null;

                        this.$router.push(`/player/${res.data.uuid}`)

                        this.closeSelf();
                    })
                    .catch(err => {
                        this.errorUpload = true;
                    })
            },
            onOverlayClick(e) {
                if(e.target !== e.currentTarget) return

                this.title              = "";
                this.title_placeholder  = "";
                this.desc               = "";
                this.selectedFile       = null;
                this.errorFile          = false;
                this.errorUpload        = false;
                this.errorTitle         = false;
                this.private            = false;
                this.download           = false;

                this.closeSelf();
            },
            onCancel() {
                this.title              = "";
                this.title_placeholder  = "";
                this.desc               = "";
                this.selectedFile       = null;
                this.errorFile          = false;
                this.errorUpload        = false;
                this.errorTitle         = false;
                this.private            = false;
                this.download           = false;

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
        width: 80%;
        max-width: 600px;
        background: #2D2D2D;
        border-radius: 5px;
        overflow: hidden;
        position: fixed;
        animation-name: onPopCont;
        animation-duration: 0.15s;
    }

    /* .UploadModal__container__header {
        width: 100%;
        position: absolute;
        top: 0;
        text-align: center;
        background-color: #272727;
        color: #BEBEBE;
    } */

    .UploadModal__container__body {
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 20px;
        width: 100%;
    }

    .UploadModal__container__body label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        position: relative;
        margin-bottom: 20px;
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
        bottom: 5px;
        text-align: center;
        width: 100%;
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
        margin-bottom: 20px;
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
        margin-bottom: 20px;
    }

    .UploadModal__buttons__cancle:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    @media screen and (max-width: 578px) {
        .UploadModal__container {
            width: 90%;
            max-width: none;
        }

        .UploadModal__buttons {
            width: 100%;
            margin-left: 0;
        }

        .UploadModal__buttons button {
            width: 100%;
            margin-left: 0;
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
        position: relative;
        box-sizing: border-box;
        padding: 35px;
        min-height: 170px;
        background-color: #252525;
        text-align: center;
        border: 1px dashed rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border-radius: 5px;
        width: 100%;
        margin-bottom: 20px;
    }

    .UploadModal__choose:hover {
        cursor: pointer;
        border-color: rgba(0, 0, 0, 0.1);
        background-color: rgba(0, 0, 0, 0.05);
    }

    .UploadModal__choose__hint {
        display: block;
        padding: 15px;
        font-size: 14px;
        color: #BEBEBE;
        line-height: 20px;
    }

    .UploadModal__choose__icon {
        display: block;
        margin: 0 auto 6px;
        width: 42px;
        height: 42px;
        overflow: hidden;
    }

    .UploadModal__choose__icon__arrow {
        display: block;
        margin: 0 auto;
        width: 0;
        height: 0;
        border-bottom: 14.7px solid #FFB74C;
        opacity: 0.8;
        border-left: 14.7px solid transparent;
        border-right: 14.7px solid transparent;
    }

    .UploadModal__choose__icon__body {
        display: block;
        width: 12.6px;
        height: 14.7px;
        margin: 0 auto;
        background-color: #FFB74C;
        opacity: 0.8;
    }

    .UploadModal__choose__icon__bottom {
        box-sizing: border-box;
        display: block;
        height: 12.6px;
        border: 6px solid #FFB74C;
        opacity: 0.8;
        border-top: none;
    }

    .UploadModal__choose__icon__done {
        margin-top: -5px;
    }

    .UploadModal__switch__wrapper {
        display: flex;
        align-items: center;
    }

    .UploadModal__switch__label {
        color: #BEBEBE;
        margin-left: 12.5px;
        white-space: nowrap;
        margin-bottom: 20px;
    }

    /* Checkbox/switches */
    .switch {
        display: inline-block;
        height: 32px;
        position: relative;
        width: 64px !important;
    }

    .switch input {
        display:none;
    }

    .slider {
        background-color: #ccc;
        bottom: 0;
        cursor: pointer;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: .4s;
    }

    .slider:before {
        background-color: #fff;
        bottom: 3px;
        content: "";
        height: 26px;
        left: 3px;
        position: absolute;
        transition: .4s;
        width: 26px;
    }

    input:checked + .slider {
        background-color: #FFB74C;
    }

    input:checked + .slider:before {
        transform: translateX(32px);
    }

    .slider.round {
        border-radius: 32px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .material-icons.md-60 { font-size: 60px; opacity: 0; }
    .material-icons.green { color: #4BB543; }
    .material-icons.red   { color: rgba(255, 76, 76, 0.5) ;}
</style>
