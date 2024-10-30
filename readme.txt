=== HTTPS force ===
Contributors: sitzz
Donate link: https://www.paypal.me/sitzz
Tags: https, mixed content, force
Requires at least: 2.7.0
Tested up to: 5.1
Stable tag: 1.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A fix for mixed content on secure sites, as all links and references are converted to HTTPS. Also redirects all non-secure calls to the secure equivalent.
Works in Front- and Backend!

Loosely based on the HTTP/HTTPS Remover by condacore.

== Description ==
A fix for mixed content on secure sites, as all links and references to local content are converted to HTTPS.
Can redirect all requests for HTTP to HTTPS.

Also works for the administration area!

= Main Features =
- Makes every Plugin compatible with https<br>
- Fixes Google Fonts issues<br>
- Can redirect to secure (https) site

= Example =

Without Plugin:
`"http://domain.com/script1.js"`
`"https://domain.com/script2.js"` 

With Plugin:
`"https://domain.com/script1.js"`
`"https://domain.com/script2.js"` 


= Note =

The Plugin does not force https on external links. This might be added later, but this plugin cannot determine if the remote site can handle HTTPS requests or not..

**Other Cache Plugin:** <br>
Please purge/clear cache for the changes to take effect!


= More =
[Feel free to visit our Website](http://sitzz.dk/)


== Installation ==
1. Upload `https-force` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin from Admin > Plugins menu
3. Go to Admin > Settings > General
4. Enable replacing insecure elements and/or redirect to secure site

== Screenshots ==


== Changelog ==
= 1.1.0 (18/01/19) =
* Added settings to WordPress general tab, to enable replacing insecure elements and/or redirecting
* In doing so, minimum required version is now WP 2.7.0
* Important for those updating: Default value for both settings is DISABLED. You need to enable in Admin > Settings > General
= 1.0.3 (17/01/19) =
* Tested and confirmed compatible with WP 5.0.3
* Added check if requested page is an administrative interface page for certain pattern
= 1.0.2 (17/07/18) =
* Tested and confirmed compatible with WP 4.9.7
= 1.0.1 (16/01/18) =
* Tested and confirmed compatible with WP 4.9.1
= 1.0.0 (17/07/17) =
* Initial release
