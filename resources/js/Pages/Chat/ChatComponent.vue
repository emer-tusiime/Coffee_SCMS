<template>
  <div class="chat-container">
    <div class="chat-header">
      <h4>Chat</h4>
      <select v-model="selectedUser" @change="loadMessages">
        <option value="">Select User</option>
        <option v-for="user in users" :key="user.id" :value="user.id">
          {{ user.name }}
        </option>
      </select>
    </div>

    <div class="chat-messages" ref="messagesContainer">
      <div v-for="(message, index) in messages" :key="index" :class="[message.sender_id === Auth.user.id ? 'sent' : 'received']">
        <div class="message-content">
          <span class="message-text">{{ message.message }}</span>
          <span class="message-time">{{ formatTime(message.created_at) }}</span>
        </div>
      </div>
    </div>

    <div class="chat-input">
      <input
        type="text"
        v-model="newMessage"
        @keypress.enter="sendMessage"
        placeholder="Type a message..."
      />
      <button @click="sendMessage">Send</button>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default defineComponent({
  data() {
    return {
      messages: [],
      newMessage: '',
      selectedUser: '',
      users: [],
    };
  },

  computed: {
    Auth() {
      return usePage().props.auth;
    },
  },

  mounted() {
    this.loadUsers();
    this.listenForMessages();
  },

  methods: {
    async loadUsers() {
      const response = await axios.get(route('chat.users'));
      this.users = response.data;
    },

    async loadMessages() {
      if (!this.selectedUser) return;
      
      const response = await axios.get(route('chat.messages'), {
        params: { receiver_id: this.selectedUser },
      });
      this.messages = response.data;
      this.scrollToBottom();
    },

    async sendMessage() {
      if (!this.selectedUser || !this.newMessage.trim()) return;

      try {
        await axios.post(route('chat.send'), {
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
        const container = this.$refs.messagesContainer;
        container.scrollTop = container.scrollHeight;
      });
    },

    formatTime(time) {
      return new Date(time).toLocaleTimeString();
    },
  },
});
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  max-width: 800px;
  margin: 0 auto;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chat-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.sent {
  align-self: flex-end;
  max-width: 70%;
}

.received {
  align-self: flex-start;
  max-width: 70%;
}

.message-content {
  background: #e3f2fd;
  padding: 0.75rem 1rem;
  border-radius: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.sent .message-content {
  background: #dcf8c6;
}

.message-text {
  font-size: 1rem;
}

.message-time {
  font-size: 0.75rem;
  color: #666;
}

.chat-input {
  padding: 1rem;
  border-top: 1px solid #eee;
  display: flex;
  gap: 1rem;
}

.chat-input input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.chat-input button {
  padding: 0.75rem 1.5rem;
  background: #2196f3;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.chat-input button:hover {
  background: #1976d2;
}
</style>
