<template lang="html">
    <div class="AudioPlayerMini">
        <audio type="audio/mpeg" preload="none" class="AudioPlayerMini__audio" :ref="'player' + audioData.uuid" :src="audioData.file_url"></audio>
        <div class="AudioPlayerMini__top">
            <div @click="() => { if(isFavorites) { $router.push(`/profile/${audioData.user.uuid}`) } }" :class=" isFavorites ? 'AudioPlayerMini__top__left pointer' : 'AudioPlayerMini__top__left'">
                <img class="AudioPlayerMini__top__left__img" :src="audioData.user.img_url">
                <p class="AudioPlayerMini__top__left__user">{{ audioData.user.name }}</p>
            </div>
            <p
                :title="new Date(audioData.created_at).toDateString()"
            >{{ timeSince(audioData.created_at) }}</p>
        </div>
        <div class="AudioPlayerMini__mid">
            <p>{{ timeFormated }}</p>
            <div class="AudioPlayerMini__mid__progress">
                <div @click="updateTime" :ref="'silde' + audioData.uuid" class="hp_slide">
                    <div :ref="'range' + audioData.uuid" class="hp_range">
                    </div>
                </div>
            </div>
            <button @click="onPlay" class="AudioPlayerMini__mid__button">
                <svg v-if="!playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="55px" height="55px"><path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.14-5.18c.62-.39.62-1.29 0-1.69L9.54 5.98C8.87 5.55 8 6.03 8 6.82z"/></svg>
                <svg v-if="playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="42.5px" height="42.5px"><path d="M8 19c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2v10c0 1.1.9 2 2 2zm6-12v10c0 1.1.9 2 2 2s2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2z"/></svg>
            </button>
            <span v-if="user_id || isFavorites">
                <span style="margin-top: 8px;" v-if="!favoriteBool" @click="onFavorite" class="material-icons-round md-30 white">
                    favorite_border
                </span>
                <span style="margin-top: 8px;" v-else @click="onFavorite" class="material-icons-round md-30 white">
                    favorite
                </span>
            </span>
            <span v-else class="material-icons-round md-30 white" style="opacity: 0; cursor: default;">
                favorite_border
            </span>
        </div>
        <div class="AudioPlayerMini__bot">
            <div class="AudioPlayerMini__bot__title">
                <p @click="() => { $router.push(`/player/${ audioData.uuid }`) }">{{ audioData.title }}</p>
                <span class="AudioPlayerMini__bot__title__private" v-if="audioData.private == 1">
                    <span class="material-icons-outlined white md-22">key</span>
                    <span class="AudioPlayerMini__bot__title__private__label">Private</span>
                </span>
            </div>
            <div class="AudioPlayerMini__bot__data">
                <span class="material-icons-round red md-24">
                    favorite
                </span>
                <p>{{ audioData.likes.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <span class="material-icons-round orange md-40">
                    play_arrow
                </span>
                <p>{{ audioData.streams.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            </div>
        </div>

        <button v-if="!isFavorites && user_id === audioData.user_id" @click="onDelete(audioData.id)" class="AudioPlayerMini__delete">Delete</button>
    </div>
</template>

<script>
    export default {
        props: [
            'audioData',
            'volume',
            'isFavorites',
            'user_id',
        ],
        data() {
            return {
                playBool: false,
                timeFormated: '00:00',
                totalStreamTime: 0,
                intervalID: null,
                favoriteBool: this.audioData.favorite_boolean,
            }
        },
        methods: {
            updateProgress() {
                this.currentTime = this.$refs['player' + this.audioData.uuid].currentTime;
                this.duration = this.$refs['player' + this.audioData.uuid].duration;

                this.timeFormated = this.secondsToMinutesAndSeconds(this.currentTime);

                this.$refs['range' + this.audioData.uuid].setAttribute('style', `width:${(this.currentTime +.25)/this.duration*100+'%'}`);
            },
            onPlay() {
                if(this.playBool) {
                    this.playBool = false;

                    clearInterval(this.intervalID);
                    this.$refs['player' + this.audioData.uuid].pause();
                } else {
                    this.playBool    = true;
                    let audioPlayers = document.getElementsByClassName('AudioPlayerMini__audio');

                    for (let i = 0; i < audioPlayers.length; i++) {
                        audioPlayers[i].pause();
                    }

                    this.intervalID = setInterval(() => {
                        if(this.totalStreamTime >= 10 && this.totalStreamTime < 10.1) {
                            axios.put(`/api/audio/${this.audioData.id}/stream`);

                            this.totalStreamTime += 0.1;
                        } else if (this.totalStreamTime < 10) {
                            this.totalStreamTime += 0.1;
                        }
                    }, 100);

                    this.$refs['player' + this.audioData.uuid].volume = (this.volume / 10);
                    this.$refs['player' + this.audioData.uuid].play();
                }
            },
            pause() {
                this.playBool = false;
                clearInterval(this.intervalID);
            },
            updateTime(e) {
                let bar_width   = $('.hp_slide').width();
                let percent     = (e.offsetX / bar_width);
                let time        = (percent * this.$refs['player' + this.audioData.uuid].duration).toFixed(2);

                if (isNaN(this.$refs['player' + this.audioData.uuid].duration)) return;

                this.$refs['player' + this.audioData.uuid].currentTime = time.toString();
            },
            secondsToMinutesAndSeconds(time) {
                let minutes = Math.floor(time / 60);
                let seconds = Math.floor(time - (minutes * 60));

                return `${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
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
            onDelete(id) {
                axios.delete(`/api/file/audio/${id}`)
                    .then(res => {
                        this.$emit('update-audioData');
                        this.$emit('update-userData');
                    });
            },
            timeSince(date) {
                let seconds = Math.floor((new Date() - new Date(date)) / 1000);

                let interval = Math.floor(seconds / 31536000);
                if (interval > 1) return interval + " years ago";
                if (interval == 1) return interval + " year ago";

                interval = Math.floor(seconds / 2592000);
                if (interval > 1) return interval + " months ago";
                if (interval == 1) return interval + " month ago";

                interval = Math.floor(seconds / 86400);
                if (interval > 1) return interval + " days ago";
                if (interval == 1) return interval + " day ago";

                interval = Math.floor(seconds / 3600);
                if (interval > 1) return interval + " hours ago";
                if (interval == 1) return interval + " hour ago";

                interval = Math.floor(seconds / 60);
                if (interval > 1) return interval + " minutes ago";
                if (interval == 1) return interval + " minute ago";

                return "Just now";
            }
        },
        watch: {
            volume: function(newVal, oldVal) {
                this.$refs['player' + this.audioData.uuid].volume = (newVal / 10);
            },
        },
        mounted() {
            this.$refs['player' + this.audioData.uuid].addEventListener('timeupdate', this.updateProgress);
            this.$refs['player' + this.audioData.uuid].addEventListener('pause', this.pause);
        },
        beforeDestroy() {
            this.$refs['player' + this.audioData.uuid].removeEventListener('timeupdate', this.updateProgress);
            this.$refs['player' + this.audioData.uuid].removeEventListener('pause', this.pause);
            clearInterval(this.intervalID);
        }
     }
</script>

<style lang="css" scoped>
    .AudioPlayerMini {
        width: 100%;
        background-color: rgba(255, 255, 255, 0.05);
        padding: 0px 10px 0px 10px;
        border-radius: 5px;
        margin-bottom: 25px;
        position: relative;
    }

    .AudioPlayerMini__delete {
        background-color: transparent;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 3px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
        position: absolute;
        right: 10px;
        bottom: 18px;
        font-size: 12px;
    }

    .AudioPlayerMini__delete:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    .AudioPlayerMini__top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .AudioPlayerMini__top > p {
        font-weight: 300;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.4);
    }

    .AudioPlayerMini__top__left {
        display: flex;
        align-items: center;
    }

    .AudioPlayerMini__top__left__img {
        height: 38px;
        width: 38px;
        object-fit: cover;
        border-radius: 5px;
        background: transparent;
        overflow: hidden;
    }

    .AudioPlayerMini__top__left__user {
        color: #BEBEBE;
        font-style: normal;
        font-weight: 300;
        font-size: 16px;
        margin-left: 10px;
    }

    .AudioPlayerMini__mid {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .AudioPlayerMini__mid > p {
        color: white;
        opacity: 0.4;
        margin-top: 0;
        margin-bottom: 0;
    }

    .AudioPlayerMini__mid__progress {
        width: calc(100% - 150px);
        margin-left: 10px;
    }

    .AudioPlayerMini__mid__button {
        height: 55px;
        width: 55px;
        border-radius: 50%;
        border: none;
        outline: none;
        background-color: #FFB74C;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
    }

    .AudioPlayerMini__mid__button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .AudioPlayerMini__bot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .AudioPlayerMini__bot p {
        color: white;
        opacity: 0.7;
        margin-top: 0;
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .AudioPlayerMini__bot__title > p:hover {
        cursor: pointer;
    }

    .AudioPlayerMini__bot__title {
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 0;
    }

    .AudioPlayerMini__bot__title__private {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        margin-left: 10px;
        margin-right: 10px;
        opacity: 0.2;
    }

    .AudioPlayerMini__bot__title__private__label {
        font-size: 12px;
        margin-left: 3px;
        color: white;
        display: flex;
        align-items: center;
    }

    .AudioPlayerMini__bot__data {
        display: flex;
        align-items: center;
        font-size: 14px;
        margin-right: 105px;
        font-weight: 300;
    }

    .hp_slide{
        width:100%;
        background: rgba(255, 255, 255, 0.3);
        height: 5px;
        border-radius: 3px;
    }
    .hp_range{
        width: 0;
        max-width: 100%;
        background: #BEBEBE;
        height: 5px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .pointer {
        cursor: pointer;
    }

    .material-icons-round.md-40 { font-size: 30px; margin-right: 5px; margin-left: 15px;}
    .material-icons-round.md-24 { font-size: 20px; margin-right: 10px;}
    .material-icons-round.md-22 { font-size: 22px; margin-right: 10px;}
    .material-icons-round.md-30 { font-size: 30px; margin-left: 10px;}
    .material-icons-round.red { color: #FF4C4C; }
    .material-icons-round.orange { color: #FFB74C; }
    .material-icons-round.white { color: #B8B8B8; cursor: pointer; }
    .material-icons-round.white:hover { color: white; opacity: 0.8;}

    .material-icons-outlined.md-22 { font-size: 24px; }
    .material-icons-outlined.white { color: rgba(255, 255, 255, 0.8); }
</style>
