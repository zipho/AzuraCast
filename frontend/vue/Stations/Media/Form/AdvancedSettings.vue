<template>
    <b-tab :title="langTitle">
        <b-form-group>
            <b-row>
                <b-form-group class="col-md-6" label-for="edit_form_song_length">
                    <template #label>
                        <translate key="lang_song_length">Song Length</translate>
                    </template>

                    <b-form-input id="edit_form_song_length" readonly :value="songLength"></b-form-input>
                </b-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_amplify" :field="form.amplify">
                    <template #label>
                        <translate key="lang_form_amplify">Amplify: Amplification (dB)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_amplify_desc">The volume in decibels to amplify the track with. Leave blank to use the system default.</translate>
                    </template>
                </b-wrapped-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_fade_overlap" :field="form.fade_overlap">
                    <template #label>
                        <translate key="lang_form_fade_overlap">Custom Fading: Overlap Time (seconds)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_fade_overlap_desc">The time that this song should overlap its surrounding songs when fading. Leave blank to use the system default.</translate>
                    </template>
                </b-wrapped-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_fade_in" :field="form.fade_in">
                    <template #label>
                        <translate key="lang_form_fade_in">Custom Fading: Fade-In Time (seconds)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_fade_in_desc">The time period that the song should fade in. Leave blank to use the system default.</translate>
                    </template>
                </b-wrapped-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_fade_out" :field="form.fade_out">
                    <template #label>
                        <translate key="lang_form_fade_out">Custom Fading: Fade-Out Time (seconds)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_fade_out_desc">The time period that the song should fade out. Leave blank to use the system default.</translate>
                    </template>
                </b-wrapped-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_cue_in" :field="form.cue_in">
                    <template #label>
                        <translate key="lang_form_cue_in">Custom Cues: Cue-In Point (seconds)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_cue_in_desc">Seconds from the start of the song that the AutoDJ should start playing.</translate>
                    </template>
                </b-wrapped-form-group>

                <b-wrapped-form-group class="col-md-6" id="edit_form_cue_out" :field="form.cue_out">
                    <template #label>
                        <translate key="lang_form_cue_out">Custom Cues: Cue-Out Point (seconds)</translate>
                    </template>
                    <template #description>
                        <translate key="lang_form_cue_out_desc">Seconds from the start of the song that the AutoDJ should stop playing.</translate>
                    </template>
                </b-wrapped-form-group>
            </b-row>
        </b-form-group>
    </b-tab>
</template>

<script>
import BWrappedFormGroup from "../../../Form/BWrappedFormGroup";

export default {
    name: 'MediaFormAdvancedSettings',
    components: {BWrappedFormGroup},
    props: {
        form: Object,
        songLength: String
    },
    computed: {
        langTitle() {
            return this.$gettext('Advanced');
        }
    }
};
</script>
