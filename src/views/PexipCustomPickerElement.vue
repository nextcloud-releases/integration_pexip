<template>
	<div class="pexip-picker-content">
		<h2>
			{{ t('integration_pexip', 'Pexip calls') }}
		</h2>
		<!-- list of my calls -->
		<div class="footer">
			<NcButton class="advanced-button"
				type="tertiary"
				@click="showAdvanced = !showAdvanced">
				<template #icon>
					<component :is="showAdvancedIcon" />
				</template>
				{{ t('integration_pexip', 'Advanced options') }}
			</NcButton>
			<NcButton
				type="primary"
				:disabled="loading || !query"
				@click="onCreate">
				{{ t('integration_pexip', 'Generate') }}
				<template #icon>
					<NcLoadingIcon v-if="loading" />
					<ArrowRightIcon v-else />
				</template>
			</NcButton>
		</div>
		<div v-show="showAdvanced" class="advanced">
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

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'PexipCustomPickerElement',

	components: {
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
			showAdvanced: false,
			description: '',
		}
	},

	computed: {
		showAdvancedIcon() {
			return this.showAdvanced
				? ChevronDownIcon
				: ChevronRightIcon
		},
	},

	watch: {
	},

	mounted() {
	},

	methods: {
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

	.attribution {
		color: var(--color-text-maxcontrast);
		padding-bottom: 8px;
	}

	.input-wrapper {
		display: flex;
		align-items: center;
		width: 100%;
	}

	.footer {
		width: 100%;
		display: flex;
		align-items: center;
		justify-content: end;
		margin-top: 12px;
		> * {
			margin-left: 4px;
		}
	}

	.advanced {
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
