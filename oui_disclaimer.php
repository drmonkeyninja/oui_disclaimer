<?php

$plugin['name'] = 'oui_disclaimer';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '1.3.0';
$plugin['author'] = 'Nicolas Morand';
$plugin['author_uri'] = 'http://www.nicolasmorand.com';
$plugin['description'] = 'PHP powered disclaimer with cookie setting';

$plugin['order'] = 5;

$plugin['type'] = 0;

// Plugin 'flags' signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use.
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

// $plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;
$plugin['flags'] = '0';

$plugin['textpack'] = <<< EOT
#@public
#@language en-gb
oui_disclaimer_accept => accept and continue 
oui_disclaimer_reset => Reset 
#@language fr-fr
oui_disclaimer_accept => Accepter et continuer 
oui_disclaimer_reset => Reset 
EOT;

if (!defined('txpinterface'))
	@include_once('zem_tpl.php');

if (0) {

?>
# --- BEGIN PLUGIN HELP ---

h1. oui_disclaimer

Easily display a warning message which can be hidden for a defined duration once accepted.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Tags":#tags
* "Exemples":#exemples
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_disclaimer’s minimum requirements:

* Textpattern 4.5+

h2(#installation). Installation

Paste the content of the plugin file under the *Admin > Plugins*, upload it and install.

h2(#tags). Tags

h3. <txp:oui_disclaimer />

Displays a conditional warning message.
Should be placed in your each page, or in a form, depending on how it is used.

bc. <txp:oui_disclaimer />

h4. Attributes

If used as a single tag, @<txp:oui_disclaimer />@ should contains at least a @message@ attribute. 

* @cookie=""@ - _Default: oui_disclaimer_accepted_ - Name of the cookie set to hide the disclaimer for a defined duration.
* @expires="…"@ - _Default: +1 week_ - The duration assigned to the cookie ("strtotime":http://php.net/manual/fr/function.strtotime.php valid value). 

* @wraptag="…"@ - _Default: div_ - The HTML tag used around the generated content.
* @class="…"@ – _Default: oui_disclaimer_ - The css class to apply to the HTML tag assigned to @wraptag@. 

* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: h1_ - The HTML tag used around the value assigned to @label@.

* @message="…"@ – _Default: unset_ - The main message to display.
* @alt="…"@ – _Default: unset_ - The alternative message to display when the disclaimer is accepted.

* @accept_url="…"@ - _Default: unset (current page)_ - An url to redirect the user once the discliamer accepted.
* @accept="…"@ - _Default: accept and continue_ - The text value to assigned to the @accept@ link.

* @decline_url="…"@ – _Default: unset_ - An alternative url to the @accept@ link used by the @alt@ link. 
* @decline="…"@ – _Default: Read more_ - The text value to assigned to the @alt@ link if a @decline_url@ is provided.

h3. <txp:oui_disclaimer_accept />

Displays a link to accept/hide the disclaimer message.

bc. <txp:oui_disclaimer_accept />

h4. Attributes

* @wraptag="…"@ - _Default: unset_ - The HTML tag used around the generated link.
* @class="…"@ – _Default: unset__ - The css class to apply to the HTML tag assigned to @wraptag@.

h3. <txp:oui_disclaimer_reset />

Displays a link to delete the cookie set by the accept link.

bc. <txp:oui_disclaimer_accept />

h4. Attributes

* @wraptag="…"@ - _Default: unset_ - The HTML tag used around the generated link.
* @class="…"@ – _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.

h2(#exemples). Exemples

h3. Exemple 1: single tag use 

bc. <txp:oui_disclaimer label="h3" labeltag="cookies-label" wraptag="p" class="cookies-warning" message="This website uses cookies" decline_url="http://www.my-website.com/privacy-policy" decline="Read more" />

Placed in your page(s), the code above will return the following HTML code if the _oui_disclaimer_accepted_ cookie is not already set or is expired.

bc.. <div class="oui_disclaimer">
	<h3 class="cookies-label">Disclaimer</h3>
	<p class="cookies-warning">
		<span class="">This website uses cookies</span>
		<a href="http://www.my-website.com/privacy-policy">Read more</a>
		<a href="?oui_disclaimer_accepted=1">Read more</a>
	</p>
</div>

h3. Exemple 2: container tag use 

bc.. <txp:oui_disclaimer>
	This content is crazy!
	<txp:oui_disclaimer_accept />
<txp:else />
	Well, you are crazy…
	<txp:oui_disclaimer_reset />
</txp:oui_disclaimer>

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "Jukka Svahn":http://rahforum.biz/ "tip":http://textpattern.tips/setting-cookies-for-eu-legislation.

h2(#licence). Licence

This plugin is distributed under "GPLv3":http://www.gnu.org/licenses/gpl-3.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

function oui_disclaimer($atts, $thing=null) {
	global $oui_disclaimer_cookie, $oui_disclaimer_container;

	extract(lAtts(array(
		'cookie'  => 'oui_disclaimer_hidden',
		'expires'  => '+1 week',
		'wraptag'  => 'p',
		'class'  => 'oui_disclaimer_content',
		'label'  => '',
		'labeltag'  => '',
		'message'  => null,
		'alt'  => '',
		'accept_url'  => '',
		'accept'  => gTxt('oui_disclaimer_accept'),
		'decline_url'  => '',
		'decline'  => gTxt('oui_disclaimer_alt'),
		'reset_url' => '',
		'reset' => '',
	),$atts));

	$oui_disclaimer_cookie = $cookie;
	$oui_disclaimer_container = $thing;

	// HTTP GET or POST request contains '$cookie'?
	if (gps($cookie))
	{
		// Set a cookie
		setcookie($cookie, 1, strtotime(''.$expires.''), '/');

		if ($thing===null)
		{
				$out = '<span class="oui_diclaimer_message">'.$alt.'</span>'.($reset ? href($reset, ($reset_url ? $reset_url : '').'?oui_disclaimer_reset=1', ' class="oui_disclaimer_reset"') : '');

				return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		else
		{
			$result = ($cookie == 1) ? 1 : 0;
			return parse(EvalElse($thing, $result));
		}

	}

	// Otherwise, cookie already exists?
	else if (cs($cookie))
	{

		if ($thing===null && gps('oui_disclaimer_reset'))
		{	
			// Delete the cookie
			setcookie($cookie, '', time() - 3600, '/');
	
			$out = '<span class="oui_diclaimer_message">'.$message.'</span>'.($decline_url ? href($decline, $decline_url, ' class="oui_disclaimer_decline"') : '').href($accept, ($accept_url ? $accept_url : '').'?'.$cookie.'=1', ' class="oui_disclaimer_accept"');

			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		else if ($thing===null)
		{			
			$out = '<span class="oui_diclaimer_message">'.$alt.'</span>'.($reset ? href($reset, ($reset_url ? $reset_url : '').'?oui_disclaimer_reset=1', ' class="oui_disclaimer_reset"') : '');

			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		else if ($thing!==null && gps('oui_disclaimer_reset'))
		{	
			// Delete the cookie
			setcookie($cookie, '', time() - 3600, '/');

			$result = ($cookie == 1) ? 0 : 1;
			return parse(EvalElse($thing, $result));		
		}

		else
		{
			$result = ($cookie == 1) ? 1 : 0;
			return parse(EvalElse($thing, $result));
		}

	}

	// Otherwise…
	else
	{

		if ($thing===null && $message!==null)
		{
			$out = '<span class="oui_diclaimer_message">'.$message.'</span>'.($decline_url ? href($decline, $decline_url, ' class="oui_disclaimer_decline"') : '').href($accept, ($accept_url ? $accept_url : '').'?'.$cookie.'=1', ' class="oui_disclaimer_accept"');

			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		else if ($thing === null)
		{
				trigger_error("oui_disclaimer requires a message when used as a single tag");
				return;
		}

		else
		{
			$result = ($cookie == $cookie) ? 1 : 0;
			return parse(EvalElse($thing, $result));
		}

	}

}

function oui_disclaimer_accept($atts) {
	global $oui_disclaimer_cookie, $oui_disclaimer_container;

	extract(lAtts(array(
		'url'  => '',
		'wraptag' => '',
		'class'  => '',
		'link'  => gTxt('oui_disclaimer_accept'),
	),$atts));

	if ($oui_disclaimer_container!==null)
	{
		$out =  href($link, ($url ? $url : '').'?'.$oui_disclaimer_cookie.'=1', ' class="oui_disclaimer_accept"');
		return ($wraptag ? doWrap($out, $wraptag, $class) : $out);
	}

	else
	{
		trigger_error("oui_disclaimer_accept must be used in a oui_disclaimer container tag");
		return;
	}

}

function oui_disclaimer_reset($atts) {
	global $oui_disclaimer_cookie, $oui_disclaimer_container;

	extract(lAtts(array(
		'url'  => '',
		'wraptag' => '',
		'class'  => '',
		'link'  => gTxt('oui_disclaimer_reset'),
	),$atts));

	if ($oui_disclaimer_container!==null)
	{
		$out =  href($link, ($url ? $url : '').'?oui_disclaimer_reset=1', ' class="oui_disclaimer_reset"');
		return ($wraptag ? doWrap($out, $wraptag, $class) : $out);
	}

	else
	{
		trigger_error("oui_disclaimer_reset must be used in a oui_disclaimer container tag");
		return;
	}

}	
# --- END PLUGIN CODE ---

?>