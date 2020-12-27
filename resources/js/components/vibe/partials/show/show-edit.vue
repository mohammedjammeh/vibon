<template>
    <div>
        <div v-if="isNotInEditModel">
            <p v-text="this.vibes.show.name"></p>
            <p v-text="this.vibes.show.description"></p>

            <p v-if="this.vibes.show.open">Opened</p>
            <p v-else>Not Opened</p>

            <p v-if="this.vibes.show.auto_dj">Auto DJ</p>
            <p v-else>Manual DJ</p>
            <br>

            <div v-if="this.vibes.show.destroyable">
                <button  @click="this.turnOnEditMode">Edit</button>
                <br><br>
            </div>
        </div>

        <div v-else>
            <form v-if="this.vibes.show.destroyable" method="POST" :action="this.vibes.routes.update(vibes.show.id)" @submit.prevent="onUpdateSubmit" autocomplete="off">
                <div>
                    <input type="text" name="name" placeholder="Name" v-model="editForm.name" @keydown="editForm.errors.clear('name')">
                    <span v-text="editForm.errors.get('name')" v-if="editForm.errors.has('name')"></span>
                </div>
                <br>

                <div>
                    <p>Open</p>

                    <input type="radio" name="open" id="open-yes" v-model="editForm.open" value="1" @click="editForm.errors.clear('open')">
                    <label for="open-yes">Yes</label>

                    <input type="radio" name="open" id="open-no" v-model="editForm.open" value="0" @click="editForm.errors.clear('open')">
                    <label for="open-no">No</label>

                    <span v-text="editForm.errors.get('open')" v-if="editForm.errors.has('open')"></span>
                </div>
                <br>

                <div>
                    <p>Auto DJ:</p>

                    <input type="radio" name="auto_dj" id="auto-dj-yes" v-model="editForm.auto_dj" value="1" @click="editForm.errors.clear('auto_dj')">
                    <label for="auto-dj-yes">Yes</label>

                    <input type="radio" name="auto_dj" id="auto-dj-no" v-model="editForm.auto_dj" value="0" @click="editForm.errors.clear('auto_dj')">
                    <label for="auto-dj-no">No</label>

                    <span v-text="editForm.errors.get('auto_dj')" v-if="editForm.errors.has('auto_dj')"></span>
                </div>
                <br>

                <div>
                    <textarea name="description" cols="19" rows="5" placeholder="Description" v-model="editForm.description" @keydown="editForm.errors.clear('description')"></textarea>
                    <span v-text="editForm.errors.get('description')" v-if="editForm.errors.has('description')"></span>
                </div>
                <br>

                <div>
                    <input type="submit" name="vibe-edit" value="Update" :disabled="editForm.errors.any()">
                    <button @click="this.turnOffEditMode">Cancel</button>
                </div>
                <br>
            </form>
        </div>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        data() {
            return {
                vibes: vibes,
                editMode: false,
                editForm: new Form({
                    name: '',
                    description: '',
                    open: '',
                    auto_dj: '',
                }),
            }
        },

        methods: {
            onUpdateSubmit() {
                this.vibes.update(this.editForm, vibes.show.id)
                    .then(() => this.editMode = false);
            },


            turnOnEditMode() {
                this.editForm.name = this.vibes.show.name;
                this.editForm.open = + this.vibes.show.open;
                this.editForm.auto_dj = + this.vibes.show.auto_dj;
                this.editForm.description = this.vibes.show.description;
                this.editMode = true;
            },

            turnOffEditMode() {
                this.editMode = false;
            }
        },

        computed : {
            isNotInEditModel() {
                return !this.editMode;
            }
        }
    }
</script>