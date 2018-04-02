<template>
    <div v-html="content" ref="content">
    </div>
</template>

<script>
    import Highlighter from 'highlight.js';

    export default {
        props: ['content'],

        mounted() {
            this.highlight(this.$refs.content);
        },

        methods: {
            highlight(block) {
                if (! block) return;

                block.querySelectorAll('pre').forEach(
                    node => Highlighter.highlightBlock(node)
                );
            }
        }
    }
</script>

<style lang="scss">
    @import '~highlight.js/styles/foundation.css';
</style>