<template>
    <div class="card text-white bg-primary mb-3">
         <div class="card-header">
            <h5 class="card-title text-white">
                <translate key="lang_vsounds_title">vSounds Music</translate>
            </h5>
        </div>
        <div class="card-body">
            <div class="list-group-item">
                <div class="text-center text-white" style="border-style: dotted">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-primary" data-v-91a4f05e="">
                        <span aria-label="Upload icon" role="img" class="material-design-icon upload-icon" data-v-91a4f05e="">
                            <svg fill="currentColor" width="34" height="34" viewBox="0 0 24 24" class="material-design-icon__svg">
                                <path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"><title>Upload icon</title></path>
                            </svg>
                        </span>
                    </div>
                    <div
                        draggable="true"
                        @dragover.prevent="tts"
                        @drop.prevent="ttrs"
                        class="bg-light py-5 list-group-item list-group-item-action flex-column align-items-start"
                        >
                    {{ dt }}
                    </div>
                </div>
            </div>
            <div
                v-for="(item, index) in fileList"
                :key="index"
                class="col list-group-item control-group justify-content-center mb-3"
                >

                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">{{ item.name }} </h6>
                        <small class="text-muted">{{ item.type }} | {{ item.size | sizeType }}</small>
                    </div>
                    <audio controls :src="srcs[index].file" class="col"></audio>
                </li>
                <button @click="del(index)" class="w-33 btn btn-lg btn-danger">Remove</button>
                <button @click="add(index)" class="w-33 btn btn-lg btn-success">Queue > Playlist 1</button>
                <button @click="add(index)" class="w-33 btn btn-lg btn-success">Queue > Playlist 2</button>
            </div>
            
            <div class="list-group list-group-flush" v-if="songs.length > 0">
                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start"
                    v-for="(rowFile, rowIndex) in songs" v-bind:class="{ active: rowIndex == fileIndex }"
                    v-on:click.prevent="play({ fileIndex: rowIndex })">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-0">{{
                                rowFile.title ? rowFile.title : lang_unknown_title
                            }}</h5>
                        <small class="pt-1">{{ rowFile.song.length | prettifyTime }}</small>
                    </div>                
                    <p class="mb-0">- by {{ rowFile.artist ? rowFile.artist : lang_unknown_artist }}</p>
                </a>
            </div>
        </div>
    </div>
</template>
<script>

import axios from "axios";
import _ from 'lodash';
import 'regenerator-runtime/runtime';
import Cookies from 'js-cookie';
import track from './Track.js';
import Icon from '../../Common/Icon';


var csrfCookie = Cookies.get('csrftoken'); 
console.log('csrf cookie: ', csrfCookie); // set to undefined

