<template>
    <div>
        <div v-html="content" ref="content"></div>
    </div>
</template>

<script>
    import Hightlighter from 'highlight.js';
    import 'highlight.js/styles/foundation.css';

    export default {
        props: ['content'],
        mounted() {
            this.highlight(this.$refs.content);
        },
        methods: {
            highlight(block) {
                block.querySelectorAll('pre').forEach(
                    node => Hightlighter.highlightBlock(node)
                )
            },
        },
        watch: {
            content() {
                this.$nextTick (() => {
                    this.highlight(this.$refs['content']);
                });
            }
        }
    }
</script>

