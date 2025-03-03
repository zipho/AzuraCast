<template>
    <div>
        <b-card no-body>
            <b-card-header header-bg-variant="primary-dark">
                <h2 class="card-title" key="lang_title" v-translate>Remote Relays</h2>
            </b-card-header>

            <info-card>
                <p class="card-text">
                    <translate key="lang_card_info">Remote relays let you work with broadcasting software outside this server. Any relay you include here will be included in your station's statistics. You can also broadcast from this server to remote relays.</translate>
                </p>
            </info-card>

            <b-card-body body-class="card-padding-sm">
                <b-button variant="outline-primary" @click.prevent="doCreate">
                    <icon icon="add"></icon>
                    <translate key="lang_add_btn">Add Remote Relay</translate>
                </b-button>
            </b-card-body>

            <data-table ref="datatable" id="station_remotes" :show-toolbar="false" :fields="fields" :api-url="listUrl">
                <template #cell(name)="row">
                    <h5 class="m-0">
                        <a :href="row.item.url" target="_blank">{{ row.item.display_name }}</a>
                    </h5>
                </template>
                <template #cell(autodj)="row">
                    <template v-if="row.item.enable_autodj">
                        <translate key="lang_autodj_enabled">Enabled</translate>
                        -
                        {{ row.item.autodj_bitrate }}kbps {{ row.item.autodj_format|upper }}
                    </template>
                    <template v-else>
                        <translate key="lang_autodj_disabled">Disabled</translate>
                    </template>
                </template>
                <template #cell(actions)="row">
                    <b-button-group size="sm" v-if="row.item.is_editable">
                        <b-button size="sm" variant="primary" @click.prevent="doEdit(row.item.links.self)">
                            <translate key="lang_btn_edit">Edit</translate>
                        </b-button>
                        <b-button size="sm" variant="danger" @click.prevent="doDelete(row.item.links.self)">
                            <translate key="lang_btn_delete">Delete</translate>
                        </b-button>
                    </b-button-group>
                </template>
            </data-table>
        </b-card>

        <remote-edit-modal ref="editModal" :create-url="listUrl" :enable-advanced-features="enableAdvancedFeatures"
                           @relist="relist"></remote-edit-modal>
    </div>
</template>

<script>
import DataTable from '../Common/DataTable';
import axios from 'axios';
import EditModal from './Mounts/EditModal';
import Icon from '../Common/Icon';
import InfoCard from '../Common/InfoCard';
import handleAxiosError from '../Function/handleAxiosError';
import RemoteEditModal from "./Remotes/EditModal";

export default {
    name: 'StationMounts',
    components: {RemoteEditModal, InfoCard, Icon, EditModal, DataTable},
    props: {
        listUrl: String,
        enableAdvancedFeatures: Boolean
    },
    data() {
        return {
            fields: [
                {key: 'name', isRowHeader: true, label: this.$gettext('Name'), sortable: false},
                {key: 'autodj', label: this.$gettext('AutoDJ'), sortable: false},
                {key: 'actions', label: this.$gettext('Actions'), sortable: false, class: 'shrink'}
            ]
        };
    },
    filters: {
        upper(data) {
            let upper = [];
            data.split(' ').forEach((word) => {
                upper.push(word.toUpperCase());
            });
            return upper.join(' ');
        }
    },
    methods: {
        relist() {
            this.$refs.datatable.refresh();
        },
        doCreate() {
            this.$refs.editModal.create();
        },
        doEdit(url) {
            this.$refs.editModal.edit(url);
        },
        doDelete(url) {
            let buttonText = this.$gettext('Delete');
            let buttonConfirmText = this.$gettext('Delete Remote Relay?');

            Swal.fire({
                title: buttonConfirmText,
                confirmButtonText: buttonText,
                confirmButtonColor: '#e64942',
                showCancelButton: true,
                focusCancel: true
            }).then((result) => {
                if (result.value) {
                    axios.delete(url).then((resp) => {
                        notify('<b>' + resp.data.message + '</b>', 'success');

                        this.relist();
                    }).catch((err) => {
                        handleAxiosError(err);
                    });
                }
            });
        }
    }
};
</script>
