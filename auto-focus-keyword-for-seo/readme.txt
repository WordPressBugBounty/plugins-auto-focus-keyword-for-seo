=== Auto Focus Keyword for SEO ===
Contributors: the-rock, pagup, freemius
Tags: focus keyword, yoast seo, rank math, seo, keyword automation
Requires at least: 4.1
Requires PHP: 7.4
Tested up to: 7.0
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically fill missing Yoast SEO or Rank Math focus keywords from post titles. Batch sync, exclusions, and Pro auto-sync.

== Description ==

**Auto Focus Keyword for SEO** is a WordPress plugin that fills **missing** focus keyword fields from the **post title**.

It is designed for sites that already use **Yoast SEO** or **Rank Math** and want a faster way to populate focus keywords across existing content.

Official documentation: [https://autolinksforseo.com/focus-keyword](https://autolinksforseo.com/focus-keyword)

= Quick product facts =

- **Product type:** WordPress SEO workflow plugin
- **Main action:** fills missing focus keyword fields from post titles
- **Works in:** WordPress admin / backend
- **Supported SEO plugins:** Yoast SEO, Rank Math
- **Default workflow:** FETCH then SYNC
- **Overwrite existing keywords:** no
- **Keyword research / AI generation:** no
- **WooCommerce product pages:** Pro
- **Continuous auto-sync for new content:** Pro

= What Auto Focus Keyword does =

Auto Focus Keyword scans selected post types and finds published items that do not yet have a focus keyword in the supported SEO plugin.

When you run **SYNC**, it writes the **post title** into the supported focus keyword field.

This plugin is useful when you want to:

- populate missing focus keywords in bulk
- save time on large WordPress sites
- standardize a starting point for SEO workflows
- prepare content for downstream systems that rely on focus keywords

= What Auto Focus Keyword does not do =

Auto Focus Keyword does **not** do the following:

- it does **not** generate AI keyword suggestions
- it does **not** perform keyword research
- it does **not** estimate search volume or difficulty
- it does **not** rewrite titles, content, or meta descriptions
- it does **not** overwrite existing focus keywords during batch sync
- it does **not** support All in One SEO (AIOSEO) in the current version
- it does **not** guarantee that a page title is the ideal target query in every case

This distinction matters: Auto Focus Keyword is a **bulk field population tool**, not a full keyword research platform.

= Why this plugin exists =

Many WordPress sites never filled focus keyword fields consistently, especially on large content libraries and WooCommerce catalogs.

Auto Focus Keyword gives you a fast way to assign a practical default value so your editorial and SEO workflow starts from something structured instead of something empty.

It also fits naturally into a broader SEO pipeline. If your site uses focus keywords as a signal for internal linking or other rule-based SEO automation, this plugin helps you establish that signal first.

Pipeline overview: [https://autolinksforseo.com/pipeline](https://autolinksforseo.com/pipeline)

= Important SEO note =

Using the post title as the focus keyword is a **practical starting point**, not a universal SEO truth.

For product pages and large catalogs, the title is often close to the intended target query. For editorial pages, service pages, or branded content, the best focus keyword may still need manual refinement.

Auto Focus Keyword is best understood as a **workflow accelerator**, not as a substitute for SEO judgment on every page.

= Free edition =

The free edition is built for manual batch work.

Included in Free:

- select post types to scan
- FETCH missing items
- SYNC missing focus keywords in bulk
- blacklist / exclusions
- activity log
- plugin settings cleanup option on deactivation

The free edition is intended to help you retrofit existing content quickly.

= Pro edition =

The Pro edition extends the workflow for sites that publish continuously.

Included in Pro:

- continuous auto-sync for new content
- WooCommerce product page support
- per-page disable control in the sidebar

See plans and documentation: [https://autolinksforseo.com/pricing](https://autolinksforseo.com/pricing)

= Compatibility =

Auto Focus Keyword supports:

- **Yoast SEO**
- **Rank Math**

It does not create its own focus keyword system. It writes to the supported SEO plugin field already present on the site.

If neither Yoast SEO nor Rank Math is active, there is no supported focus keyword field to fill.

= How it works =

1. Select the post types you want to process
2. Exclude any pages or URLs you do not want to touch
3. Run **FETCH** to identify published items with a missing focus keyword
4. Run **SYNC** to fill the missing field with the post title
5. Review the log

Pro can keep new content synchronized automatically after publication.

= Use cases =

Auto Focus Keyword is especially useful for:

- large blog archives
- WooCommerce stores
- editorial teams that inherited unoptimized content
- agencies standardizing an SEO workflow before deeper manual review

= Links =

- [Official documentation](https://autolinksforseo.com/focus-keyword)
- [Pricing and plans](https://autolinksforseo.com/pricing)
- [Pipeline overview](https://autolinksforseo.com/pipeline)
- [Full changelog](https://autolinksforseo.com/guides/changelog-afk)

= About the publisher =


Auto Focus Keyword for SEO is developed by [Pagup](https://pagup.com/), a digital readability firm based in Quebec, Canada.


A clear focus keyword on every page is a foundational layer of digital readability. Without it, search engines and AI systems cannot determine what a page is about or how it relates to the rest of your content. This plugin automates that layer so that your editorial structure remains coherent as your site grows.


Auto Focus Keyword is part of a broader practice that includes [semantic content architecture](https://pagup.com/en/services/semantic-content-architecture/) and [interpretive SEO](https://pagup.com/en/glossary/interpretive-seo/).


= Part of the Pagup ecosystem =


* [pagup.com](https://pagup.com/) — Digital readability firm. Diagnostic, semantic architecture, AI governance.
* [gautierdorval.com](https://gautierdorval.com/) — Doctrine, canonical definitions, interpretive governance research.
* [interpretive-governance.org](https://interpretive-governance.org/) — Formal versioned standard for interpretive governance.


== Installation ==

= Installing from WordPress =

1. Go to Plugins > Add New in WordPress admin
2. Search for "Auto Focus Keyword for SEO"
3. Click "Install Now"
4. Click "Activate"
5. Open "Auto Focus Keyword" in the admin menu

= Installing manually =

1. Unzip all files to the `/wp-content/plugins/auto-focus-keyword-for-seo` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Open "Auto Focus Keyword" in the admin menu

= After activation =

1. Select the post types you want to process
2. Add exclusions if needed
3. Click **FETCH**
4. Click **SYNC**

== Frequently Asked Questions ==

= What is Auto Focus Keyword for SEO? =

It is a WordPress plugin that fills **missing** Yoast SEO or Rank Math focus keyword fields from post titles.

= Which SEO plugins are supported? =

Auto Focus Keyword supports **Yoast SEO** and **Rank Math**.

= Does it support All in One SEO (AIOSEO)? =

No. The current version supports **Yoast SEO** and **Rank Math** only.

= Does it work without Yoast SEO or Rank Math? =

No. The plugin needs a supported focus keyword field to write into.

= Does it overwrite existing focus keywords? =

No. The plugin is designed to fill **missing** focus keyword fields during the batch process.

= What value does the plugin write? =

It writes the **post title** into the supported focus keyword field.

= Does it change the frontend of my site? =

No. Auto Focus Keyword works in the admin workflow and updates the supported SEO plugin field. It does not rewrite your post title or body content.

= Is the post title always the perfect focus keyword? =

No. The post title is a practical default, not always the best final keyword. Manual refinement may still be preferable on important pages.

= Can I exclude pages from the process? =

Yes. The plugin includes an exclusion / blacklist workflow. Pro also adds per-page disable control.

= Does the free version support WooCommerce product pages? =

WooCommerce product pages require Pro.

= Does the plugin support continuous auto-sync? =

Continuous auto-sync for newly published content is a Pro feature.

= Does the plugin clean up after deactivation? =

The plugin includes a **remove settings** option for plugin settings. Generated focus keywords are stored in the supported SEO plugin field and are not automatically removed just by deactivating the plugin. If you want to remove generated values, use the sync log workflow before uninstalling.

= Can I use this with your internal linking plugin? =

Yes. Auto Focus Keyword is designed to work naturally with the broader focus keyword to internal linking workflow documented on autolinksforseo.com.

= Where can I find the full documentation? =

Documentation is available at [https://autolinksforseo.com/focus-keyword](https://autolinksforseo.com/focus-keyword).

= Who develops Auto Focus Keyword? =

Auto Focus Keyword is developed by [Pagup](https://pagup.com/), a digital readability firm based in Quebec, Canada. Pagup specializes in semantic architecture, interpretive SEO, and AI governance.

= Why do focus keywords matter for AI systems? =

AI systems do not just read your page content. They try to understand what each page is about and how it fits within your site's structure. A missing or generic focus keyword creates ambiguity. The system cannot determine whether a page is a service page, a blog post, a product description, or documentation. This ambiguity leads to [semantic compression](https://pagup.com/en/glossary/semantic-compression/) — the loss of meaningful distinctions when AI systems summarize your organization.

= What is digital readability? =

Digital readability is the capacity of a website to be correctly understood by all four reading layers: humans, search engines, generative AI systems, and autonomous agents. Learn more at [pagup.com](https://pagup.com/en/glossary/digital-readability/).


== Screenshots ==

1. Auto Focus Keyword dashboard
2. FETCH and SYNC workflow
3. Post type selection and exclusions
4. Activity log

== Changelog ==

= 1.0.5 =
* Update Freemius SDK to 2.13.1.

= 1.0.4 =
* FIX: Namespace issue with certain servers
* IMPROVE: Updated Freemius SDK to v2.12.0

= 1.0.3 =
* FIX: Freemius SDK security fix

= 1.0.2 =
* FIX: Critical error because of array sort function

Older release notes: [https://autolinksforseo.com/guides/changelog-afk](https://autolinksforseo.com/guides/changelog-afk)
