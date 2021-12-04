<template>
    <div class="list-group list-group-flush">
        <ul>
            <li v-for="s in songs" :key="s.id">
                {{ song.title }} {{ s.song }}
            </li>
        </ul>
    </div>
</template>
<script>

import axios from "axios";
import 'regenerator-runtime/runtime';
import Cookies from 'js-cookie';

var csrfCookie = Cookies.get('csrftoken'); 
console.log('csrf cookie: ', csrfCookie); // set to undefined

axios.defaults.xsrfCookieName = 'csrftoken';
axios.defaults.xsrfHeaderName = 'X-CSRFToken';
export default {
    name: 'VeritiezSongs',
    data() { 
        return {
            songs: []
        };
    },
    async mounted() {
        try {
            var result = await axios({
                method: "POST",
                xstfCookieName: 'csrftoken',
                xsrfHeaderName: 'X-CSRFToken',
                headers: {
                            'X-CSRFToken': csrfCookie,
                        }, 
                url: "http://localhost:8000/graphql",
                data: {
                    query: `
                    {
                        allAudio {
                          id,
                          title,
                          song
                        }
                      }
                    `
                }
            });
            this.songs = result.data.data.allAudio;
        } catch (error) {
            console.error(error);
        }
    }
};
</script>