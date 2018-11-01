<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
            <textarea name="body" id="body" cols="30" rows="5" class="form-control"
                      placeholder="Have something to say?" required v-model="body"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" @click="addReply">Post</button>
            </div>
        </div>
        <p class="text-center" v-else>Please <a href="/login">sign in</a> to participate in this disscussion.</p>
    </div>
</template>

<script>
    export default {
        props: ['endpoint'],
        data() {
            return {
                body: '',
            }
        }, computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },
        methods: {
            addReply() {
                axios.post(this.endpoint, {body: this.body})
                    .then(({data}) => {
                        this.body = '';

                        flash('Your reply has been posted!');

                        this.$emit('created', data);
                    });
            }
        },
    }
</script>

<style scoped>

</style>
