<template>
    <div>
        <form method="GET" :action="this.search.route" @submit.prevent>
            <input type="text" name="search" placeholder="Search.." @keyup="sendRequest" v-model="form.input" style="width: 220px">
        </form>
    </div>
</template>

<script>
    import Form from '../../classes/Form';
    import Search from '../../core/search';

    export default {
        data() {
            return {
                search: Search,
                form: new Form({
                    input: ''
                })
            }
        },

        methods: {
            sendRequest: function() {
                let input = this.form.input.trim();

                if (input.length > 0) {
                    this.search.setRoute(input);
                    this.$router.push({
                        name: 'searchResults',
                        params: {
                            input: input
                        }
                    })
                        .catch(err => {});
                }
            }
        }
    }
</script>

<style scoped>
    div {
        margin-left: 34%;
    }

    form input {
        background-color: #f8f9fa;
        border-radius: 17px;
        border: 1px solid #e6e6e6;
        padding: 5px 12px;
    }

    form input:focus {
        outline: none;
    }
</style>