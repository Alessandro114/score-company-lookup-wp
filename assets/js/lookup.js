/**
 * Company Score Lookup — Vanilla JS front-end.
 *
 * @package ScoreCompanyLookup
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var wrappers = document.querySelectorAll('.scl-wrapper');
		if (!wrappers.length) {
			return;
		}

		wrappers.forEach(function (wrapper) {
			initLookup(wrapper);
		});
	});

	/**
	 * Initialise a single lookup widget.
	 *
	 * @param {HTMLElement} wrapper The .scl-wrapper element.
	 */
	function initLookup(wrapper) {
		var input     = wrapper.querySelector('.scl-search-input');
		var btn       = wrapper.querySelector('.scl-search-btn');
		var statusEl  = wrapper.querySelector('.scl-status');
		var resultsEl = wrapper.querySelector('.scl-results');
		var limit     = parseInt(wrapper.getAttribute('data-limit'), 10) || 5;
		var debounce  = null;
		var controller = null;

		if (!input || !btn || !statusEl || !resultsEl) {
			return;
		}

		// Search on button click.
		btn.addEventListener('click', function () {
			doSearch();
		});

		// Search on Enter key.
		input.addEventListener('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				doSearch();
			}
		});

		// Debounced search as user types (300 ms).
		input.addEventListener('input', function () {
			clearTimeout(debounce);
			debounce = setTimeout(function () {
				if (input.value.trim().length >= 2) {
					doSearch();
				}
			}, 300);
		});

		/**
		 * Execute the AJAX search.
		 */
		function doSearch() {
			var query = input.value.trim();

			if (query.length < 2) {
				setStatus(sclData.i18n.minChars, '');
				return;
			}

			// Abort any in-flight request.
			if (controller) {
				controller.abort();
			}
			controller = typeof AbortController !== 'undefined' ? new AbortController() : null;

			setStatus(sclData.i18n.searching, 'loading');
			resultsEl.innerHTML = '';

			var url = sclData.ajaxUrl +
				'?action=scl_search' +
				'&nonce=' + encodeURIComponent(sclData.nonce) +
				'&query=' + encodeURIComponent(query) +
				'&limit=' + limit;

			var fetchOpts = {
				method: 'GET',
				credentials: 'same-origin',
			};
			if (controller) {
				fetchOpts.signal = controller.signal;
			}

			fetch(url, fetchOpts)
				.then(function (res) {
					return res.json();
				})
				.then(function (json) {
					controller = null;

					if (!json.success) {
						setStatus(json.data && json.data.message ? json.data.message : sclData.i18n.error, 'error');
						return;
					}

					var companies = json.data && json.data.companies ? json.data.companies : [];

					if (!companies.length) {
						setStatus(sclData.i18n.noResults, '');
						return;
					}

					setStatus('', '');
					renderTable(companies);
				})
				.catch(function (err) {
					if (err.name === 'AbortError') {
						return;
					}
					controller = null;
					setStatus(sclData.i18n.error, 'error');
				});
		}

		/**
		 * Set the status message.
		 *
		 * @param {string} msg  Text to display.
		 * @param {string} type 'loading', 'error', or ''.
		 */
		function setStatus(msg, type) {
			statusEl.textContent = msg;
			statusEl.className = 'scl-status';
			if (type === 'error') {
				statusEl.classList.add('scl-status--error');
			} else if (type === 'loading') {
				statusEl.classList.add('scl-status--loading');
			}
		}

		/**
		 * Render the results table.
		 *
		 * @param {Array} companies Array of company objects.
		 */
		function renderTable(companies) {
			var table = document.createElement('table');
			table.className = 'scl-results-table';

			// Header.
			var thead = document.createElement('thead');
			var headerRow = document.createElement('tr');
			var headers = [
				sclData.i18n.name,
				sclData.i18n.country,
				sclData.i18n.revenue,
				sclData.i18n.employees,
				sclData.i18n.score
			];
			headers.forEach(function (h) {
				var th = document.createElement('th');
				th.textContent = h;
				headerRow.appendChild(th);
			});
			thead.appendChild(headerRow);
			table.appendChild(thead);

			// Body.
			var tbody = document.createElement('tbody');
			companies.forEach(function (c) {
				var tr = document.createElement('tr');

				// Name.
				var tdName = document.createElement('td');
				tdName.textContent = c.name || '—';
				tr.appendChild(tdName);

				// Country.
				var tdCountry = document.createElement('td');
				tdCountry.textContent = c.country || '—';
				tr.appendChild(tdCountry);

				// Revenue.
				var tdRevenue = document.createElement('td');
				tdRevenue.textContent = formatRevenue(c.revenue);
				tr.appendChild(tdRevenue);

				// Employees.
				var tdEmployees = document.createElement('td');
				tdEmployees.textContent = c.employees != null ? formatNumber(c.employees) : '—';
				tr.appendChild(tdEmployees);

				// Score.
				var tdScore = document.createElement('td');
				tdScore.appendChild(createScoreBadge(c.score));
				tr.appendChild(tdScore);

				tbody.appendChild(tr);
			});
			table.appendChild(tbody);

			resultsEl.innerHTML = '';
			resultsEl.appendChild(table);
		}

		/**
		 * Format a revenue number.
		 *
		 * @param {number|null} val Revenue value.
		 * @return {string} Formatted string.
		 */
		function formatRevenue(val) {
			if (val == null) {
				return '—';
			}
			var num = parseFloat(val);
			if (isNaN(num)) {
				return '—';
			}
			if (num >= 1e9) {
				return '€' + (num / 1e9).toFixed(1) + 'B';
			}
			if (num >= 1e6) {
				return '€' + (num / 1e6).toFixed(1) + 'M';
			}
			if (num >= 1e3) {
				return '€' + (num / 1e3).toFixed(0) + 'K';
			}
			return '€' + num.toLocaleString();
		}

		/**
		 * Format a plain number with locale separators.
		 *
		 * @param {number} val Number.
		 * @return {string}
		 */
		function formatNumber(val) {
			var n = parseInt(val, 10);
			if (isNaN(n)) {
				return '—';
			}
			return n.toLocaleString();
		}

		/**
		 * Create a score badge element.
		 *
		 * @param {number|null} score Score value.
		 * @return {HTMLElement}
		 */
		function createScoreBadge(score) {
			var span = document.createElement('span');
			span.className = 'scl-score-badge';

			if (score == null || isNaN(parseFloat(score))) {
				span.classList.add('scl-score-badge--unknown');
				span.textContent = '—';
				return span;
			}

			var s = parseFloat(score);
			span.textContent = s.toFixed(0);

			if (s >= 70) {
				span.classList.add('scl-score-badge--high');
			} else if (s >= 40) {
				span.classList.add('scl-score-badge--medium');
			} else {
				span.classList.add('scl-score-badge--low');
			}

			return span;
		}
	}
})();
