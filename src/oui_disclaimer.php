<?php

$plugin['name'] = 'oui_disclaimer';

$plugin['allow_html_help'] = 0;

$plugin['version'] = '1.4.4';
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
* "Examples":#examples
* "Styles":#styles
* "Author":#author
* "Licence":#licence

h2(#requirements). Plugin requirements

oui_disclaimer’s minimum requirements:

* Textpattern 4.5+

h2(#installation). Installation

Paste the content of the plugin file under *Admin > Plugins*, upload it and install.

h2(#tags). Tags

h3. <txp:oui_disclaimer />

Displays a conditional warning message.
Should be placed in every page, or in a form, depending on how it is used.

bc. <txp:oui_disclaimer />

h4. Attributes

If used as a single tag, @<txp:oui_disclaimer />@ should contains at least a @message@ attribute.

* @cookie="…"@ - _Default: oui_disclaimer_accepted - Name of the cookie set to hide the disclaimer for a defined duration.
* @expires="…"@ - _Default: +1 day_ - The duration assigned to the cookie ("strtotime":http://php.net/manual/fr/function.strtotime.php valid value).

* @wraptag="…"@ - _Default: p_ - The HTML tag used around the generated content.
* @class="…"@ – _Default: oui_disclaimer_content_ - The css class to apply to the HTML tag assigned to @wraptag@.

* @label="…"@ – _Default: unset_ - The label used to entitled the generated content.
* @labeltag="…"@ - _Default: unset_ - The HTML tag used around the value assigned to @label@.

* @message="…"@ – _Default: unset_ - The main message to display.
* @alt="…"@ – _Default: unset_ - The alternative message to display when the disclaimer is accepted.

* @accept_url="…"@ - _Default: unset (current page)_ - An url to redirect the user once the discliamer accepted.
* @accept_text="…"@ - _Default: unset_ - The value to assigned to the @accept@ link.

* @decline_url="…"@ – _Default: unset_ - An alternative url to the @accept@ link url.
* @decline_text="…"@ – _Default: unset_ - The value to assigned to the @decline@ link if a @decline_url@ is provided.

* @reset_url="…"@ - _Default: unset (current page)_ - An url to redirect the user once the cookie deleted.
* @reset_text="…"@ - _Default: unset_ - The value to assigned to the @reset@ link.

h3. <txp:oui_disclaimer_accept />

Displays a link to hide/replace the disclaimer message.

bc. <txp:oui_disclaimer_accept />

h4. Attributes

* @class="…"@ – _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.

* @url="…"@ - _Default: unset (current page)_ - An url to redirect the user once the discliamer accepted.
* @text="…"@ * - _Default: unset_ - The value to assigned to the @accept@ link.

h3. <txp:oui_disclaimer_reset />

Displays a link to delete the cookie set by the accept link and shows the disclaimer again.

bc. <txp:oui_disclaimer_reset />

h4. Attributes

* @class="…"@ – _Default: unset_ - The css class to apply to the HTML tag assigned to @wraptag@.

* @url="…"@ - _Default: unset (current page)_ - An url to redirect the user once the cookie deleted.
* @text="…"@ * - _Default: unset_ - The value to assigned to the @areset@ link.

h2(#examples). Examples

h3. Example 1: single tag use

bc. <txp:oui_disclaimer label="h3" labeltag="cookies-label" wraptag="p" class="cookies-warning" message="This website uses cookies" decline_text="Read more" decline_url="http://www.my-website.com/privacy-policy" accept_text="Accept and continue" />

Placed in your page(s), the code above will return the following HTML code if the _oui_disclaimer_accepted_ cookie is not already set or is expired.

bc.. <div id="oui_disclaimer_accepted" class="oui_disclaimer">
    <h3 class="cookies-label">Disclaimer</h3>
    <p class="cookies-warning">
        <span class="oui_disclaimer_message">This website uses cookies</span>
        <a class="oui_disclaimer_decline" href="http://www.my-website.com/privacy-policy">Read more</a>
        <a class="oui_disclaimer_accept" href="?oui_disclaimer_accepted=1">Accept and continue</a>
    </p>
</div>

h3. Example 2: container tag use

bc.. <txp:oui_disclaimer>
    This content is crazy!
    <txp:oui_disclaimer_accept text="I'm crazy!" />
<txp:else />
    Well, you are crazy…
    <txp:oui_disclaimer_reset text="I'm not that crazy!" />
</txp:oui_disclaimer>

h2(#styles). Styles

Defined id:

* The @cookie@ attribute value is applied to a div wrapper.

Defined classes:

* @oui_disclaimer@ – Applied to a div wrapper with the @cookie@ id.
* @oui_disclaimer_label@ – Applied to the @labeltag@.
* @oui_disclaimer_content@ – Applied to the @wraptag@ value by default; overrided by the @class@ attribute.
* @oui_disclaimer_message@ – Applied to a span element.
* @oui_disclaimer_accept@ – Applied to the link; overrided by the @class@ attribute of @<txp:oui_disclaimer_accept />@.
* @oui_disclaimer_decline@ – Applied to the link when @<txp:oui_disclaimer />@ is used as a single tag.
* @oui_disclaimer_reset@ – Applied to the link; overrided by the @class@ attribute of @<txp:oui_disclaimer_reset />@.

h2(#author). Author

"Nicolas Morand":http://www.nicolasmorand.com, from a "Jukka Svahn":http://rahforum.biz/ "tip":http://textpattern.tips/setting-cookies-for-eu-legislation.

h2(#licence). Licence

This plugin is distributed under "GPLv2":http://www.gnu.org/licenses/gpl-2.0.fr.html.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---
if (class_exists('\Textpattern\Tag\Registry')) {
    // Register Textpattern tags for TXP 4.6+.
    Txp::get('\Textpattern\Tag\Registry')
        ->register('oui_disclaimer')
        ->register('oui_disclaimer_accept')
        ->register('oui_disclaimer_reset');
}

function oui_disclaimer($atts, $thing=null) {
    global $oui_disclaimer_urlvar, $oui_disclaimer_cookie, $oui_disclaimer_expires;

    extract(lAtts(array(
        'cookie'      => 'oui_disclaimer_hidden',
        'expires'     => '+1 week',
        'wraptag'     => 'p',
        'class'       => 'oui_disclaimer_content',
        'label'       => '',
        'labeltag'    => '',
        'message'     => '',
        'alt'         => '',
        'accept_url'  => '',
        'accept_text' => '',
        'decline_url' => '',
        'decline_text'=> '',
        'reset_url'   => '',
        'reset_text'  => '',
    ),$atts));

    $oui_disclaimer_urlvar = $cookie;
    $oui_disclaimer_cookie = $cookie;
    $oui_disclaimer_expires = $expires;

    $visible = oui_disclaimer_visible();

    $content = '<span class="oui_disclaimer_message">'.$message.'</span>'.($decline_url ? href($decline_text, $decline_url, ' class="oui_disclaimer_decline"') : '').href($accept_text, ($accept_url ? $accept_url : '').'?'.$oui_disclaimer_urlvar.'=1', ' class="oui_disclaimer_accept"');

    $alt_content = ($alt ? '<span class="oui_disclaimer_message">'.$alt.'</span>' :'').($reset_text ? href($reset_text, ($reset_url ? $reset_url : '').'?oui_disclaimer_reset=1', ' class="oui_disclaimer_reset"') : '');

    if ($thing===null) {
        $out = ($visible) ? '<div id="'.$cookie.'" class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($content, $wraptag, $class) : $out).'</div>' : ($alt_content ? '<div id="'.$cookie.'" class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($alt_content, $wraptag, $class) : $out).'</div>' : '');
        return $out;
    } else {
        $result = ($visible) ? 1 : 0;
        $out = parse(EvalElse($thing, $result));
        return ($out ? '<div id="'.$cookie.'" class="oui_disclaimer">'.($label ? doLabel($label, $labeltag) : '').(($wraptag) ? doTag($out, $wraptag, $class) : $out).'</div>' : '');
    }

}

function oui_disclaimer_visible() {
    global $oui_disclaimer_urlvar, $oui_disclaimer_cookie, $oui_disclaimer_expires;

    if (gps($oui_disclaimer_urlvar)) {
        setcookie($oui_disclaimer_cookie, 1, strtotime(''.$oui_disclaimer_expires.''), '/');
        $oui_disclaimer_visible = false;
    }

    else if (cs($oui_disclaimer_cookie)) {

        if (gps('oui_disclaimer_reset')) {
            setcookie($oui_disclaimer_cookie, '', -1, '/');
            $oui_disclaimer_visible = true;
        } else {
            $oui_disclaimer_visible = false;
        }
    } else {
        $oui_disclaimer_visible = true;
    }

    return $oui_disclaimer_visible;

}

function oui_disclaimer_accept($atts) {
    global $oui_disclaimer_urlvar;

    extract(lAtts(array(
        'url'  => '',
        'class'  => 'oui_disclaimer_accept',
        'text'  => '',
    ),$atts));

    if (isset($atts['text'])) {
    	$out =  href($text, ($url ? $url : '').'?'.$oui_disclaimer_urlvar.'=1', ' class="'.$class.'"');
        return $out;
    } else {
        trigger_error("missing attribute; oui_disclaimer_accept requires a text attribute.");
        return;
    }

}

function oui_disclaimer_reset($atts) {

    extract(lAtts(array(
        'url'  => '',
        'class'  => 'oui_disclaimer_reset',
        'text'  => '',
    ),$atts));

    if (isset($atts['text'])) {
        $out =  href($text, ($url ? $url : '').'?oui_disclaimer_reset=1', ' class="'.$class.'"');
        return $out;
    } else {
        trigger_error("missing attribute: oui_disclaimer_reset requires a text attribute.");
        return;
    }

}
# --- END PLUGIN CODE ---

?>