<template lang="html">
    <div class="PageNotFound">

    </div>
</template>

<script>
    export default {
        mounted() {
            if(window.authUser) {
                this.$router.push(`/profile/${window.authUser.uuid}`);
            } else {
                this.$router.push('/login');
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