axios.defaults.xsrfCookieName = 'csrftoken';
axios.defaults.xsrfHeaderName = 'X-CSRFToken';
export default {
    //name: 'VeritiezSongs',
    components: { Icon },
    extends: track,
    data() { 
        return {
            'fileIndex': -1,
            songs: [],
            dt: 'DRAG AND DROP A SONG HERE',
            fileList: [],
            srcs:[],
            isImage:false,
            isAudio:false,
            isVideo:false
        };
    },
    async mounted() {
        try {
            var result = await axios({
                method: "POST",
                xstfCookieName: 'csrftoken',
                xsrfHeaderName: 'X-CSRFToken',
                headers: {
                            'X-CSRFToken': csrfCookie
                        }, 
                url: "http://localhost:8000/graphql/",
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
            //this.addNewFiles(this.songs)
        } catch (error) {
            console.error(error);
        };
        let vm = this;
        window.addEventListener("dragdrop", this.testfunc, false);
        
        document.addEventListener("dragover", function() {
            vm.dt = "Drag here to upload your songs";
        });
    },
    filters: {
        prettifyTime (time) {
            if (typeof time === 'undefined') {
                return 'N/A';
            }

            var hours = parseInt(time / 3600);
            time %= 3600;
            var minutes = parseInt(time / 60);
            var seconds = parseInt(time % 60);

            if (minutes < 10) {
                minutes = '0' + minutes;
            }
            if (seconds < 10) {
                seconds = '0' + seconds;
            }

            if (hours > 0) {
                return hours + ':' + minutes + ':' + seconds;
            } else {
                return minutes + ':' + seconds;
            }
        },

        sizeType(val) {
            let kbs = val / 1024;
            let mbs = 0;
            let gbs = 0;
            if (kbs >= 1024) {
                mbs = kbs / 1024;
            }
            if (mbs >= 1024) {
                gbs = mbs / 1024;
                return gbs.toFixed(2) + "GB";
            } else if (mbs >= 1) {
                return mbs.toFixed(2) + "MB";
            } else {
                return kbs.toFixed(2) + "KB";
            }
        }
    },
    computed: {
        lang_unknown_title () {
            return this.$gettext('Unknown Title');
        },
        lang_unknown_artist () {
            return this.$gettext('Unknown Artist');
        }
    },
    methods: {
        cue () {
            this.resumeStream();
            this.$root.$emit('new-cue', (this.passThrough) ? 'off' : this.id);
        },

        onNewCue (new_cue) {
            this.passThrough = (new_cue === this.id);
        },

        setMixGain (new_value) {
            if (this.id === 'playlist_1') {
                this.mixGainObj.gain.value = 1.0 - new_value;
            } else {
                this.mixGainObj.gain.value = new_value;
            }
        },

        play (options) {
            
            this.resumeStream();

            if (this.paused) {
                this.togglePause();
                return;
            }

            this.stop();

            if (!(this.file = this.selectFile(options))) {
                return;
            }

            this.prepare();
            return this.getStream().createFileSource(this.file, this, (source) => {
                var ref1;
                this.source = source;
                this.source.connect(this.destination);
                if (this.source.duration != null) {
                    this.duration = this.source.duration();
                } else {
                    if (((ref1 = this.file.audio) != null ? ref1.length : void 0) != null) {
                        this.duration = parseFloat(this.file.audio.length);
                    }
                }

                this.source.play(this.file);

                this.$root.$emit('metadata-update', {
                    title: this.file.metadata.title,
                    artist: this.file.metadata.artist
                });

                this.playing = true;
                this.paused = false;
            });
        },

        selectFile (options = {}) {
            if (this.songs.length === 0) {
                return;
            }

            if (options.fileIndex) {
                this.fileIndex = options.fileIndex;
            } else {
                this.fileIndex += options.backward ? -1 : 1;
                if (this.fileIndex < 0) {
                    this.fileIndex = this.songs.length - 1;
                }

                if (this.fileIndex >= this.songs.length) {
                    if (options.isAutoPlay && !this.loop) {
                        this.fileIndex = -1;
                        return;
                    }

                    if (this.fileIndex < 0) {
                        this.fileIndex = this.songs.length - 1;
                    } else {
                        this.fileIndex = 0;
                    }
                }
            }
             return this.songs[this.fileIndex];
        },
        
        previous () {
            if (!this.playing) {
                return;
            }

            return this.play({
                backward: true
            });
        },

        next () {
            if (!this.playing) {
                return;
            }

            return this.play();
        },

        onEnd () {
            this.stop();

            if (this.playThrough) {
                return this.play({
                    isAutoPlay: true
                });
            }
        },

        doSeek (e) {
            if (this.isSeeking) {
                this.seekPosition = e.target.value;
                this.seek(this.seekPosition / 100);
            }
        },

        /******** Audio Upload */
        readFile(files){
            var vm = this
            for (var index = 0; index < files.length; index++) {
                var file = {isImage: false, isAudio: false, isVideo: false, file: ''}
                var reader = new FileReader();
                var type = files[index].type.substr(0,5);
                
                if(type=="image"){
                file.isImage = true;
                file.isAudio =false;
                file.isVideo = false;
                }else if(type=="audio"){
                file.isImage = false;
                file.isAudio =true;
                file.isVideo = false;
                }else if(type=="video"){
                file.isImage = false;
                file.isAudio = false;
                file.isVideo = true;
                }else {
                    alert("Not a picture/video/audio")
                }
                
                reader.onload = function(event) {
                    console.log(event.target.result)
                    file.file = event.target.result;
                    vm.srcs.push(file);
                }
                reader.readAsDataURL(files[index]);
            }
        },
        testfunc(event) {
            alert("dragdrop!");
            event.stopPropagation();
            event.preventDefault();
        },
        del(index) {
        this.fileList.splice(index, 1);
            if (this.fileList.length === 0) {
                this.dt = "";
            }
        },
        tts(e) {
            this.dt = "Drag here to upload files";
        },
        ttrs(e) {
            let datas =  e.target.files || e.dataTransfer.files;
            
            var i
            for (i = 0; i < datas.length; i++) {
                this.fileList.push(datas[i])
            }
            
            this.readFile(datas)
            this.queueNewFiles(datas)
            
            e.stopPropagation();
            e.preventDefault();
            
            this.dt = "Upload is complete, you can continue to upload";
        },
         queueNewFiles (newFiles) {
            console.log(newFiles)
            _.each(newFiles, (file) => {
                file.readTaglibMetadata((data) => {
                    this.files.push({
                        file: file,
                        audio: data.audio,
                        metadata: data.metadata || { title: '', artist: '' }
                    });
                });
            });
        },
    }
};
</script>