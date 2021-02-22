<template>
    <div class="AudioPlayer">
        <div class="AudioPlayer__wrapper">
            <div class="AudioPlayer__waveform__container">
                <p v-if="loadingBool">Loading...</p>
                <div @click="updateTime"
                    id="waveform"
                    class="AudioPlayer__waveform"
                ></div>
            </div>
            <p v-if="audioData.title" class="AudioPlayer__time">{{ audioTime }}</p>
            <div v-if="audioData.title">
                <span v-if="!favoriteBool" @click="onFavorite" class="material-icons-round md-36 white">
                    favorite_border
                </span>
                <span v-else @click="onFavorite" class="material-icons-round md-36 white">
                    favorite
                </span>
            </div>
            <button @click="onPlayClick" class="AudioPlayer__button">
                <svg v-if="!playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="110px" height="110px"><path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.14-5.18c.62-.39.62-1.29 0-1.69L9.54 5.98C8.87 5.55 8 6.03 8 6.82z"/></svg>
                <svg v-if="playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="85px" height="85px"><path d="M8 19c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2v10c0 1.1.9 2 2 2zm6-12v10c0 1.1.9 2 2 2s2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2z"/></svg>
            </button>
        </div>
        <AudioInfo v-bind:authUser="authUser" v-bind:audioData="audioData"/>
    </div>
</template>

<script>
    import axios from 'axios';
    import WaveSurfer from "wavesurfer.js";

    import AudioInfo from './AudioInfo.vue';

    export default {
        components: {
            AudioInfo,
        },
        data() {
          return {
            playBool: false,
            audioTime: "00:00",
            intervalID: null,
            audioURL: "",
            wavesurfer: null,
            audioData: {},
            favoriteBool: null,
            totalStreamTime: 0,
            loadingBool: true,
          }
        },
        props: [
            'volume',
            'authUser'
        ],
        methods: {
            onPlayClick() {
                if(!this.playBool) {
                    this.wavesurfer.playPause();
                    this.wavesurfer.on('finish', this.onEnd);

                    if(this.wavesurfer.isPlaying()) {
                        this.playBool = true;
                    }

                    this.intervalID = setInterval(() => {
                        this.audioTime = this.secondsToMinutesAndSeconds(Math.floor(this.wavesurfer.getCurrentTime()));

                        if(this.totalStreamTime >= 10 && this.totalStreamTime < 10.1) {
                            axios.put(`/api/audio/${this.audioData.id}/stream`);

                            this.totalStreamTime += 0.1;
                        } else if (this.totalStreamTime < 10) {
                            this.totalStreamTime += 0.1;
                        }
                    }, 100);
                } else {
                    this.playBool = false;

                    this.wavesurfer.playPause();
                    this.wavesurfer.un('finish', this.onEnd);

                    clearInterval(this.intervalID);
                }
            },
            onEnd() {
                this.playBool = false;
            },
            secondsToMinutesAndSeconds(time) {
                let minutes = Math.floor(time / 60);
                let seconds = Math.floor(time - (minutes * 60));

                return `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            },
            createWaveSurfer() {
                this.wavesurfer = WaveSurfer.create({
                    container: '#waveform',
                    waveColor: '#222222',
                    progressColor: '#BEBEBE',
                    barWidth: 4,
                    barRadius: 4,
                    normalize: true,
                    height: 170,
                    minPxPerSec: 4,
                    fillParent: true,
                    responsive: true,
                    hideScrollbar: true,
                    cursorWidth: 0,
                    barMinHeight: 1,
                });

                this.wavesurfer.on('ready', this.switchloadingBool);

                this.wavesurfer.load(this.audioURL);
                this.wavesurfer.setVolume(this.volume / 10);
            },
            getAudioData() {
                axios.get("/api/file/audio/" + this.$route.params.uuid)
                    .then(res => {
                        this.audioData      = res.data.file_data;
                        this.audioURL       = res.data.file_data.file_url;
                        this.favoriteBool   = res.data.favorite_boolean;

                        if (!this.wavesurfer) {
                            this.createWaveSurfer();
                        } else {
                            this.playBool    = false;
                            this.loadingBool = true;

                            this.wavesurfer.destroy();
                            this.createWaveSurfer();
                        }
                    });
            },
            onFavorite() {
                if(this.favoriteBool) {
                    axios.delete(`/api/audio/${this.audioData.id}/favorite`)
                        .then(res => {
                            this.favoriteBool = false;
                            this.audioData.likes -= 1;
                        });
                } else {
                    axios.put(`/api/audio/${this.audioData.id}/favorite`)
                        .then(res => {
                            this.favoriteBool = true;
                            this.audioData.likes += 1;
                        });
                }
            },
            updateTime() {
                setTimeout(() => {
                    this.audioTime = this.secondsToMinutesAndSeconds(Math.floor(this.wavesurfer.getCurrentTime()));
                }, 10)    ;
            },
            switchloadingBool() {
                this.loadingBool = false;
            }
        },
        watch: {
            volume: function(newVal, oldVal) {
                this.wavesurfer.setVolume(newVal / 10);
            },
            $route(to, from) {
                if(to !== from){
                    this.getAudioData();
                    window.scrollTo(0, 0);
                }
            }
        },
        mounted() {
            this.getAudioData();
            window.scrollTo(0, 0);
        },
        beforeDestroy() {
            clearInterval(this.intervalID);

            this.wavesurfer.destroy();
        }
    }
</script>

<style lang="css" scoped>
    .AudioPlayer__wrapper {
        height: 210px;
        background: #2E2E2E;
        position: relative;
        padding-top: 20px;
        padding-right: 10px;
        padding-left: 10px;
        width: 100%;
    }

    .AudioPlayer__button {
        height: 110px;
        width: 110px;
        border-radius: 50%;
        border: none;
        outline: none;
        background-color: #FFB74C;
        position: absolute;
        margin-left: -55px;
        left: 50%;
        bottom: -70px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 4;
    }

    .AudioPlayer__button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .AudioPlayer__time {
        color: white;
        opacity: 0.7;
        position: absolute;
        left: 50%;
        margin-left: -140px;
        bottom: -70px;
        font-size: 24px;
        font-weight: 300;
    }

    .material-icons-round.md-36 {
        font-size: 36px;
        color: white;
        opacity: 0.7;
        position: absolute;
        left: 50%;
        margin-left: 75px;
        bottom: -45px;
    }

    .AudioPlayer__waveform__container {
        min-height: 170px;
        cursor: pointer;
    }

    .AudioPlayer__waveform__container > p {
        height: 170px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        font-size: 32px;
        color: #BEBEBE;
        opacity: 0.5;
    }

    .material-icons-round.white { color: white; opacity: 0.7;}
    .material-icons-round.white:hover { opacity: 0.8; cursor: pointer; }
</style>
