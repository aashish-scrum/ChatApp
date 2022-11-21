<style>
@import '../../css/chat.css';
.v3-emoji-picker{
    position: absolute;
    text-align: left;
    bottom: 85%;
    left: 2%;
}
.v3-emoji-picker .v3-footer{
    display: none;
}
.unread-badge{
    font-size: 11px;
    border-radius: 25px;
    padding: 3px 5px;
}
</style>
<template>
    <div class="chat-area">
        <!-- chatlist -->
        <div class="chatlist p-0">
            <div class="modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="chat-header p-3">
                        <div class="msg-search">
                            <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Search"
                                aria-label="search">
                            <a class="add" href="#"><img class="img-fluid"
                                    src="https://mehedihtml.com/chatbox/assets/img/add.svg" alt="add"></a>
                        </div>
                    </div>

                    <div class="modal-body mt-1 border-top">
                        <div class="chat-lists">
                            <div class="chat-list">
                                <template v-for="contact in users">
                                    <a href="javascript:void(0)" class="d-flex align-items-center px-3 py-2" v-bind:class="(contact.id == chatUser.id) ? 'selected-user' : '' "
                                        @click="fetchMessages(contact)">
                                        <div class="flex-shrink-0">
                                            <img class="img-fluid"
                                                v-bind:src="contact.avatar"
                                                alt="user img">
                                            <span class="active"></span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3>{{ contact.name }}</h3>
                                            <p>{{ contact.email }}</p>
                                        </div>
                                        <span class="badge text-bg-danger unread-badge" v-if="contact.unread_messages_count > 0">{{contact.unread_messages_count}}</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- chatbox -->
        <div class="chatbox">
            <div class="modal-dialog-scrollable">
                <div class="modal-content d-none" id="chatBox">
                    <div class="msg-head">
                        <div class="row">
                            <div class="col-8">
                                    <div class="d-flex align-items-center">
                                        <span class="chat-icon"><img class="img-fluid avatar"
                                                v-bind:src="chatUser.avatar"
                                                alt="image title"></span>
                                        <div class="flex-shrink-0">
                                            <img class="img-fluid avatar" v-bind:src="chatUser.avatar" alt="user img">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3>{{ chatUser.name }}</h3>
                                            <p>{{ chatUser.email }}</p>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="scrollable modal-body" ref="hasScrolledToBottom">
                        <div class="msg-body" v-if="messages.length > 0">
                            <ul class="">
                                <template v-for="message in messages">
                                    <li class="sender" v-if="chatUser.id == message.sender_id">
                                        <p><span class="">{{ message.sender.name }} : </span> {{ message.message }} </p>
                                        <span class="time">{{ message.created_at }}</span>
                                    </li>
                                    <li class="repaly" v-else-if="user.id == message.sender_id">
                                        <p><span>You : </span>{{ message.message }}</p>
                                        <span class="time">{{ message.created_at }}</span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="w-100 h-100 d-flex justify-content-center align-items-center" v-else>
                            <h5>Say Hi to <span style="font-weight: 600;">{{ chatUser.name }}</span> 👋</h5>
                        </div>
                    </div>

                    <div class="send-box position-relative">
                        <form action="javascript:void(0)">
                            <EmojiPicker :display-recent="true" :disableSkinTones="false" @select="onSelectEmoji" class="d-none" />
                            <a tabindex="0" class="p-2" role="button" @click="showEmoji" >😀</a>
                            <input type="text" class="form-control" aria-label="message…" placeholder="Write message…"
                                v-model="newMessage" @keyup.enter="addMessage">

                            <button type="button" id="btn-chat" @click="addMessage"><i class="fa fa-paper-plane"
                                    aria-hidden="true"></i> Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive, inject, ref, onMounted, onUpdated } from 'vue';
import axios from 'axios';
import "bootstrap/dist/js/bootstrap.min.js";
import { Popover } from 'bootstrap/dist/js/bootstrap.esm.min.js'

export default {
    props: ['user'],
    setup(props) {
        let users = ref([])
        let chatUser = ref('')
        let messages = ref([])
        let newMessage = ref('')
        let hasScrolledToBottom = ref('')
        onMounted(() => {
            Array.from(document.querySelectorAll('button[data-bs-toggle="popover"],a[data-bs-toggle="popover"]'))
                .forEach(popoverNode => new Popover(popoverNode,{
                    trigger : 'focus body',
                }))
            fetchUsers()
        })
        onUpdated(() => {
            scrollBottom()
        })
        Echo.private('chat-channel')
            .listen('SendMessage', (e) => {
                messages.value.push(e.message);
                console.log(props.user,users.value,chatUser.value,e.message);
                if(e.message.sender_id != chatUser.value.id && e.message.receiver_id == props.user.id && e.message.read == 0){
                    let i = users.value.map(item => item.id).indexOf(e.message.sender_id) // find index of your object
                    users.value.splice(i, 1);
                    fetchUnread(e.message.sender_id);
                }
                console.log(users.value);
            })
        const fetchUsers = async () => {
            axios.get('/operator/users').then(response => {
                users.value = response.data;
            });
        }
        const fetchUnread = async (user) => {
            axios.get('/operator/user/fetch-unread/'+user).then(response => {
                users.value.unshift(response.data);
            });
        }
        const fetchMessages = async (selectedUser) => {
            document.querySelector('#chatBox').classList.remove('d-none');
            if(chatUser.value.id != selectedUser.id){
                chatUser.value = selectedUser;
                axios.get('/operator/contact-messages/' + selectedUser.id).then(response => {
                    messages.value = response.data;
                });
            }
        }
        const onSelectEmoji = async (emoji) => {
            newMessage.value += emoji.i;
        }
        function showEmoji() {
            document.querySelector('.v3-emoji-picker').classList.toggle('d-none');
        }
        const addMessage = async () => {
            let user_message = {
                user: props.user.id,
                receiver : chatUser.value.id,
                message: newMessage.value
            };
            axios.post('/operator/messages', user_message).then(response => {
                messages.value.push(response.data);
            });
            newMessage.value = ''
        }
        const scrollBottom = () => {
            if (messages.value.length > 1) {
                axios.get('/operator/read/' + chatUser.value.id).then(response => {
                    if(document.querySelector('.selected-user > .unread-badge') != null){
                        document.querySelector('.selected-user > .unread-badge').remove();
                    }
                });
                let el = hasScrolledToBottom.value;
                el.scrollTop = el.scrollHeight;
            }
        }
        return {
            users,
            messages,
            chatUser,
            newMessage,
            fetchUnread,
            addMessage,
            fetchMessages,
            showEmoji,
            onSelectEmoji,
            hasScrolledToBottom
        }
    }
}
</script>