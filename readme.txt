=== Company Score Lookup ===
Contributors: scalaaios
Tags: company, lookup, search, revenue, employees, score, business, database
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Look up company data from 250M+ records. Revenue, employees, credit score — right from your WordPress site.

== Description ==

**Company Score Lookup** lets you embed a company search form on any WordPress page or post. Your visitors can instantly look up data on over 250 million companies worldwide.

**What data is available?**

* Company name
* Country
* Revenue
* Number of employees
* Credit / reliability score

**How it works**

The plugin connects to the [SCALA Score API](https://score.get-scala.com), one of the largest open company data aggregators in the world, and displays results in a clean, responsive table.

**Features**

* `[company_lookup]` shortcode for any post, page, or widget
* Native Gutenberg block — "Company Score Lookup"
* Configurable API endpoint (Settings > Score API)
* Vanilla JavaScript — no jQuery dependency, fast page loads
* Fully responsive design
* Accessible: ARIA labels, keyboard navigation, live status region
* Translation-ready (`.pot` file included)
* Color-coded score badges (green / yellow / red)
* Debounced live search as you type

== Installation ==

1. Upload the `score-company-lookup` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. (Optional) Go to **Settings > Score API** to configure the API endpoint.
4. Add `[company_lookup]` to any post or page, or use the Gutenberg block.

== Frequently Asked Questions ==

= Do I need an API key? =

No. The default SCALA Score API endpoint is free and requires no authentication.

= Can I change the number of results? =

Yes. Use the shortcode attribute: `[company_lookup limit="10"]`. In the Gutenberg block, use the sidebar Settings panel.

= Can I change the placeholder text? =

Yes: `[company_lookup placeholder="Find your company..."]`.

= Where does the data come from? =

The data is aggregated from over 30 official government registries across Europe and beyond, totaling 250M+ company records.

= Is this GDPR compliant? =

The plugin only transmits the search query to the API. No personal data is stored or shared. All company data is sourced from public business registries.

== Screenshots ==

1. Search form on the front end.
2. Results table with score badges.
3. Settings page under Settings > Score API.
4. Gutenberg block in the editor.

== Changelog ==

= 1.0.0 =
* Initial release.
* Shortcode `[company_lookup]` with search form and results table.
* Gutenberg block with live preview and sidebar settings.
* Settings page for API endpoint configuration.
* Vanilla JS AJAX search with debounce and AbortController.
* Responsive design with color-coded score badges.
* Full i18n support.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
