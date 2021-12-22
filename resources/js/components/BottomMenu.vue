<template>
    <div class="BottomMenu">
        <div class="BottomMenu__icon__wrapper">
            <span @click="onUpload" class="material-icons-round md-34 white BottomMenu__icon">
                upload
            </span>
            <span @click="onRandom" class="material-icons-round md-34 white BottomMenu__icon">
                repeat
            </span>
            <Volume class="BottomMenu__icon"/>
            <Notification class="BottomMenu__icon"/>
        </div>
    </div>
</template>

<script>
    import Volume from "./Volume";
    import Notification from "./Notification";

    export default {
        name: "BottomMenu",

        props: [
            'onUpload',
            'onRandom'
        ],

        components: {
            Volume,
            Notification,
        },
    }
</script>

<style scoped>
    .BottomMenu {
        height: 50px;
        background: #272727;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-left: 14px;
        padding-right: 14px;
        position: fixed;
        bottom: 0;
        z-index: 100;
        width: 100%;
        box-shadow: 0 -2px 2px rgb(9 12 35 / 5%);
    }

    .BottomMenu__icon__wrapper {
        display: flex;
        align-items: center;
        justify-content: space-around;
        width: 100%;
    }

    .BottomMenu__icon {

    }

    .material-icons-round.md-34 { font-size: 34px; }
    .material-icons-round.white { color: white; opacity: 0.7;}
    .material-icons-round.white:hover { opacity: 0.8; cursor: pointer; }
</style>
