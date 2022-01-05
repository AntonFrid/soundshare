<template lang="html">
    <div v-if="showSelf" @mousedown="onOverlayClick" class="EditModal">
        <div id="waveform" class="EditModal__container">
            <div class="EditModal__container__body">
                <label>
                    <span>Title</span>
                    <input
                        @input="() => { errorTitle = false; }"
                        class="EditModal__title"
                        type="text"
                        name="title"
                        v-model="title"
                    />
                    <p v-if="errorTitle">*Please enter a title.</p>
                </label>
                <label>
                    <span>Description</span>
                    <textarea class="EditModal__desc" name="desc" v-model="desc"/>
                </label>

                <div class="EditModal__switches">
                    <span class="EditModal__switch__wrapper">
                        <label class="switch" for="checkbox_private">
                            <input v-model="private" type="checkbox" id="checkbox_private"/>
                            <div class="slider round"></div>
                        </label>
                        <div class="EditModal__switch__label">Make Private</div>
                    </span>

                    <span class="EditModal__switch__wrapper">
                        <label class="switch" for="checkbox_download">
                            <input v-model="download" type="checkbox" id="checkbox_download"/>
                            <div class="slider round"></div>
                        </label>
                        <div class="EditModal__switch__label">Enable Download</div>
                    </span>
                </div>

                <div class="EditModal__buttons">
                    <button @click="onDelete" class="EditModal__buttons__delete">Delete</button>

                    <div class="EditModal__buttons__right">
                        <button @click="onCancel" class="EditModal__buttons__cancle">Cancel</button>
                        <button class="EditModal__buttons__saveButton" @click="onSave">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'EditModal',
        props: [
            'showSelf',
            'closeSelf',
            'audioData',
            'isPlayer',
            'authUser'
        ],
        data() {
            return {
                title: this.audioData.title,
                desc: this.audioData.description,
                private: this.audioData.private,
                download: this.audioData.download,
                errorTitle: false,
            }
        },
        methods: {
            onSave() {
                let postParams = new FormData();

                postParams.append('description', this.desc);
                postParams.append('private', this.private);
                postParams.append('download', this.download);

                if(this.title.replace(' ', '') === '') {
                    this.errorTitle = true;
                } else {
                    postParams.append('title', this.title);
                }

                axios.post('/api/edit/audio/' + this.audioData.id, postParams)
                    .then(res => {
                        this.title              = "";
                        this.title_placeholder  = "";
                        this.desc               = "";

                        if(this.isPlayer) {
                            this.$emit('update-audioData');
                        } else {
                            this.$emit('update-audioData');
                            this.$emit('update-userData');
                        }

                        this.closeSelf();
                    })
                    .catch(err => {
                        // Mby error handling here later
                    })
            },
            onDelete() {
                axios.delete(`/api/file/audio/${ this.audioData.id }`)
                    .then(res => {

                        if(this.isPlayer) {
                            this.$router.push(`/profile/${authUser.uuid}`);
                        } else {
                            this.$emit('update-audioData');
                            this.$emit('update-userData');
                        }

                        this.closeSelf();
                    });
            },
            onOverlayClick(e) {
                if(e.target !== e.currentTarget) return

                this.title              = "";
                this.desc               = "";
                this.errorTitle         = false;
                this.private            = false;
                this.download           = false;

                this.closeSelf();
            },
            onCancel() {
                this.title              = "";
                this.desc               = "";
                this.errorTitle         = false;
                this.private            = false;
                this.download           = false;

                this.closeSelf();
            }
        },
    }
</script>

<style lang="css" scoped>
    .EditModal {
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

    .EditModal__container {
        width: 80%;
        max-width: 600px;
        background: #2D2D2D;
        border-radius: 5px;
        overflow: hidden;
        position: fixed;
        animation-name: onPopCont;
        animation-duration: 0.15s;
    }

    .EditModal__container__body {
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 20px;
        width: 100%;
    }

    .EditModal__container__body label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        position: relative;
        margin-bottom: 20px;
    }

    .EditModal__container__body label > span {
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

    .EditModal__container__body > label > p {
        color: rgba(255, 76, 76, 0.5);
        font-size: 14px;
        position: absolute;
        bottom: -38px;
        right: 0;
    }

    .EditModal__buttons {
        display: flex;
        justify-content: space-between;
    }

    .EditModal__buttons__delete {
        background-color: rgba(255, 76, 76, 1);
        opacity: 1;
        color: white;
        border: none;
        outline: none;
        height: 32px;
        width: 96px;
        border-radius: 18px;
        margin-bottom: 20px;
    }

    .EditModal__buttons__delete:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .EditModal__buttons__saveButton {
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

    .EditModal__buttons__saveButton:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .EditModal__buttons__cancle {
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

    .EditModal__buttons__cancle:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    @media screen and (max-width: 578px) {
        .EditModal__container {
            width: 90%;
            max-width: none;
        }

        .EditModal__buttons {
            width: 100%;
            margin-left: 0;
            flex-direction: column;
        }

        .EditModal__buttons button {
            width: 100%;
            margin-left: 0;
        }
    }

    .EditModal__title {
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

    .EditModal__title:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .EditModal__title:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .EditModal__desc {
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

    .EditModal__desc:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .EditModal__desc:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .EditModal__switch__wrapper {
        display: flex;
        align-items: center;
    }

    .EditModal__switch__label {
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
</style>
