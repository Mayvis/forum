<script>
    import Replies from '../components/Replies.vue';
    import Highlight from '../components/Highlight.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],
        components: {Replies, SubscribeButton, Highlight},
        data() {
            return {
                repliesCount: this.thread.replies_counts,
                locked: this.thread.locked,
                title: this.thread.title,
                body: this.thread.body,
                pinned: this.thread.pinned,
                form: {},
                editing: false
            }
        },
        created() {
            this.resetForm();
        },
        methods: {
            togglePin() {
                let uri = `/pinned-threads/${this.thread.slug}`;

                axios[this.pinned ? 'delete' : 'post'](uri);

                this.pinned = !this.pinned;
            },
            toggleLock() {
                let uri = `/locked-threads/${this.thread.slug}`;

                axios[this.locked ? 'delete' : 'post'](uri);

                this.locked = !this.locked;
            },
            classes(target) {
                return [
                    'btn',
                    target ? 'btn-primary' : 'btn-default'
                ];
            },
            update() {
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;

                axios.patch(uri, this.form).then(() => {
                    this.editing = false;
                    this.title = this.form.title;
                    this.body = this.form.body;

                    flash('Your thread had been updated.');
                });
            },
            resetForm() {
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body
                };

                this.editing = false;
            }
        },
    }
</script>
