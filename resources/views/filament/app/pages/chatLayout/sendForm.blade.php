<form @submit.prevent="sendMessage">
    <input type="text" x-model="newMessage" placeholder="Type your message..." required />
    <button type="submit">Send</button>
</form>

<script>
    function sendMessage() {
        axios.post('/messages', {
            to_id: selectedContactId,
            body: this.newMessage
        }).then(response => {
            this.newMessage = '';
            console.log('Message sent:', response.data.message);
        }).catch(error => {
            console.error('Error sending message:', error);
        });
    }
</script>
