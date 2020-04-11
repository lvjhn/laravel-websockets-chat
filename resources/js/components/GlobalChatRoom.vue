<template>
    <div class="chat-room container">
        <div class="messages">
            <div class="messages-container">
                <h3>Global Chat Room</h3>
                <hr />
                <div class="messages-list">
                    <div v-for="message in messages" class="message-item">
                        <b>{{message.user.name}}</b>: {{message.message}}
                    </div>
                </div>
            </div>
            <div class="message-controls-container">
                <input type="text" class="new-message-textarea" placeholder="Enter message... (and press enter)"
                          @keyup.enter="sendMessage" @keydown="whisperTyping" v-model="bufferMessageText"/>
                <div class="is-typing-container">
                    <br />
                    <ul>
                        <li v-for="typer in typers">
                            &nbsp;&nbsp;{{typer}} is typing...
                        </li>
                    </ul>
                </div>
            </div>  
        </div>
        <div class="users">
            <div class="users-container">
                <h3>Online Users</h3>
                <hr />
                <ul>    
                    <li v-for="user in users">{{user.name}}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    export default {
        props: [
            "user"
        ],
        data () {
            return {
                bufferMessageText: "",
                users: [],
                typers: [],
                messages: []
            }
        },
        methods: {
            sendMessage() {
                let messageText = this.bufferMessageText;
                console.log("Sending message: " + messageText)
                axios.post("/messages/send", { message: messageText })
                     .then(response => {
                         this.messages.push(response.data);
                     }) 
                this.bufferMessageText = "";
            },
            whisperTyping() {
                console.log("Whispering typing...")
                Echo.private('global-chatroom')
                    .whisper('typing', this.user)
            }
        },
        mounted() {
            let messages = axios.get("/messages/get").then(response => {
                this.messages = response.data;
            })

            let chatroom = 'global-chatroom'
            Echo.join(chatroom)
                .here(users => {
                    console.log("Entered chatroom: " + chatroom); 
                    console.log("Users: ");
                    console.log(users);
                    this.users = users;
                })
                .joining(user => {
                    console.log("A user has joined");
                    console.log(user);
                    this.users.push(user);
                })
                .leaving(user => {
                    console.log("A user has left");
                    console.log(user);
                    this.users.splice(
                        this.users.indexOf(user), 
                        1
                    );
                })
                .listen("NewMessage", data => {
                    console.log("A new message was received");
                    console.log(data.message);
                    this.messages.push(data.message);
                })
        },
        created() {
            let self = this;
            Echo.private('global-chatroom')
                .listenForWhisper('typing', user => {
                    console.log("Handled typing event ");
                    console.log("Who? " + user.id);
                    if(this.typers.indexOf(user.name) == -1) {
                        this.typers.push(user.name);
                    }
                    let self = this;
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(function(){
                        self.typers.splice(self.typers.indexOf(user), 1)
                    }, 500);
                })
        }
    }
</script>

<style scoped lang="scss">
    .chat-room {
        display: flex;
    }

    .messages {
        width: 70%;

        .messages-container {
            margin-right: 50px;
            box-shadow: 0px 0px 1px 1px grey;
            padding: 20px;
            height: 430px;

            .messages-list {
                height: 350px;
                overflow-y: scroll;
            }

        }

        .message-controls-container {
            margin-right: 50px;
            margin-top: 20px;
            height: 65px;
            
            .new-message-textarea {
                border: none;
                font-size: 15px;
                height: 50px;
                resize: none;
                overflow-y: scroll;
                padding: 20px;
                box-shadow: 0px 0px 1px 1px grey;
            }

            .new-message-textarea::-webkit-scrollbar {
                display: none;
            }
        }
    }

    .users {
        width: 30%;

        .users-container {
            box-shadow: 0px 0px 1px 1px grey;
            padding: 20px;
            height: 515px;
        }
    }
</style>