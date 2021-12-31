<template lang="html">
    <div class="Favorites">
        <div class="Favorites__header">
            <p>Favorites</p>
        </div>
        <div class="Favorites__body">
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 24px;" v-if="!favoriteData[0]">No favorites yet.</p>
            <AudioPlayerMini
                v-bind:isFavorites="true"
                v-bind:volume="volume"
                v-bind:audioData="favorite.audio"
                v-for="favorite in favoriteData"
                :key="favorite.audio.id"
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
            'volume',
        ],
        data() {
            return {
                favoriteData: [],
            }
        },
        mounted() {
            axios.get('/api/favorites')
                .then(res => {
                    this.favoriteData = res.data.reverse();
                })
        }
    }
</script>

<style lang="css" scoped>
    .Favorites {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .Favorites__header {
        height: 105px;
        width: 100%;
        background: #2E2E2E;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #BEBEBE;
    }

    .Favorites__header > p {
        font-size: 32px;
        color: #BEBEBE;
    }

    .Favorites__body {
        width: 95%;
        max-width: 900px;
        margin-top: 25px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        flex-direction: column;
    }
</style>
