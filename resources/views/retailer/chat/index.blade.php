@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Chat</h5>
                    <select class="form-select" id="userSelect" v-model="selectedUser" @change="loadMessages">
                        <option value="">Select User</option>
                        @foreach($messages->unique('sender_id')->pluck('sender') as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="card-body chat-messages" id="messagesContainer">
                    <div v-for="message in messages" :class="[message.sender_type === 'retailer' ? 'sent' : 'received']">
                        <div class="message-content">
                            <span class="message-text">{{ message.message }}</span>
                            <span class="message-time">{{ message.created_at }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <form @submit.prevent="sendMessage">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   v-model="newMessage" 
                                   placeholder="Type a message..."
                                   @keypress.enter="sendMessage">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/app.js') }}"></script>
<script>
    import { defineComponent } from 'vue';
    import { usePage } from '@inertiajs/vue3';

    const app = defineComponent({
        data() {
            return {
                messages: [],
                newMessage: '',
                selectedUser: '',
            };
        },

        computed: {
            Auth() {
                return usePage().props.auth;
            },
        },

        mounted() {
            this.listenForMessages();
        },

        methods: {
            async loadMessages() {
                if (!this.selectedUser) return;

                try {
                    const response = await axios.get(route('retailer.chat.messages'), {
                        params: { receiver_id: this.selectedUser },
                    });
                    this.messages = response.data;
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Error loading messages:', error);
                }
            },

            async sendMessage() {
                if (!this.selectedUser || !this.newMessage.trim()) return;

                try {
                    await axios.post(route('retailer.chat.send'), {
                        message: this.newMessage,
                        receiver_id: this.selectedUser,
                    });
                    this.newMessage = '';
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            },

            listenForMessages() {
                window.Echo.private(`chat.${this.Auth.user.id}`)
                    .listen('.new-message', (e) => {
                        if (e.message.receiver_id === this.selectedUser) {
                            this.messages.push(e.message);
                            this.scrollToBottom();
                        }
                    });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('messagesContainer');
                    container.scrollTop = container.scrollHeight;
                });
            },
        },
    });

    app.mount('#app');
</script>
@endpush
