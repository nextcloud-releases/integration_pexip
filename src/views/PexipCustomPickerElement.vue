<template>
	<div class="pexip-picker-content">
		<h2>
			{{ t('integration_pexip', 'Pexip meetings') }}
		</h2>
		<div class="call-list">
			<PexipCall v-for="call in calls"
				:key="call.pexip_id"
				:call="call"
				@click.native="$emit('submit', call.link)" />
		</div>
		<div class="creation-toggle">
			<NcButton class="toggle-button"
				type="tertiary"
				@click="showCreation = !showCreation">
				<template #icon>
					<component :is="showCreationIcon" />
				</template>
				{{ t('integration_pexip', 'Meeting creation') }}
			</NcButton>
		</div>
		<div v-show="showCreation" class="creation-form">
			<div class="line">
				<label for="desc">
					{{ t('integration_pexip', 'Description') }}
				</label>
				<div class="spacer" />
				<NcRichContenteditable
					id="desc"
					:value.sync="description"
					:maxlength="3000"
					:placeholder="t('integration_pexip', 'Short description, max 3000 characters')"
					:link-autocomplete="false" />
			</div>
			<div class="line">
				<label for="pin">
					{{ t('integration_pexip', 'Pin') }}
				</label>
				<div class="spacer" />
				<input
					id="pin"
					v-model="pin"
					type="text"
					:placeholder="t('integration_pexip', '1234abcd...')"
					maxlength="64">
			</div>
			<NcCheckboxRadioSwitch
				:checked.sync="allow_guests">
				{{ t('integration_pexip', 'Allow guests') }}
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch
				:checked.sync="guests_can_present"
				:disabled="!allow_guests">
				{{ t('integration_pexip', 'Guests can present') }}
			</NcCheckboxRadioSwitch>
			<div class="line">
				<label for="guest-pin">
					{{ t('integration_pexip', 'Guest pin') }}
				</label>
				<div class="spacer" />
				<input
					id="guest-pin"
					v-model="guest_pin"
					:disabled="!allow_guests"
					type="text"
					:placeholder="t('integration_pexip', '1234abcd...')"
					maxlength="64">
			</div>
			<div class="creation-footer">
				<NcButton class="create-button"
					type="primary"
					:disabled="!canCreate"
					@click="onCreate">
					<template #icon>
						<NcLoadingIcon v-if="creating" />
						<ArrowRightIcon v-else />
					</template>
					{{ t('integration_pexip', 'Create') }}
				</NcButton>
			</div>
		</div>
	</div>
</template>

<script>
import ArrowRightIcon from 'vue-material-design-icons/ArrowRight.vue'
import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue'
import ChevronDownIcon from 'vue-material-design-icons/ChevronDown.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js'
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import NcRichContenteditable from '@nextcloud/vue/dist/Components/NcRichContenteditable.js'

import PexipCall from '../components/PexipCall.vue'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'PexipCustomPickerElement',

	components: {
		PexipCall,
		NcButton,
		NcLoadingIcon,
		NcRichContenteditable,
		NcCheckboxRadioSwitch,
		ChevronRightIcon,
		ChevronDownIcon,
		ArrowRightIcon,
	},

	props: {
		providerId: {
			type: String,
			required: true,
		},
		accessible: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			creating: false,
			calls: [],
			showCreation: false,
			description: '',
			pin: '',
			allow_guests: true,
			guest_pin: '',
			guests_can_present: false,
		}
	},

	computed: {
		showCreationIcon() {
			return this.showCreation
				? ChevronDownIcon
				: ChevronRightIcon
		},
		canCreate() {
			return !!this.description
		},
	},

	watch: {
	},

	mounted() {
		this.getCalls()
	},

	methods: {
		getCalls() {
			const url = generateUrl('/apps/integration_pexip/calls')
			return axios.get(url)
				.then((response) => {
					this.calls = response.data
				})
				.catch((error) => {
					console.error('Pexip get calls error', error)
					showError(t('integration_pexip', 'Error getting the Pexip calls'))
				})
				.then(() => {
				})
		},
		onSubmit(url) {
			this.$emit('submit', url)
		},
		onCreate() {
			this.creating = true
			const params = {
				description: this.description,
				pin: this.pin,
				allowGuests: this.allow_guests,
				guestPin: this.guest_pin,
				guestsCanPresent: this.guests_can_present,
			}
			const url = generateUrl('/apps/integration_pexip/calls')
			return axios.post(url, params)
				.then((response) => {
					const link = response.data?.link
					this.onSubmit(link)
				})
				.catch((error) => {
					console.error('Pexip call creation error', error)
					showError(t('integration_pexip', 'Error creating the Pexip call'))
				})
				.then(() => {
					this.creating = false
				})
		},
	},
}
</script>

<style scoped lang="scss">
.pexip-picker-content {
	width: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	//padding: 16px;

	h2 {
		display: flex;
		align-items: center;
	}

	.spacer {
		flex-grow: 1;
	}

	.creation-toggle {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: end;
		margin-top: 12px;
		> * {
			margin-left: 4px;
		}
	}

	.creation-form {
		width: 100%;
		padding: 12px 0;
		display: flex;
		flex-direction: column;
		align-items: center;

		.line {
			display: flex;
			align-items: center;
			margin-top: 8px;
			width: 100%;

			input,
			select {
				width: 200px;
			}
		}

		input[type=number] {
			width: 80px;
			appearance: initial !important;
			-moz-appearance: initial !important;
			-webkit-appearance: initial !important;
		}

		#desc {
			width: 300px;
		}
	}
}
</style>
