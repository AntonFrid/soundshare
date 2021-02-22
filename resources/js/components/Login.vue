<template lang="html">
    <div class="Login">
        <div class="Login__left">
            <!-- <div class="Login__left__info">
                <h2>Welcome <br/> to</h2>
                <h1>Soundshare</h1>
            </div> -->
        </div>
        <div class="Login__right">
            <div class="Login__right__tabs">
                <button class="Login__right__tabs__active">Login</button>
                <button class="Login__right__tabs__inactive" @click="() => this.$router.push('/register')">Register</button>
            </div>
            <form class="Login__container" @submit="onLogin">
                <label>
                    <span>Email</span>
                    <input @input="onInput" type="email" name="email" placeholder="Enter your email" v-model="email">
                    <p v-if="error">*Please insert valid credentials.</p>
                </label>
                <label>
                    <span>Password</span>
                    <input @input="onInput" type="password" name="password" placeholder="Enter your password" v-model="password">
                    <p v-if="error">*Please insert valid credentials.</p>
                </label>
                <button type="submit">Login</button>
            </form>

        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: [
            'authUser',
        ],
        data() {
            return {
                email: '',
                password: '',
                error: false,
            }
        },
        methods: {
            onLogin(e) {
                e.preventDefault();

                let postParams = new FormData();
                postParams.append('email', this.email);
                postParams.append('password', this.password);

                axios.get('/sanctum/csrf-cookie')
                    .then(res => {
                        axios.post('/login', postParams)
                            .then(res => {
                                axios.get('/api/user')
                                    .then(res => {
                                        this.$router.push(`/profile/${ res.data.uuid }`);
                                        this.$emit('update-authUser', res.data);

                                        this.email                  = '';
                                        this.password               = '';
                                    });
                            })
                            .catch(err => {
                                this.error = true;
                            });
                    });
            },
            onInput() {
                this.error = false;
            }
        },
        mounted() {
            if(this.authUser) {
                this.$router.push(`/profile/${ this.authUser.uuid }`);
            }
        }
    }
</script>

<style lang="css" scoped>
    .Login {
        height: calc(100vh - 70px);
        width: 100%;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .Login__left {
        height: 100%;
        width: 60%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .Login__left__info {
        width: 80%;
    }

    /* h2 {
        font-size: 40px;
        color: #BEBEBE;
        font-family: 'Nunito Sans', sans-serif;
        margin: 0;
        line-height: 40px;
        margin-bottom: -10px;
    }

    h1 {
        font-size: 60px;
        color: #BEBEBE;
        font-family: 'Nunito Sans', sans-serif;
        margin: 0;
    } */

    .Login__right {
        height: 100%;
        width: 40%;
        background: linear-gradient(180deg, #2E2E2E 0%, #272727 100%);
    }

    .Login__right__tabs {
        width: 100%;
        display: flex;
        flex-direction: row;
    }

    .Login__right__tabs__inactive {
        flex-grow: 1;
        height: 45px;
        background-color: transparent;
        font-size: 18px;
        color: rgba(190, 190, 190, 0.4);
        box-shadow: inset 0 0 100px 100px rgba(0, 0, 0, 0.23);
        cursor: pointer;
        outline: none;
    }

    .Login__right__tabs__inactive:hover {
        box-shadow: inset 0 0 100px 100px rgba(0, 0, 0, 0.10);
    }

    .Login__right__tabs__active {
        flex-grow: 1;
        height: 45px;
        background-color: transparent;
        font-size: 18px;
        color: #BEBEBE;
        cursor: default;
        outline: none;
    }

    .Login__container {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 90px;
    }

    .Login__container > label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 70%;
        margin-bottom: 20px;
        position: relative;
    }

    .Login__container > label > span {
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

    .Login__container > label > p {
        color: rgba(255, 76, 76, 0.5);
        font-size: 14px;
        position: absolute;
        bottom: -38px;
        right: 0;
    }

    .Login__container input {
        width: 100%;
        height: 45px;
        padding: 5px;
        background: #252525;
        border-radius: 5px;
        border-top-left-radius: 0px;
        outline: none;
        font-size: 16px;
        color: rgba(255,255,255,0.5);
        border: solid 2px rgba(255, 255, 255, 0.0);
    }

    .Login__container input:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .Login__container input:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .Login__container button{
        background-color: #FFB74C;
        opacity: 1;
        color: white;
        border: none;
        outline: none;
        height: 45px;
        width: 70%;
        border-radius: 3px;
        font-size: 18px;
        margin-top: 14px;
    }

    .Login__container button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }
</style>
