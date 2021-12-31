<template lang="html">
    <div class="Comment">
        <input v-if="authUser" @keypress="onInputKey" placeholder="Write a comment.." @click="onInputClick" ref="input" type="text" v-model="message">
        <div class="Comment__submit">
            <p v-if="commentsArr.length >= 1">{{ commentsArr.length }} {{ commentsArr.length == 1 ? 'Comment' : 'Comments' }}</p>
            <p v-else>No comments yet.</p>
            <span v-if="authUser">
                <button v-if="inputBool" @click="onCancel" class="Comment__submit__cancle">Cancel</button>
                <button v-if="inputBool" @click="postComment" class="Comment__submit__btn">Submit</button>
            </span>
        </div>

        <div class="Comment__display" v-for="(comment, comment_index) in commentsArr">
            <div class="Comment__display__top">
                <div class="Comment__display__top__user">
                    <img @click="() => { $router.push(`/profile/${ comment.user.uuid }`) }" class="Comment__display__top__user__img" :src="comment.user.img_url" >
                    <p @click="() => { $router.push(`/profile/${ comment.user.uuid }`) }" >{{ comment.user.name }}</p>
                    <button @click="onDelete(comment.id)" v-if="authUser && comment.user.id === authUser.id" class="Comment__display__top__user__delete">Delete</button>
                </div>
                <p
                    :title="new Date(comment.created_at).toDateString()"
                >{{ timeSince(comment.created_at) }}</p>
            </div>
            <p>{{ comment.message }}</p>

            <div class="Comment__display__top__user__reply__wrapper">
                <button
                    v-if="authUser && !showReplies[comment.id]"
                    class="Comment__display__top__user__reply"
                    @click="onReplyComment($event, comment.id)"
                >
                    Reply
                </button>

                <p v-if="comment.replies.length >= 1 && !showReplies[comment.id]">{{ comment.replies.length }} {{ comment.replies.length == 1 ? 'Reply' : 'Replies' }}</p>
            </div>

            <div class="Comment reply" v-if="authUser && showReplies[comment.id]">
                <input :data-parent-id="comment.id" @keypress="onReplyKey($event, comment.id, comment_index)" placeholder="Write a reply.." type="text" v-model="reply">
                <div class="Comment__submit">
                    <p v-if="comment.replies.length >= 1">{{ comment.replies.length }} {{ comment.replies.length == 1 ? 'Reply' : 'Replies' }}</p>
                    <span>
                        <button @click="onReplyCancel(comment.id)" class="Comment__submit__cancle">Cancel</button>
                        <button @click="postReply(comment.id, comment_index)" class="Comment__submit__btn">Submit</button>
                    </span>
                </div>
            </div>

            <div
                class="Comment__display replies"
                v-for="(reply, index) in comment.replies"
                :style="(index == 0) ? 'margin-top: 16px;' : ''"
            >
                <div class="Comment__display__top__user__replies__angle" v-if="index == 0"></div>

                <div class="Comment__display__top">
                    <div class="Comment__display__top__user">
                        <img @click="() => { $router.push(`/profile/${ reply.user.uuid }`) }" class="Comment__display__top__user__img" :src="reply.user.img_url" >
                        <p @click="() => { $router.push(`/profile/${ reply.user.uuid }`) }" >{{ reply.user.name }}</p>
                        <button @click="onDelete(reply.id)" v-if="authUser && reply.user.id === authUser.id" class="Comment__display__top__user__delete">Delete</button>
                    </div>
                    <p
                        :title="new Date(comment.created_at).toDateString()"
                    >{{ timeSince(reply.created_at) }}</p>
                </div>
                <p>{{ reply.message }}</p>
            </div>
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
                reply: '',
                inputBool: false,
                commentsArr: [],
                showReplies: {}
            }
        },
        methods: {
            postComment() {
                if(this.message.replace(' ', '').length < 1) return;

                axios.post(`/api/audio/${this.audio_id}/comment`, {
                    message: this.message,
                }).then(res => {
                    this.commentsArr.unshift(res.data);

                    this.$set(this.showReplies, res.data.id, false);
                    this.message    = '';
                    this.inputBool  = false
                    this.$refs.input.blur();
                });
            },
            onInputClick() {
                if(this.inputBool) return;

                this.inputBool = true;

                for (let key in this.showReplies) {
                    this.showReplies[key] = false;
                }

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
            },
            onReplyComment(e, parent_id) {
                for (let key in this.showReplies) {
                    this.showReplies[key] = false;
                }

                this.reply                  = '';
                this.showReplies[parent_id] = true;

                this.$nextTick(function() {
                    $(`input[data-parent-id=${ parent_id }]`).focus();
                });
            },
            onReplyKey(e, parent_id, parent_index) {
                if(e.keyCode === 13) {
                    this.postReply(parent_id, parent_index);
                }
            },
            postReply(parent_id, parent_index) {
                if(this.reply.replace(' ', '').length < 1) return;

                axios.post(`/api/audio/${this.audio_id}/comment`, {
                    message: this.reply,
                    parent_id: parent_id,
                }).then(res => {
                    this.commentsArr[parent_index].replies.push(res.data);

                    this.reply                  = '';
                    this.showReplies[parent_id] = false;
                });
            },
            onReplyCancel(parent_id) {
                this.reply                  = '';
                this.showReplies[parent_id] = false;
            },
            timeSince(date) {
                let seconds = Math.floor((new Date() - new Date(date)) / 1000);

                let interval = Math.floor(seconds / 31536000);
                if (interval > 1) return interval + " years ago";
                if (interval == 1) return interval + " year ago";

                interval = Math.floor(seconds / 2592000);
                if (interval > 1) return interval + " months ago";
                if (interval == 1) return interval + " month ago";

                interval = Math.floor(seconds / 86400);
                if (interval > 1) return interval + " days ago";
                if (interval == 1) return interval + " day ago";

                interval = Math.floor(seconds / 3600);
                if (interval > 1) return interval + " hours ago";
                if (interval == 1) return interval + " hour ago";

                interval = Math.floor(seconds / 60);
                if (interval > 1) return interval + " minutes ago";
                if (interval == 1) return interval + " minute ago";

                return "Just now";
            }
        },
        mounted() {
            axios.get(`/api/audio/${this.audio_id}/comments`)
                .then(res => {
                    this.commentsArr = res.data.reverse();
                    this.showReplies = {};

                    this.commentsArr.forEach((comment, i) => {
                        this.$set(this.showReplies, comment.id, false);
                    });
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
                        this.showReplies = {};

                        this.commentsArr.forEach((comment, i) => {
                            this.$set(this.showReplies, comment.id, false);
                        });
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
        justify-content: space-between;
        align-items: center;
        height: 32px;
        margin-bottom: 10px;
    }

    .Comment__submit > p {
        font-weight: 300;
        font-size: 16px;
        color: rgba(255, 255, 255, 0.3);
        text-align: left;
        margin: 0;
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
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        position: relative;
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
        width: 100%;
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
        background: transparent;
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

    .replies {
        margin-left: 15%;
        width: 85%;
    }

    .Comment__display__top__user__reply__wrapper {
        display: flex;
        align-items: center;
    }

    .Comment__display__top__user__reply__wrapper > p {
        font-weight: 300;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.3);
        text-align: left;
        margin: 0;
        margin-left: 12px;
    }

    .Comment__display__top__user__reply {
        background-color: transparent;
        border: 2px solid rgba(255, 255, 255, 0.05);
        box-sizing: border-box;
        border-radius: 3px;
        color: rgba(255, 255, 255, 0.3);
        outline: none;
        font-size: 12px;
    }

    .Comment__display__top__user__reply:hover {
        border: 2px solid rgba(255, 183, 76, 0.30);
        color: rgba(255, 183, 76, 0.90);
        cursor: pointer;
    }

    .Comment__display__top__user__replies__angle {
        border-left: 2px solid white;
        border-bottom: 2px solid white;
        width: 12%;
        height: 28px;
        position: absolute;
        top: 0px;
        left: calc(-14.5%);
        opacity: 0.1;
    }

    .reply {
        margin: 0;
        width: 100%;
    }
</style>
