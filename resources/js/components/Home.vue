<template lang="html">
    <div class="Home">
        <div class="Home__container">
            <div @click="() => $router.push('/player/' + audio.uuid)" class="Home__card" v-for="audio in audioData">
                <p>{{ audio.user.name }}</p>
                <p>{{ audio.title }}</p>
                <button @click="onPlayClick" class="Home__card__button">
                    <svg v-if="!playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="110px" height="110px"><path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.14-5.18c.62-.39.62-1.29 0-1.69L9.54 5.98C8.87 5.55 8 6.03 8 6.82z"/></svg>
                    <svg v-if="playBool" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="85px" height="85px"><path d="M8 19c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2v10c0 1.1.9 2 2 2zm6-12v10c0 1.1.9 2 2 2s2-.9 2-2V7c0-1.1-.9-2-2-2s-2 .9-2 2z"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                audioData: [],
                playBool: false,
            }
        },
        methods: {
            onPlayClick() {
            }
        },
        mounted() {
            axios.get('/api/file/audio')
                .then(res => {
                    this.audioData = res.data;
                });
        }
    }
</script>

<style lang="css" scoped>
    .Home {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .Home__container {
        width: 95%;
        max-width: 1000px;
    }

    .Home__card {
        background: #2E2E2E;
        height: 120px;
        position: relative;
        margin-top: 20px;
        border-radius: 3px;
        width: 100%;
    }

    .Home__card__button {
        height: 100px;
        width: 100px;
        border-radius: 50%;
        border: none;
        outline: none;
        background-color: #FFB74C;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 10px;
        margin-top: -50px;
        top: 50%;
    }

    .Home__card__button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }
</style>
