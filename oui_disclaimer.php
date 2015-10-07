<?php

$plugin['name'] = 'oui_disclaimer';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '1.0.0';
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
oui_disclaimer_alt => Read more
oui_disclaimer_accept => accept and continue
#@language fr-fr
oui_disclaimer_alt => En savoir plus
oui_disclaimer_accept => Accepter et continuer
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
Should be placed in your each page.

bc. <txp:oui_disclaimer />

h3(#attributes). Attributes

If used as a single tag, @<txp:oui_disclaimer />@ should contains at least a @message@ or a @form@ attribute; @form@ overrides @message@. 

* @cookie=""@ - _Default: oui_disclaimer_accepted_ - Name of the cookie set to hide the disclaimer for a defined duration.
* @expires="…"@ - _Default: +1 week_ - The duration assigned to the cookie ("strtotime":http://php.net/manual/fr/function.strtotime.php valid value). 

* @form=""@ - _Default: unset_ - The form used for the disclaimer content.

* @wraptag="…"@ - _Default: div_ - The HTML tag used around the generated content.
* @class="…"@ – _Default: oui_disclaimer_ - The css class to apply to the HTML tag assigned to @wraptag@. 

* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @label_tag="…"@ - _Default: h1_ - The HTML tag used around the value assigned to @label@.
* @label_class="…"@ – _Default: oui_disclaimer_label_ - The css class to apply to the HTML tag assigned to @label_tag@. 

* @message="…"@ – _Default: unset_ - The main message you want to display.
* @message_tag="…"@ - _Default: span_ - The HTML tag used around the value assigned to @message@.
* @message_class="…"@ – _Default: oui_disclaimer_message_ - The css class to apply to the HTML tag assigned to @message_tag@.

* @alt_url="…"@ – _Default: unset_ - An alternative url to the @accept@ link used by the @alt@ link. 
* @alt="…"@ – _Default: Read more_ - The text value to assigned to the @alt@ link.
* @alt_class="…"@ – _Default: oui_disclaimer_alt_ - The css class to apply to the HTML link tag @a@.

* @accept="…"@ - _Default: accept and continue_ - The text value to assigned to the @accept@ link.
* @accept_class="…"@ – _Default: oui_disclaimer_accept_ - The css class to apply to the HTML link tag @a@.

h3. <txp:oui_disclaimer_accept />

Displays a link to accept/hide the disclaimer message.
Should be used only in the form used as the @form@ attribute of @<txp:oui_disclaimer />@.

bc. <txp:oui_disclaimer_accept />

h2(#exemples). Exemples

h3. Exemple 1: simple use 

bc. <txp:oui_disclaimer wraptag="div" class="cookies-warning" message="This website uses cookies" message_class="cookies-warning-message" alt_url="http://www.my-website.com/privacy-policy" alt="Read more"  alt_class="button" accept_class="button" />

Placed in your page(s), the code above will return the following HTML code if the _oui_disclaimer_accepted_ cookie is not already set or is expired.

bc.. <div class="cookies-warning">
	<span class="">This website uses cookies</span>
	<a href="http://www.my-website.com/privacy-policy">Read more</a>
	<a href="?oui_disclaimer_accepted=1">Read more</a>
</div>

h3. Exemple 2: form use 

bc. <txp:oui_disclaimer form="cookies-warning" />

Placed in your page(s), the code above will return the content of the form named _disclaimer_. This form must contains a @<txp:oui_disclaimer_accept />@ tag.

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "Jukka Svahn":http://rahforum.biz/ "tip":http://textpattern.tips/setting-cookies-for-eu-legislation.

h2(#licence). Licence

This plugin is distributed under "GPLv3":http://www.gnu.org/licenses/gpl-3.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

function oui_disclaimer($atts) {
	global $oui_disclaimer_cookie, $oui_disclaimer_form;

	extract(lAtts(array(
		'cookie'  => 'oui_disclaimer_hidden',
		'expires'  => '+1 week',
		'form'  => null,
		'wraptag'  => 'div',
		'class'  => 'oui_disclaimer',
		'label'  => '',
		'label_tag'  => 'h1',
		'label_class'  => 'oui_disclaimer_label',
		'message'  => null,
		'message_tag'  => 'span',
		'message_class'  => 'oui_disclaimer_message',
		'accept'  => gTxt('oui_disclaimer_accept'),
		'accept_class'  => 'oui_disclaimer_accept',
		'alt_url'  => '',
		'alt'  => gTxt('oui_disclaimer_alt'),
		'alt_class' => 'oui_disclaimer_alt',
	),$atts));

		$oui_disclaimer_cookie = $cookie;
		$oui_disclaimer_form = $form;

	// HTTP GET or POST request contains 'oui_accept_disclaimer_warning'?
	if (gps($cookie))
	{
		// Set a cookie
		setcookie($cookie, 1, strtotime(''.$expires.''), '/');
	}

	// Otherwise, cookie already exists?
	else if (cs($cookie))
	{
		// Do nothing
	}

	// Otherwise…
	else
	{

		if ($form!==null) 
		{		
			return output_form(array("form"=>$form));			
		}
		// Displays warning
		else if ($message!==null)
		{
			return 
			'<'.$wraptag.' class="'.$class.'">'.n.
				($label ? '<'.$label_tag.' class="'.$label_class.'">'.$label.'</'.$label_tag.'>' : '').n.
				'<'.$message_tag.' class="'.$message_class.'">'.$message.'.</'.$message_tag.'>'.n.	
				($alt_url ? '<a rel="internal" class="'.$alt_class.'" href="'.$alt_url.'">'.$alt.'</a>' : '').n.
				'<a rel="internal" class="'.$accept_class.'" href="?'.$cookie.'=1">'.$accept.'</a>'.n.
			'</'.$wraptag.'>'.n;
		}

		else 
		{
			trigger_error("oui_disclaimer requires a message or a form attribute");
			return;
		}
	}
			
}

function oui_disclaimer_accept($atts) {
	global $oui_disclaimer_cookie, $oui_disclaimer_form;

	extract(lAtts(array(
		'wraptag' => '',
		'class'  => 'oui_disclaimer_accept',
		'link'  => gTxt('oui_disclaimer_accept'),
		'link_class'  => 'oui_disclaimer_accept_link',
	),$atts));

	if ($oui_disclaimer_form!==null) 
	{
		return 
		($wraptag ? '<'.$wraptag.' class="'.$class.'">' : '').n.
			'<a rel="internal" class="'.$link_class.'" href="?'.$oui_disclaimer_cookie.'=1">'.$link.'</a>'.n.
		($wraptag ? '</'.$wraptag.'>' : '').n;	
	}
	else 
	{
		trigger_error("oui_disclaimer_accept requires a oui_disclaimer form attribute");
		return;
	}
		
}	
# --- END PLUGIN CODE ---

?>