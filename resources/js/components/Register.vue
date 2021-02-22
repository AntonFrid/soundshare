<template lang="html">
    <div class="Register">
        <div class="Register__left">
        </div>
        <div class="Register__right">
            <div class="Register__right__tabs">
                <button class="Register__right__tabs__inactive" @click="() => this.$router.push('/login')">Login</button>
                <button class="Register__right__tabs__active">Register</button>
            </div>
            <form class="Register__container" @submit="onRegister">
                <label>
                    <span>Username</span>
                    <input type="text" name="name" placeholder="Enter a username" @input="onInput" v-model="name">
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" placeholder="Enter a valid email address" @input="onInput" v-model="email">
                    <p v-if="errorEmail">*Email has already been taken.</p>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" placeholder="Enter a password" @input="onInput" v-model="password">
                    <p v-if="errorPassword">*Password doesn't match.</p>
                </label>
                <label>
                    <span>Confirm password</span>
                    <input type="password" name="password_confirmation" placeholder="Confirm password" @input="onInput" v-model="password_confirmation">
                    <p v-if="errorPassword">*Password doesn't match.</p>
                </label>
                <button :class="!isFilled ? 'Register__container__button__disabled' : 'Register__container__button'" type="submit">Register</button>
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
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                errorPassword: false,
                errorEmail: false,
                isFilled: false,
            }
        },
        methods: {
            onRegister(e) {
                e.preventDefault();

                if(this.password !== this.password_confirmation) {
                    this.errorPassword = true;

                    return
                }

                let postParams = new FormData();
                postParams.append('name', this.name);
                postParams.append('email', this.email);
                postParams.append('password', this.password);
                postParams.append('password_confirmation', this.password_confirmation);

                axios.get('/sanctum/csrf-cookie')
                    .then(res => {
                        axios.post('/register', postParams)
                            .then(res => {
                                this.login();
                            })
                            .catch(err => {
                                this.errorEmail = true;
                            });
                    });
            },
            login() {
                let postParamsLogin = new FormData();
                postParamsLogin.append('email', this.email);
                postParamsLogin.append('password', this.password);

                axios.post('/login', postParamsLogin)
                    .then(res => {
                        axios.get('/api/user')
                            .then(res => {
                                this.name                   = '';
                                this.email                  = '';
                                this.password               = '';
                                this.password_confirmation  = '';

                                this.$router.push(`/profile/${res.data.uuid}`);
                                this.$emit('update-authUser', res.data);
                            });
                    });
            },
            onInput(e) {
                if(
                    this.name.replace(' ', '') === ''
                    || this.email.replace(' ', '') === ''
                    || this.password.replace(' ', '') === ''
                    || this.password_confirmation.replace(' ', '') === ''
                ) {
                    this.isFilled = false;
                } else {
                    this.isFilled = true;
                }

                if(e.target.name === 'password' || e.target.name === 'password_confirmation') {
                    this.errorPassword = false;
                } else if (e.target.name === 'email') {
                    this.errorEmail    = false;
                }
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
    .Register {
        height: calc(100vh - 70px);
        width: 100%;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .Register__left {
        height: 100%;
        width: 60%;
    }

    .Register__right {
        height: 100%;
        width: 40%;
        background: linear-gradient(180deg, #2E2E2E 0%, #272727 100%);
    }

    h1 {
        color: #BEBEBE;
        text-align: center;
    }

    .Register__right__tabs {
        width: 100%;
        display: flex;
        flex-direction: row;
    }

    .Register__right__tabs__inactive {
        flex-grow: 1;
        height: 45px;
        background-color: transparent;
        font-size: 18px;
        color: rgba(190, 190, 190, 0.4);
        box-shadow: inset 0 0 100px 100px rgba(0, 0, 0, 0.23);
        cursor: pointer;
        outline: none;
    }

    .Register__right__tabs__inactive:hover {
        box-shadow: inset 0 0 100px 100px rgba(0, 0, 0, 0.10);
    }

    .Register__right__tabs__active {
        flex-grow: 1;
        height: 45px;
        background-color: transparent;
        font-size: 18px;
        color: #BEBEBE;
        cursor: default;
        outline: none;
    }

    .Register__container {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 90px;
    }

    .Register__container > label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 70%;
        margin-bottom: 20px;
        position: relative;
    }

    .Register__container > label > span {
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

    .Register__container input {
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

    .Register__container input:hover {
        border: solid 2px rgba(255, 255, 255, 0.09);
    }

    .Register__container input:focus {
        border: solid 2px rgba(255, 255, 255, 0.13);
    }

    .Register__container > label > p {
        color: rgba(255, 76, 76, 0.5);
        font-size: 14px;
        position: absolute;
        bottom: -38px;
        right: 0;
    }

    .Register__container__button{
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
        cursor: pointer;
    }

    .Register__container__button__disabled {
        background-color: #FFB74C;
        opacity: 0.3;
        color: white;
        border: none;
        outline: none;
        height: 45px;
        width: 70%;
        border-radius: 3px;
        font-size: 18px;
        margin-top: 14px;
        cursor: normal;
        pointer-events: none;
    }

    .Register__container__button:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
    }
</style>
