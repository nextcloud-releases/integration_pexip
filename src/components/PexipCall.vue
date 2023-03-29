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
		<div class="header">
			<label v-if="!noDescriptionLabel">
				<PexipIcon :size="20" class="icon" />
				{{ t('integration_pexip', 'Pexip meeting') }}:&nbsp;
			</label>
			<span v-if="noLink"
				class="description">
				{{ call.description }}
			</span>
			<a v-else
				:href="call.link"
				class="description">
				{{ call.description }}
			</a>
		</div>
		<div class="content">
			<div class="guest-allowed">
				<AccountIcon v-if="call.allow_guests" :size="20" />
				<AccountOffIcon v-else :size="20" />
				{{ guestAllowedText }}
			</div>
		</div>
	</div>
</template>

<script>
import AccountIcon from 'vue-material-design-icons/Account.vue'
import AccountOffIcon from 'vue-material-design-icons/AccountOff.vue'

import PexipIcon from './icons/PexipIcon.vue'

export default {
	name: 'PexipCall',

	components: {
		PexipIcon,
		AccountIcon,
		AccountOffIcon,
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
		}
	},

	computed: {
		guestAllowedText() {
			return this.call.allow_guests
				? t('integration_pexip', 'Guests allowed')
				: t('integration_pexip', 'No guest access')
		},
	},

	mounted() {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
.pexip-call {
	white-space: normal;
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
		}
	}
	a.hover {
		color: var(--color-primary);
	}
	.description {
		font-weight: bold;
	}
	.content {
		.guest-allowed {
			display: flex;
			align-items: center;
		}
	}
}
</style>
