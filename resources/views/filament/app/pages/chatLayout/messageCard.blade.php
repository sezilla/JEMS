<div>
    <ul>
        <template x-for="message in messages" :key="message.id">
            <li x-text="message.body"></li>
        </template>
    </ul>
</div>
