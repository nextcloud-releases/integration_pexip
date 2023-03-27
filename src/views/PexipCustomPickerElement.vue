<template>
	<div class="pexip-picker-content">
		<h2>
			{{ t('integration_pexip', 'Pexip meetings') }}
		</h2>
		<div class="call-list">
			<PexipCall v-for="call in calls"
				:key="call.pexip_id"
				:call="call" />
		</div>
		<div class="creation-toggle">
			<NcButton class="toggle-button"
				type="tertiary"
				@click="showCreation = !showCreation">
				<template #icon>
					<component :is="showCreationIcon" />
				</template>
				{{ t('integration_pexip', 'Create a meeting') }}
			</NcButton>
		</div>
		<div v-show="showCreation" class="creation-form">
			<div class="line">
				<label for="number">
					{{ t('integration_pexip', 'Description') }}
				</label>
				<div class="spacer" />
				<input
					id="desc"
					v-model="description"
					type="text">
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
			loading: false,
			calls: [],
			showCreation: false,
			description: '',
		}
	},

	computed: {
		showCreationIcon() {
			return this.showCreation
				? ChevronDownIcon
				: ChevronRightIcon
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
			return axios.post(url)
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
			this.loading = true
			const params = {
				description: this.description,
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
					this.loading = false
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
		.line {
			display: flex;
			align-items: center;
			margin-top: 8px;

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
	}
}
</style>
