<template>
    <form class="uk-search uk-search-default" :class="searchError ? 'uk-form-danger' : ''" :action="$props.searchUrl" @submit="event => ajaxSearch(event)">
        <span v-if="!loading" uk-search-icon></span>
        <span v-else uk-spinner></span>
        <input class="uk-search-input" name="name" type="search" placeholder="Search" v-model="input">
    </form>

    <div id="search-modal" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Search results</h2>
            <div>
                <p>
                    Player:
                    <a :href="'/player/' + player.id">{{ player.name }}</a>
                </p>
            </div>
            <div>
                <p>
                    Author:
                    <a :href="'/author/' + authorName">{{authorName}}</a>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import { ref, inject } from 'vue';

    export default {
        name: 'Search',
        props: ['searchUrl'],
        setup(props) {
            const axios = inject('axios')
            const input = ref('');
            const player = ref({});
            const authorName = ref(null);
            const loading = ref(false);
            const searchError = ref(null);

            async function ajaxSearch(event) {
                event.preventDefault();
                if (input.value.count < 3) {
                    return;
                }

                const {data, error} = await axios.post(props.searchUrl, { name: input.value });
                if (error) {
                    console.error(error);
                }

                if (!data.player && !data.author) {
                    searchError.value = true;
                    return;
                }

                player.value = data.player;
                authorName.value = data.author;

                searchError.value = false;

                if (data.player && !data.author) {
                    window.location.href = '/player/' + data.player.id;
                    return;
                }
                if (data.author && !data.player) {
                    window.location.href = '/author/' + data.author;
                    return;
                }

                UIkit.modal('#search-modal').show();
            }

            return {
                input,
                player,
                authorName,
                loading,
                searchError,
                ajaxSearch
            };
        }
    }
</script>
