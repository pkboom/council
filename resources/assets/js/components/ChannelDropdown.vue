<template>
    <li class="dropdown">
        <a href="#" 
            class="dropdown-toggle" 
            id="dropdownMenuLink" 
            data-toggle="dropdown" 
            aria-haspopup="true" 
            aria-expanded="false">
            Channels <span class="caret"></span>
        </a>

        <div class="dropdown-menu channel-dropdown" aria-labelledby="dropdownMenuLink">
            <div class="input-wrapper">
                <input type="text" 
                        class="form-control" 
                        v-model="filter" 
                        placeholder="Filter Channels..."/>
            </div>

            <ul class="list-group channel-list">
                <li class="list-group-item" v-for="channel in filterChannels">
                    <a :href="`/threads/${channel.slug}`" v-text="channel.name"></a>
                </li>
            </ul>
        </div>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                channels: [],
                filter: ''
            };
        },

        created() {
            axios.get('/threads/channels').then(({ data }) => {
                this.channels = data
            });
        },

        computed: {
            filterChannels() {
                return this.channels.filter(channel => {
                    return channel.name
                        .toLowerCase()
                        .startsWith(this.filter.toLocaleLowerCase());
                    // return channel.name.toLowerCase().includes(this.filter.toLocaleLowerCase())
                });
            }
        },
    }
</script>

<style lang="scss">
    .channel-dropdown {
        padding: 0;
    }

    .input-wrapper {
        padding: .5rem 1rem;
    }

    .channel-list {
        max-height: 400px;
        overflow: auto;
        margin-bottom: 0;

        .list-group-item {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
    }
</style>
