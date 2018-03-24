<template>
    <div>
        <button :class="classes" @click="subscribe">Subscribe</button>
    </div>
</template>

<script>
    export default {
        props: ['active'],

        data() {
            return {
                isSubscribedTo: this.active,
            }
        },

        computed: {
            classes() {
                return ['btn', this.isSubscribedTo ? 'btn-primary' : 'btn-default'];
            }
        },

        methods: {
            subscribe() {
                axios[
                    this.isSubscribedTo ? 'delete' : 'post'
                ](location.pathname + '/subscriptions');

                this.isSubscribedTo = !this.isSubscribedTo;
            }
        }
    }
</script>