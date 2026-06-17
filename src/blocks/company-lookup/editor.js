/**
 * Company Score Lookup — Gutenberg Block (editor).
 *
 * @package ScoreCompanyLookup
 */
(function (blocks, element, blockEditor, components, i18n) {
	'use strict';

	var el            = element.createElement;
	var Fragment      = element.Fragment;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody     = components.PanelBody;
	var TextControl   = components.TextControl;
	var RangeControl  = components.RangeControl;
	var __            = i18n.__;

	blocks.registerBlockType('score-company-lookup/lookup', {
		title: __('Company Score Lookup', 'score-company-lookup'),
		description: __('Search 250M+ company records for revenue, employees, and credit score.', 'score-company-lookup'),
		icon: 'search',
		category: 'widgets',
		keywords: [
			__('company', 'score-company-lookup'),
			__('score', 'score-company-lookup'),
			__('lookup', 'score-company-lookup'),
			__('revenue', 'score-company-lookup'),
			__('SCALA', 'score-company-lookup'),
		],
		attributes: {
			placeholder: {
				type: 'string',
				default: '',
			},
			limit: {
				type: 'number',
				default: 5,
			},
		},
		supports: {
			html: false,
			align: ['wide', 'full'],
		},

		/**
		 * Editor view.
		 */
		edit: function (props) {
			var attrs = props.attributes;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{
							title: __('Settings', 'score-company-lookup'),
							initialOpen: true,
						},
						el(TextControl, {
							label: __('Placeholder text', 'score-company-lookup'),
							value: attrs.placeholder,
							onChange: function (val) {
								props.setAttributes({ placeholder: val });
							},
							placeholder: __('Search for a company...', 'score-company-lookup'),
						}),
						el(RangeControl, {
							label: __('Results limit', 'score-company-lookup'),
							value: attrs.limit,
							onChange: function (val) {
								props.setAttributes({ limit: val });
							},
							min: 1,
							max: 50,
						})
					)
				),
				el(
					'div',
					{ className: 'scl-wrapper scl-editor-preview' },
					el(
						'div',
						{ className: 'scl-form' },
						el(
							'div',
							{ className: 'scl-input-group' },
							el('input', {
								type: 'search',
								className: 'scl-search-input',
								placeholder: attrs.placeholder || __('Search for a company...', 'score-company-lookup'),
								disabled: true,
							}),
							el(
								'button',
								{ className: 'scl-search-btn', disabled: true },
								el(
									'svg',
									{
										xmlns: 'http://www.w3.org/2000/svg',
										width: 18,
										height: 18,
										viewBox: '0 0 24 24',
										fill: 'none',
										stroke: 'currentColor',
										strokeWidth: 2,
										strokeLinecap: 'round',
										strokeLinejoin: 'round',
									},
									el('circle', { cx: 11, cy: 11, r: 8 }),
									el('line', { x1: 21, y1: 21, x2: 16.65, y2: 16.65 })
								),
								el('span', null, __('Search', 'score-company-lookup'))
							)
						)
					),
					el(
						'p',
						{
							style: {
								textAlign: 'center',
								color: '#9ca3af',
								fontSize: '13px',
								marginTop: '1em',
							},
						},
						__('Company Score Lookup — results will appear here on the front end.', 'score-company-lookup')
					)
				)
			);
		},

		/**
		 * No save — server-side rendered.
		 */
		save: function () {
			return null;
		},
	});
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
