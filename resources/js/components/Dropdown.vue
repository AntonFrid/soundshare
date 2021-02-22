<template lang="html">
    <div ref="dropdown" class="Dropdown">
        <button name="profile" @click="onClick">PROFILE</button>
        <button name="favorites" @click="onClick">FAVORITES</button>
        <button name="logout" @click="onClick">LOG OUT</button>
    </div>
</template>

<script>
    export default {
        props: [
            'logout',
            'parentRef',
            'authUser'
        ],
        methods: {
            onClick(e) {
                if(e.target.name === 'logout') {
                    this.logout();
                } else if (e.target.name === 'profile'){
                    if(window.location.pathname === `/profile/${this.authUser.uuid}`) return;

                    this.$router.push(`/profile/${this.authUser.uuid}`);
                    this.$emit('close-self');
                } else {
                    if(window.location.pathname === `/favorites`) return;

                    this.$router.push(`/favorites`);
                    this.$emit('close-self');
                }
            },
            onEvent(e) {
                if(e.target === this.$refs.dropdown || e.target === this.parentRef) return;
                this.$emit('close-self');
            }
        },
        mounted() {
            document.addEventListener('click', this.onEvent);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.onEvent);
        }
    }
</script>

<style lang="css" scoped>
    .Dropdown {
        position: absolute;
        right: -14px;
        bottom: -140px;
        height: 140px;
        width: 150px;
        display: flex;
        flex-direction: column;
        background-color: #272727;
        border-radius: 0px 3px;
        z-index: 99;
        overflow: hidden;
        box-shadow: -2px 2px 2px rgb(9 12 35 / 5%);
    }

    .Dropdown button {
        height: calc(140px / 3);
        font-size: 15px;
        cursor: pointer;
        background-color: #272727;
        color: #BEBEBE;
        outline: none;
    }

    .Dropdown button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.1);
    }
</style>
