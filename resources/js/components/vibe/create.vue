<template>
    <div>
        <button @click="$modal.show('create-vibe-modal')">start vibe</button>

        <modal
            name="create-vibe-modal"
            height="auto"
        >
            <form method="POST" :action="this.vibes.routes.create" @submit.prevent="onSubmit" autocomplete="off">
                <div>
                    <input type="text" name="name" placeholder="Name" v-model="form.name" @keydown="form.errors.clear('name')">
                    <span v-text="form.errors.get('name')" v-if="form.errors.has('name')"></span>
                </div>
                <br>

                <div>
                    <p>Open</p>

                    <input type="radio" name="open" id="open-yes" v-model="form.open" value="1" @click="form.errors.clear('open')">
                    <label for="open-yes">Yes</label>

                    <input type="radio" name="open" id="open-no" v-model="form.open" value="0" @click="form.errors.clear('open')">
                    <label for="open-no">No</label>

                    <span v-text="form.errors.get('open')" v-if="form.errors.has('open')"></span>
                </div>
                <br>


                <div>
                    <p>Auto DJ:</p>

                    <input type="radio" name="auto_dj" id="auto-dj-yes" v-model="form.auto_dj" value="1" @click="form.errors.clear('auto_dj')">
                    <label for="auto-dj-yes">Yes</label>

                    <input type="radio" name="auto_dj" id="auto-dj-no" v-model="form.auto_dj" value="0" @click="form.errors.clear('auto_dj')">
                    <label for="auto-dj-no">No</label>

                    <span v-text="form.errors.get('auto_dj')" v-if="form.errors.has('auto_dj')"></span>
                </div>
                <br>

                <div>
                    <textarea name="description" cols="19" rows="5" placeholder="Description" v-model="form.description" @keydown="form.errors.clear('description')"></textarea>
                    <span v-text="form.errors.get('description')" v-if="form.errors.has('description')"></span>
                </div>
                <br>

                <div>
                    <input type="submit" name="vibe-create" value="Start" :disabled="form.errors.any()">
                </div>
            </form>
        </modal>
    </div>
</template>

<script>
    import Form from '../../classes/Form.js';
    import vibes from '../../core/vibes.js';

    export default {
        data() {
            return {
                form: new Form({
                    name: '',
                    description: '',
                    open: '',
                    auto_dj: '',
                }),
                vibes: vibes
            }
        },
        methods: {
            onSubmit() {
                this.vibes.create(this.form)
                    .then((vibe) => {
                        if (this.form.errors.any()) {
                            return;
                        }

                        this.$modal.hide('create-vibe-modal');
                        this.$router.push({
                            name: 'showVibe',
                            params: {
                                id: vibe.id
                            }
                        }).catch(err => {});
                    });
            }
        }
    }
</script>

<style scoped>
    button {
        padding: 5px 10px;
        border: 1px solid lightgrey;
        border-radius: 3px;
    }

    form {
        margin: 20px;
    }

    div[data-modal="create-vibe-modal"] {
        background: rgba(0, 0, 0, 0.4);
    }
</style>