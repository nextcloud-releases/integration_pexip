<template>
	<div class="pexip-picker-content">
		<h2>
			{{ t('integration_pexip', 'Pexip meetings') }}
		</h2>
		<NcLoadingIcon v-if="loadingCalls" :size="20" />
		<div v-else-if="calls.length > 0" class="call-list">
			<PexipCall v-for="call in calls"
				:key="call.pexip_id"
				:call="call"
				:no-link="true"
				:no-description-label="true"
				class="call"
				tabindex="0"
				@keydown.native.enter.prevent.stop="$emit('submit', call.link)"
				@click.native="$emit('submit', call.link)" />
		</div>
		<NcEmptyContent v-else
			:title="t('integration_pexip', 'No meetings found')">
			<template #icon>
				<PexipIcon />
			</template>
		</NcEmptyContent>
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
				<input
					id="pin"
					v-model="pin"
					type="text"
					:placeholder="t('integration_pexip', '1234abcd...')"
					maxlength="64">
			</div>
			<div class="line">
				<NcCheckboxRadioSwitch
					:checked.sync="allow_guests">
					{{ t('integration_pexip', 'Allow guests') }}
				</NcCheckboxRadioSwitch>
			</div>
			<div class="line">
				<label for="guest-pin">
					{{ t('integration_pexip', 'Guest pin') }}
				</label>
				<input
					id="guest-pin"
					v-model="guest_pin"
					:disabled="!allow_guests"
					type="text"
					:placeholder="t('integration_pexip', '1234abcd...')"
					maxlength="64">
			</div>
			<div class="line">
				<NcCheckboxRadioSwitch
					:checked.sync="guests_can_present"
					:disabled="!allow_guests">
					{{ t('integration_pexip', 'Guests can present') }}
				</NcCheckboxRadioSwitch>
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

import PexipIcon from '../components/icons/PexipIcon.vue'

import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js'
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import NcRichContenteditable from '@nextcloud/vue/dist/Components/NcRichContenteditable.js'
import NcEmptyContent from '@nextcloud/vue/dist/Components/NcEmptyContent.js'

import PexipCall from '../components/PexipCall.vue'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'PexipCustomPickerElement',

	components: {
		PexipIcon,
		PexipCall,
		NcButton,
		NcLoadingIcon,
		NcRichContenteditable,
		NcCheckboxRadioSwitch,
		NcEmptyContent,
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
			loadingCalls: false,
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
			this.loadingCalls = true
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
					this.loadingCalls = false
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

	.call-list {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		gap: 8px;
		.call {
			border: 2px solid var(--color-border);
			border-radius: var(--border-radius-large);
			padding: 8px;
			cursor: pointer;
			:deep(*) {
				cursor: pointer;
			}
			&:focus,
			&:focus-visible,
			&:hover {
				//background-color: var(--color-background-hover);
				border: 2px solid var(--color-primary);
				outline: none;
				box-shadow: none;
			}
		}
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

	.creation-footer {
		margin-top: 8px;
	}

	.creation-form {
		width: 100%;
		padding: 12px 0;
		display: flex;
		flex-direction: column;
		align-items: start;

		.line {
			display: flex;
			align-items: center;
			margin: 4px 0 0 14px;

			label {
				width: 150px;
			}

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
