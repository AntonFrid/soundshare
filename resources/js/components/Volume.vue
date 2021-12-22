<template>
    <div @mouseleave="sliderBool = false" class="Volume">
        <span @mouseover="sliderBool = true" v-if="muteBool" @click="toggleMute" class="material-icons-round md-40 white">
            volume_off
        </span>
        <span @mouseover="sliderBool = true" v-else-if="parseInt(volume) > 5" @click="toggleMute" class="material-icons-round md-40 white">
            volume_up
        </span>
        <span @mouseover="sliderBool = true" v-else-if="parseInt(volume) === 0" @click="toggleMute" class="material-icons-round md-40 white">
            volume_mute
        </span>
        <span @mouseover="sliderBool = true" v-else @click="toggleMute" class="material-icons-round md-40 white">
            volume_down
        </span>
        <div v-if="sliderBool" @mouseover="sliderBool = true" @mouseleave="sliderBool = false" class="Volume__slider__container">
            <input type="range" id="slider" name="volume" min="0" max="10" step="1" v-model="volume" @input="updateVolume">
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                sliderBool: false,
                volume: '10',
                prevVol: '10',
                muteBool: false
            }
        },
        methods: {
            updateVolume(e) {
                this.muteBool = false;

                this.$parent.$emit('volume', e.target.value);
            },
            toggleMute() {
                if(!this.muteBool) {
                    this.muteBool   = true;
                    this.prevVol    = this.volume;
                    this.volume     = '0';

                    this.$parent.$emit('volume', this.volume);
                } else {
                    this.muteBool   = false;
                    this.volume     = this.prevVol;

                    this.$parent.$emit('volume',this.volume);
                }
            }
        }
    }
</script>

<style lang="css" scoped>
    .Volume {
        display: flex;
        flex-direction: row;
        align-items: center;
        position: relative;
        justify-content: center;
        height: 70px;
    }

    .Volume input {
        cursor: pointer;
        opacity: 0.7;
    }

    .Volume__slider__container {
        position: absolute;
        bottom: -91px;
        background-color: #272727;
        transform: rotate(-90deg);
        display: flex;
        justify-content: center;
        border-radius: 3px;
        z-index: 99;
        overflow: hidden;
        box-shadow: -2px -2px 2px rgb(9 12 35 / 5%);
        padding: 15px;
        padding-right: 20px;
    }

    @media screen and (max-width: 578px){
        .Volume__slider__container {
            top: -91px;
            height: 50px;
            box-shadow: 2px 2px 2px rgb(9 12 35 / 5%);
        }
    }

    .material-icons-round.md-40 { font-size: 40px; }
    .material-icons-round.white { color: white; opacity: 0.7;}
    .material-icons-round.white:hover { opacity: 0.8; cursor: pointer; }
</style>
