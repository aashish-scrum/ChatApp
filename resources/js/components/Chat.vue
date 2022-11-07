<style>
@import '../../css/chat.css';
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

                    <div class="modal-body mt-1 p-3 border-top">
                        <div class="chat-lists">
                            <div class="chat-list">
                                <a href="javascript:void(0)" class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img class="img-fluid"
                                            src="https://mehedihtml.com/chatbox/assets/img/user.png"
                                            alt="user img">
                                            <span class="active"></span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>Ryhan</h3>
                                        <p>front end developer</p>
                                    </div>
                                </a>
                                <template v-for="contact in users">
                                    <a href="javascript:void(0)" class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img class="img-fluid"
                                                src="https://mehedihtml.com/chatbox/assets/img/user.png"
                                                alt="user img">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h3>{{contact.name}}</h3>
                                            <p>{{contact.email}}</p>
                                        </div>
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
                <div class="modal-content">
                    <div class="msg-head">
                        <div class="row">
                            <div class="col-8">
                                <div class="d-flex align-items-center">
                                    <span class="chat-icon"><img class="img-fluid"
                                            src="https://mehedihtml.com/chatbox/assets/img/arroleftt.svg"
                                            alt="image title"></span>
                                    <div class="flex-shrink-0">
                                        <img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/user.png"
                                            alt="user img">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>Mehedi Hasan</h3>
                                        <p>front end developer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="scrollable modal-body" ref="hasScrolledToBottom">
                        <div class="msg-body" >
                            <ul class="">
                                <template v-for="message in messages">
                                    <li class="sender" v-if="user.id != message.user.id">
                                        <p><span class="">{{ message.user.name }} : </span> {{ message.message }} </p>
                                        <span class="time">{{ message.created_at }}</span>
                                    </li>
                                    <li class="repaly" v-else>
                                        <p><span>You : </span>{{ message.message }}</p>
                                        <span class="time">{{ message.created_at }}</span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>


                    <div class="send-box">
                        <form action="javascript:void(0)">
                            <input type="text" class="form-control" aria-label="message…" placeholder="Write message…" v-model="newMessage" @keyup.enter="addMessage">

                            <button type="button" id="btn-chat" @click="addMessage"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import { reactive, inject,ref, onMounted,onUpdated } from 'vue';
	import axios from 'axios';
	export default{
		props:['user'],
	    setup(props){
	    	let users = ref([])
	    	let newUsers = ref('')
	    	let messages = ref([])
	    	let newMessage = ref('')
	    	let hasScrolledToBottom = ref('')
	    	onMounted(() =>{
	    		fetchUsers()
	    	})
	    	onUpdated(() => {
	    		scrollBottom()
	    	})
	    	Echo.private('chat-channel')
	          .listen('SendMessage', (e) => {
	            messages.value.push({
	              message: e.message.message,
	              user: e.user,
                  created_at : e.message.created_at,
	            });
	        })
	    	const fetchUsers = async()=> {
	            axios.get('/operator/users').then(response => {
	                users.value = response.data;
                    console.log(response.data);
	            });
	        }
	    	const fetchMessages = async()=> {
	            axios.get('/operator/messages').then(response => {
	                messages.value = response.data;
                    console.log(response.data);
	            });
	        }
	        const addMessage = async()=> {
	        	let user_message = {
                    user: props.user,
                    message: newMessage.value
                };
	            axios.post('/operator/messages', user_message).then(response => {
                    let responseData = {
                        user: props.user,
                        message: response.data.message,
                        created_at : response.data.created_at,
                    };
                    messages.value.push(responseData);
                    console.log(response.data);
	            });
                newMessage.value = ''
	        }
	        const scrollBottom = () =>{
	        	if(messages.value.length > 1){
		        	let el = hasScrolledToBottom.value;
	      			el.scrollTop = el.scrollHeight;
	        	}        	
	        }
	        return {
                users,
	        	messages,
	        	newMessage,
	        	addMessage,
	        	fetchMessages,
	        	hasScrolledToBottom
	        }
	    }
	}
</script>