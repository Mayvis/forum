<template>
    <div :id="'reply-' + id" class="card mb-3">
        <div class="card-header" :class="isBest ? 'bg' : ''">
            <div class="level">
                <h6 class="flex">
                    <a :href="'/profiles/' + reply.owner.name"
                       v-text="reply.owner.name">
                    </a> said <span v-text="ago"></span>...
                </h6>

                <div v-if="signedIn">
                    <favorite :reply="reply"></favorite>
                </div>

            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <wysiwyg v-model="body"></wysiwyg>
                        <!--<textarea class="form-control" v-model="body" title="body" name="body" required></textarea>-->
                    </div>

                    <button class="btn btn-sm btn-primary">Update</button>
                    <button class="btn btn-sm btn-link" @click="remainTheSame" type="button">Cancel</button>
                    <!--<button class="btn btn-sm btn-link" @click="editing = false" type="button">Cancel</button>-->
                </form>
            </div>

            <div v-else ref="body">
                <highlight :content="body"></highlight>
            </div>
        </div>

        <div class="card-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="btn btn-sm mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-sm mr-1" @click="destroy">Delete</button>
            </div>

            <button class="btn btn-default btn-sm ml-auto" @click="markBestReply"
                    v-if="authorize('owns', reply.thread)">Best Reply?
            </button>
        </div>

    </div>
</template>

<script>
    import Favorite from './Favorite.vue';
    import Highlight from './Highlight.vue';
    import moment from 'moment';

    export default {
        props: ['reply'],
        components: {Favorite, Highlight},
        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest,
            }
        },
        created() {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },
        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow();
            },
        },
        methods: {
            remainTheSame() {
                this.editing = false;

                this.body = this.reply.body;
            },
            update() {
                axios.patch('/replies/' + this.id, {
                    body: this.body
                }).catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;

                flash('updated!');
            },
            destroy() {
                axios.delete('/replies/' + this.id);

                this.$emit('deleted', this.id);
            },
            markBestReply() {
                axios.post('/replies/' + this.id + '/best');

                window.events.$emit('best-reply-selected', this.id);
            }
        },
    }
</script>
