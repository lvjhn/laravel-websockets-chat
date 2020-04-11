<template>
    <div class="public-chat-room container">
        <div class="messages">
            <div class="messages-container">
                <h3>Public Chat Room ({{chatroom_id}})</h3>
                <hr />
                <div class="messages-list" v-chat-scroll>
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
            "user",
            "chatroom_id"
        ],
        data () {
            return {
                bufferMessageText: "",
                users: [],
                typers: [],
                messages: [],
            }
        },
        computed: {
            channel () {
                return "public-chatroom-" + this.chatroom_id;
            }
        },  
        methods: {
            sendMessage() {
                let messageText = this.bufferMessageText;
                console.log("Sending message: " + messageText)
                axios.post("/public_messages/send", { 
                        chatroom_id: this.chatroom_id,
                        message: messageText 
                      })
                     .then(response => {
                         this.messages.push(response.data);
                     }) 
                this.bufferMessageText = "";
            },
            whisperTyping() {
                console.log("Whispering typing...")
                Echo.private(this.channel)
                    .whisper('typing', this.user)
            }
        },
        mounted() {
            let messages = axios.get("/public_messages/get/" + this.chatroom_id).then(response => {
                this.messages = response.data;
            })

            Echo.join(this.channel)
                .here(users => {
                    console.log("Entered chatroom: " + this.channel); 
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
                .listen("NewPublicMessage", data => {
                    console.log("A new message was received");
                    console.log(data.message);
                    this.messages.push(data.message);
                })
        },
        created() {
            let self = this;
            Echo.private(this.channel)
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
    .public-chat-room {
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