<template lang="html">
    <div class="Comment">
        <input @keypress="onInputKey" placeholder="Write a comment.." @click="onInputClick" ref="input" type="text" v-model="message">
        <div class="Comment__submit">
            <button v-if="inputBool" @click="onCancel" class="Comment__submit__cancle">Cancel</button>
            <button v-if="inputBool" @click="postComment" class="Comment__submit__btn">Submit</button>
        </div>
        <div v-if="commentsArr.length < 1" class="Comment__display">
            <p>No comments yet.</p>
        </div>
        <div class="Comment__display" v-for="comment in commentsArr">
            <div class="Comment__display__top">
                <div class="Comment__display__top__user">
                    <img @click="() => { $router.push(`/profile/${ comment.user.uuid }`) }" class="Comment__display__top__user__img" :src="comment.user.img_url" >
                    <p @click="() => { $router.push(`/profile/${ comment.user.uuid }`) }" >{{ comment.user.name }}</p>
                    <button @click="onDelete(comment.id)" v-if="comment.user.id === authUser.id" class="Comment__display__top__user__delete">Delete</button>
                </div>
                <p>{{ new Date(comment.created_at).toDateString() }}</p>
            </div>
            <p>{{ comment.message }}</p>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'Comment',
        props: [
            'audio_id',
            'authUser'
        ],
        data() {
            return {
                message: '',
                inputBool: false,
                commentsArr: [],
            }
        },
        methods: {
            postComment() {
                if(this.message.replace(' ', '').length < 1) return;

                axios.post(`/api/audio/${this.audio_id}/comment`, {
                    message: this.message,
                }).then(res => {
                    this.commentsArr.unshift(res.data);

                    this.message    = '';
                    this.inputBool  = false
                    this.$refs.input.blur();
                });
            },
            onInputClick() {
                if(this.inputBool) return;

                this.inputBool = true;

                document.addEventListener('click', this.onDocumentClick);
            },
            onInputKey(e) {
                if(e.keyCode === 13) {
                    this.postComment();
                }
            },
            onDocumentClick(e) {
                if(e.target === this.$refs.input || this.message.replace(' ', '').length > 0) return;

                this.inputBool = false;

                document.removeEventListener('click', this.onDocumentClick);
            },
            onCancel() {
                this.message    = '';
                this.inputBool  = false;
            },
            onDelete(id) {
                axios.delete(`/api/audio/comments/${id}`)
                    .then(res => {
                        axios.get(`/api/audio/${this.audio_id}/comments`)
                            .then(res => {
                                this.commentsArr = res.data.reverse();
                            });
                    });
            }
        },
        mounted() {
            axios.get(`/api/audio/${this.audio_id}/comments`)
                .then(res => {
                    this.commentsArr = res.data.reverse();
                });
        },
        beforeDestroy() {

            if(this.inputBool) {
                document.removeEventListener('click', this.onDocumentClick);
            }
        },
        watch: {
            audio_id: function(newVal, oldVal) {
                axios.get(`/api/audio/${this.audio_id}/comments`)
                    .then(res => {
                        this.commentsArr = res.data.reverse();
                    });
            },
        }
    }
</script>

<style lang="css" scoped>
    .Comment {
        width: 95%;
        max-width: 900px;
        margin-top: 25px;
        margin-bottom: 25px;
    }

    .Comment > input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 5px;
        outline: none;
        font-size: 18px;
        color: rgba(255,255,255,0.5);
        border: solid 2px rgba(255, 255, 255, 0.0);
        padding: 20px 15px;
        margin-bottom: 10px;
    }

    .Comment > input:hover {
        border: solid 2px rgba(255, 255, 255, 0.05);
    }

    .Comment > input:focus {
        border: solid 2px rgba(255, 255, 255, 0.10);
    }

    .Comment__submit {
        width: 100%;
        display: flex;
        justify-content: flex-end;
        height: 32px;
        margin-bottom: 10px;
    }

    .Comment__submit__btn {
        background-color: #FFB74C;
        opacity: 1;
        color: white;
        border: none;
        outline: none;
        height: 32px;
        width: 96px;
        border-radius: 18px;
        margin-left: 12px;
    }

    .Comment__submit__btn:hover {
        box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .Comment__submit__cancle {
        background-color: transparent;
        width: 96px;
        height: 32px;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 18px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
    }

    .Comment__submit__cancle:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }

    .Comment__display {
        width: 100%;
        margin-bottom: 16px;
    }

    .Comment__display > p {
        font-weight: 300;
        font-size: 16px;
        color: rgba(255, 255, 255, 0.6);
        text-align: left;
        margin-top: 8px;
    }

    .Comment__display__top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .Comment__display__top > p {
        font-weight: 300;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.4);
    }

    .Comment__display__top__user {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
    }

    .Comment__display__top__user__img {
        height: 42px;
        width: 42px;
        object-fit: cover;
        border-radius: 5px;
        background: #BEBEBE;
        overflow: hidden;
        margin-right: 12px;
    }

    .Comment__display__top__user__delete {
        background-color: transparent;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 3px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
        margin-left: 12px;
        font-size: 12px;
    }

    .Comment__display__top__user__delete:hover {
        border: 2px solid rgba(255, 76, 76, 0.15);
        color: rgba(255, 76, 76, 0.45);
        cursor: pointer;
    }
</style>
