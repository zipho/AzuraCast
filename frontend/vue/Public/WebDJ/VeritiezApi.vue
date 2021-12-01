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
import 'regenerator-runtime/runtime'
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
                url: "http://127.0.0.1:8000/graphql",
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
            console.log("results: Zipho");
            this.songs = result.data.data.allAudio;
        } catch (error) {
            console.error(error);
        }
    }
};
</script>