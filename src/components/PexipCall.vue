<!--
  - @copyright Copyright (c) 2023 Julien Veyssier <julien-nc@posteo.net>
  -
  - @author 2023 Julien Veyssier <julien-nc@posteo.net>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div class="pexip-call">
		<div v-if="deleted" class="call-info">
			{{ t('integration_pexip', 'This Pexip meeting has been deleted') }}
		</div>
		<div v-else-if="call.error" class="call-info">
			{{ t('integration_pexip', 'This Pexip meeting does not exist') }}
		</div>
		<div v-else class="call-info">
			<div class="header">
				<label v-if="!noDescriptionLabel">
					<PexipIcon :size="20" class="icon" /><span>{{ t('integration_pexip', 'Pexip meeting') }}:&nbsp;</span>
				</label>
				<span v-if="noLink"
					class="description">
					{{ call.description }}
				</span>
				<a v-else
					:href="call.link"
					target="_blank"
					class="description">
					{{ call.description }}
				</a>
			</div>
			<div class="content">
				<div class="creator">
					{{ t('integration_pexip', 'Creator') }}:&nbsp;
					<NcUserBubble
						:primary="true"
						:user="call.user_id"
						:display-name="creatorDisplayName" />
				</div>
				<div class="host-pin">
					<LockOutlineIcon v-if="call.pin" class="icon" :size="20" />
					<LockOpenVariantOutlineIcon v-else class="icon" :size="20" />
					{{ hostPinText }}
				</div>
				<div class="guest-allowed">
					<AccountIcon v-if="call.allow_guests" class="icon" :size="20" />
					<AccountOffIcon v-else class="icon" :size="20" />
					{{ guestAllowedText }}
				</div>
				<div v-if="call.allow_guests" class="guest-pin">
					<LockOutlineIcon v-if="call.guest_pin" class="icon" :size="20" />
					<LockOpenVariantOutlineIcon v-else class="icon" :size="20" />
					{{ guestPinText }}
				</div>
				<div v-if="call.allow_guests" class="guest-presentation">
					<CardOutlineIcon v-if="call.guests_can_present" class="icon" :size="20" />
					<CardOffOutlineIcon v-else class="icon" :size="20" />
					{{ guestCanPresentText }}
				</div>
			</div>
		</div>
		<div class="spacer" />
		<NcButton v-if="canDelete && !deleted"
			:title="t('integration_pexip', 'Delete meeting')"
			class="delete-button"
			@click.prevent.stop="onDelete">
			<template #icon>
				<NcLoadingIcon v-if="deleting" />
				<DeleteIcon v-else />
			</template>
		</NcButton>
	</div>
</template>

<script>
import LockOutlineIcon from 'vue-material-design-icons/LockOutline.vue'
import LockOpenVariantOutlineIcon from 'vue-material-design-icons/LockOpenVariantOutline.vue'
import CardOutlineIcon from 'vue-material-design-icons/CardOutline.vue'
import CardOffOutlineIcon from 'vue-material-design-icons/CardOffOutline.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'
import AccountIcon from 'vue-material-design-icons/Account.vue'
import AccountOffIcon from 'vue-material-design-icons/AccountOff.vue'

import PexipIcon from './icons/PexipIcon.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js'
import NcUserBubble from '@nextcloud/vue/dist/Components/NcUserBubble.js'

import { showError } from '@nextcloud/dialogs'
import { getCurrentUser } from '@nextcloud/auth'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'PexipCall',

	components: {
		PexipIcon,
		AccountIcon,
		AccountOffIcon,
		DeleteIcon,
		NcButton,
		NcLoadingIcon,
		CardOffOutlineIcon,
		CardOutlineIcon,
		NcUserBubble,
		LockOutlineIcon,
		LockOpenVariantOutlineIcon,
	},

	props: {
		call: {
			type: Object,
			required: true,
		},
		noLink: {
			type: Boolean,
			default: false,
		},
		noDescriptionLabel: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			canDelete: getCurrentUser().uid === this.call.user_id,
			deleting: false,
			deleted: false,
		}
	},

	computed: {
		guestAllowedText() {
			return this.call.allow_guests
				? t('integration_pexip', 'Guests allowed')
				: t('integration_pexip', 'No guest access')
		},
		guestCanPresentText() {
			return this.call.guests_can_present
				? t('integration_pexip', 'Guests can present')
				: t('integration_pexip', 'Guests cannot present')
		},
		hostPinText() {
			return this.call.pin
				? t('integration_pexip', 'Host pin')
				: t('integration_pexip', 'No host pin')
		},
		guestPinText() {
			return this.call.guest_pin
				? t('integration_pexip', 'Guests pin')
				: t('integration_pexip', 'No guests pin')
		},
		creatorDisplayName() {
			return getCurrentUser().uid === this.call.user_id
				? t('integration_pexip', 'You')
				: this.call.user_name
		},
	},

	mounted() {
	},

	methods: {
		onDelete() {
			this.deleting = true
			const url = generateUrl('/apps/integration_pexip/calls/{id}', { id: this.call.pexip_id })
			return axios.delete(url)
				.then((response) => {
					this.deleted = true
					this.$emit('deleted')
				})
				.catch((error) => {
					console.error('Pexip delete call error', error)
					showError(t('integration_pexip', 'Error deleting the Pexip call'))
				})
				.then(() => {
					this.deleting = false
				})
		},
	},
}
</script>

<style scoped lang="scss">
.pexip-call {
	white-space: normal;
	display: flex;
	align-items: center;

	.header {
		//display: flex;
		//align-items: center;
		> * {
			display: inline;
		}
		.icon {
			display: inline;
			position: relative;
			top: 4px;
			margin-right: 8px;
		}
	}
	a.hover {
		color: var(--color-primary);
	}
	.description {
		font-weight: bold;
	}
	.content {
		.host-pin,
		.guest-pin,
		.guest-presentation,
		.guest-allowed {
			display: flex;
			align-items: center;
			.icon {
				margin-right: 8px;
			}
		}
	}

	.delete-button {
		margin-left: 8px;
	}

	.spacer {
		flex-grow: 1;
	}
}
</style>
