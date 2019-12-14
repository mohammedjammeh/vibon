<template>
    <div style="margin-left: 29%">
        <form method="GET" :action="this.search.route" @submit.prevent>
            <input type="text" name="search" placeholder="Search.." @keyup="sendRequest" v-model="form.input" style="width: 220px">
        </form>
    </div>
</template>

<script>
    import Form from '../../classes/Form';
    import Search from '../../core/search';
    import _ from 'lodash';

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
            sendRequest: _.debounce(function() {
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
            }, 2000)
        }
    }
</script>