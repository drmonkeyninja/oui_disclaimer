<?php

$plugin['name'] = 'oui_disclaimer';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '1.3.1';
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

Easily display a warning message which can be hidden or replaced for a defined duration once accepted.

h2. Table of contents

* "Plugin requirements":#requirements
* "Installation":#installation
* "Tags":#tags
* "Exemples":#exemples
* "Styles":#styles
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
		<span class="oui_disclaimer_message">This website uses cookies</span>
		<a class="oui_disclaimer_decline" href="http://www.my-website.com/privacy-policy">Read more</a>
		<a class="oui_disclaimer_accept" href="?oui_disclaimer_accepted=1">Accept and continue</a>
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

h2(#styles). Styles

Defined classes:

* @oui_diclaimer@ – Applied to a div wrapper.
* @oui_disclaimer_label@ – Applied to the @labeltag@.
* @oui_disclaimer_content@ – Applied to the @wraptag@ value by default; overrided by the @class@ attribute.
* @oui_disclaimer_message@ – Applied to a span element.
* @oui_disclaimer_accept@ – Applied to the link; overrided by the @class@ attribute of @<txp:oui_diclaimer_accept />@.
* @oui_disclaimer_decline@ – Applied to the link when @<txp:oui_diclaimer />@ is used as a single tag.
* @oui_disclaimer_reset@ – Applied to the link; overrided by the @class@ attribute of @<txp:oui_diclaimer_reset />@.

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "Jukka Svahn":http://rahforum.biz/ "tip":http://textpattern.tips/setting-cookies-for-eu-legislation.

h2(#licence). Licence

This plugin is distributed under "GPLv3":http://www.gnu.org/licenses/gpl-3.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

function oui_disclaimer($atts, $thing=null) {
	global $oui_disclaimer_cookie, $oui_disclaimer_container_tag;

	extract(lAtts(array(
		'cookie'  => 'oui_disclaimer_hidden',
		'expires'  => '+1 week',
		'wraptag'  => 'p',
		'class'  => 'oui_disclaimer_content',
		'label'  => '',
		'labeltag'  => '',
		'message'  => null,
		'alt'  => null,
		'accept_url'  => '',
		'accept'  => gTxt('oui_disclaimer_accept'),
		'decline_url'  => '',
		'decline'  => gTxt('oui_disclaimer_alt'),
		'reset_url' => '',
		'reset' => null,
	),$atts));

	$oui_disclaimer_cookie = $cookie;
	$oui_disclaimer_container_tag = ($thing ? $thing : null);

	$oui_disclaimer_out = '<span class="oui_diclaimer_message">'.$message.'</span>'.($decline_url ? href($decline, $decline_url, ' class="oui_disclaimer_decline"') : '').href($accept, ($accept_url ? $accept_url : '').'?'.$cookie.'=1', ' class="oui_disclaimer_accept"');

	$oui_disclaimer_alt_out = ($alt ? '<span class="oui_diclaimer_message">'.$alt.'</span>' :'').($reset ? href($reset, ($reset_url ? $reset_url : '').'?oui_disclaimer_reset=1', ' class="oui_disclaimer_reset"') : '');

	// HTTP request contains '$cookie'?
	if (gps($cookie))
	{
		// Set the cookie
		setcookie($cookie, 1, strtotime(''.$expires.''), '/');

		// alt message or reset link set in a single tag
		if ($thing===null && ($alt!==null || $reset!==null))
		{			
			// Return the alternative disclaimer content
			$out = $oui_disclaimer_alt_out;
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		// alt message or reset link not set for a single tag
		else if ($thing===null)
		{			
			// Do nothing
		}

		// Container tag
		else
		{
			// txp:else magic	
			$result = ($cookie == 1) ? 1 : 0;
			$out = parse(EvalElse($thing, $result));
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

	}

	// Otherwise, cookie already exists?
	else if (cs($cookie))
	{

		// HTTP request contains oui_disclaimer_reset?
		if ($thing===null && gps('oui_disclaimer_reset'))
		{	
			// Delete the cookie
			setcookie($cookie, '', time() - 3600, '/');
	
			// Return the disclaimer
			$out = $oui_disclaimer_out;
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		// alt message or reset link set in a single tag
		else if ($thing===null && ($alt!==null || $reset!==null))
		{	
			// Return the alternative disclaimer content
			$out = $oui_disclaimer_alt_out;
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		// alt message or reset link not set for a single tag
		else if ($thing===null)
		{			

		}

		// Container tag and HTTP request contains oui_disclaimer_reset
		else if ($thing!==null && gps('oui_disclaimer_reset'))
		{	
			// Delete the cookie
			setcookie($cookie, '', time() - 3600, '/');
			// txp:else magic reverse	
			$result = ($cookie == 1) ? 0 : 1;
			$out = parse(EvalElse($thing, $result));
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';		
		}

		// Container tag otherwise
		else
		{
			// txp:else magic	
			$result = ($cookie == 1) ? 1 : 0;
			$out = parse(EvalElse($thing, $result));
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

	}

	// Otherwise…
	else
	{
		// Single tag with a message attribute set
		if ($thing===null && $message!==null)
		{
			// Return the alternative disclaimer content
			$out = $oui_disclaimer_out;
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

		// Single tag with a missing message attribute
		else if ($thing === null)
		{
			// Ask for a message attribute
			trigger_error("oui_disclaimer requires a message when used as a single tag");
			return;
		}

		// Container tag
		else
		{
			// txp:else magic	
			$result = ($cookie == $cookie) ? 1 : 0;
			$out = parse(EvalElse($thing, $result));
			return '<div class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>';
		}

	}

}

function oui_disclaimer_accept($atts) {
	global $oui_disclaimer_cookie, $oui_disclaimer_container_tag;

	extract(lAtts(array(
		'url'  => '',
		'class'  => 'oui_disclaimer_accept',
		'link'  => gTxt('oui_disclaimer_accept'),
	),$atts));

	// In a oui_disclaimer container tag?
	if ($oui_disclaimer_container_tag!==null)
	{
		// Return the link
		$out =  href($link, ($url ? $url : '').'?'.$oui_disclaimer_cookie.'=1', ' class="'.$class.'"');
		return $out;
	}

	// Not in a oui_disclaimer container tag?
	else
	{
		// Ask for oui_disclaimer container tag around
		trigger_error("oui_disclaimer_accept must be used in a oui_disclaimer container tag");
		return;
	}

}

function oui_disclaimer_reset($atts) {
	global $oui_disclaimer_container_tag;

	extract(lAtts(array(
		'url'  => '',
		'class'  => 'oui_disclaimer_reset',
		'link'  => gTxt('oui_disclaimer_reset'),
	),$atts));

	// In a oui_disclaimer container tag?
	if ($oui_disclaimer_container_tag!==null)
	{
		// Return the link
		$out =  href($link, ($url ? $url : '').'?oui_disclaimer_reset=1', ' class="'.$class.'"');
		return $out;
	}

	// Not in a oui_disclaimer container tag?
	else
	{
		// Ask for oui_disclaimer container tag around
		trigger_error("oui_disclaimer_reset must be used in a oui_disclaimer container tag");
		return;
	}

}	
# --- END PLUGIN CODE ---

?>