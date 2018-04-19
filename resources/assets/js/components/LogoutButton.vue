<template>
    <a :href="route" @click.prevent="logout">
        <slot/>
    </a>
</template>

<script>
    export default {
        props: {
            route: {default: '/logout'}
        },

        methods: {
            logout() {
                axios.post(this.route).then(({request}) => {
                    window.location.href = request.responseURL;
                    // window.location.assign(url);
                    // window.location = url;
                    // window.location.href = url;
                });
            }
        }
    }
</script>
