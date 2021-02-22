<template lang="html">
    <div v-if="audioData.user" class="AudioInfo">
        <p>{{ audioData.title }}</p>
        <div class="AudioInfo__container">
            <div v-if="audioData.user" class="AudioInfo__container__top">
                <div @click="() => { $router.push(`/profile/${audioData.user.uuid}`)}" class="AudioInfo__container__top__user">
                    <img class="AudioInfo__container__top__img" :src="audioData.user.img_url" >
                    <p>{{ audioData.user.name }}</p>
                </div>
                <div class="AudioInfo__container__top__data">
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
            <p>{{ audioData.description }}</p>
            <div class="AudioInfo__container__bottom">
                <label>Direct Link
                    <button
                        @mouseover="showCopyIcon = true"
                        @mouseleave="showCopyIcon = false"
                        title="Copy to clipboard."
                        @click="copyStringToClipboard(url)"
                        :class="copyBool ? 'AudioInfo__container__bottom__link copied' : 'AudioInfo__container__bottom__link'"
                    >{{ url }}</button>
                    <span v-if="showCopyIcon === true && copyBool === false" class="material-icons-round white md-22">
                        content_copy
                    </span>
                    <span v-if="copyBool" class="material-icons-round white md-24">
                        check
                    </span>
                </label>
                <a :href="audioData.file_url" :download="audioData.title">
                    <button class="AudioInfo__container__bottom__download">Download</button>
                </a>
            </div>
        </div>
        <Comment v-bind:authUser="authUser" v-if="audioData.id" v-bind:audio_id="audioData.id"/>
    </div>
</template>

<script>
    import Comment from './Comment.vue';

    export default {
        components: {
            Comment,
        },
        props: [
            'audioData',
            'authUser'
        ],
        data() {
            return {
                url: window.location.href,
                copyBool: false,
                showCopyIcon: false,
                copyTimeoutID: null,
            }
        },
        methods: {
            copyStringToClipboard(str) {
                var el      = document.createElement('textarea');
                el.value    = str;

                el.setAttribute('readonly', '');
                el.style = {position: 'absolute', left: '-9999px'};

                document.body.appendChild(el);

                el.select();

                document.execCommand('copy');
                document.body.removeChild(el);

                this.copyBool = true;

                this.copyTimeoutID = setTimeout(() => {
                    this.copyBool = false;
                }, 2000);
            }
        },
        watch: {
            audioData: function(newVal, oldVal) {
                this.copyBool = false;
            },
        }
    }
</script>

<style lang="css" scoped>
    .AudioInfo {
        width: 100%;
        margin-top: 55px;
        text-align: center;
        display: flex;
        align-items: center;
        flex-direction: column;
    }

    .AudioInfo > p {
        font-size: 30px;
        margin-bottom: 15px;
        color: white;
        opacity: 0.7;
        font-weight: 300;
    }

    .AudioInfo__container {
        width: 95%;
        max-width: 900px;
        text-align: left;
    }

    .AudioInfo__container > p {
        font-size: 18px;
        color: white;
        opacity: 0.6;
        font-weight: 300;
    }

    .AudioInfo__container__top {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .AudioInfo__container__top p {
        color: white;
        opacity: 0.7;
    }

    .AudioInfo__container__top__user {
        display: flex;
        align-items: center;
        font-size: 20px;
        cursor: pointer;
    }

    .AudioInfo__container__top__data {
        display: flex;
        align-items: center;
        font-size: 16px;
    }

    .AudioInfo__container__top__img {
        height: 65px;
        width: 65px;
        object-fit: cover;
        border-radius: 5px;
        background: #BEBEBE;
        overflow: hidden;
        margin-right: 20px;
    }

    .AudioInfo__container__bottom {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
    }

    .AudioInfo__container__bottom label {
        display: flex;
        align-items: center;
        font-size: 18px;
        line-height: 33px;
        font-weight: 300;
        color: rgba(255, 255, 255, 0.3);
    }

    .AudioInfo__container__bottom__link {
        background-color: transparent;
        width: 210px;
        height: 38px;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 5px;
        margin-left: 10px;

        font-style: normal;
        font-weight: 300;
        font-size: 15px;
        line-height: 25px;
        color: rgba(255, 255, 255, 0.3);
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
        outline: none;
    }

    .AudioInfo__container__bottom__link:hover {
        border: 2px solid rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.45);
        cursor: pointer;
    }

    .AudioInfo__container__bottom__download {
        width: 130px;
        height: 38px;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.4);
        font-weight: 300;
        font-size: 18px;
        outline: none;
    }

    .AudioInfo__container__bottom__download:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.1);
        cursor: pointer;
    }

    .copied {
        border: 2px solid rgba(255, 255, 255, 0.15);
        color: rgba(255, 255, 255, 0.45);
    }

    .material-icons-round.md-40 { font-size: 34px; margin-right: 6px; margin-left: 15px;}
    .material-icons-round.md-24 { font-size: 25px; margin-right: 12px;}
    .material-icons-round.md-22 { font-size: 22px; margin-right: 12px;}
    .material-icons-round.red { color: #FF4C4C; }
    .material-icons-round.orange { color: #FFB74C; }
    .material-icons-round.white { margin-left: 5px; color: rgba(255, 255, 255, 0.3); }
</style>
