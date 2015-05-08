<?php
	
/*
Plugin Name: Bing Ads UET
Plugin URI: https://github.com/jamiechung/BingAds-UET-WordPress
Description: Easily setup Bing Ads UET tag in your WordPress website.
Version: 1.0
Author: Jamie Chung
Author URI: http://github.com/jamiechung
License: MIT
License URI: http://opensource.org/licenses/MIT
*/

$bingads_uet_settings = get_option('bingads_uet_settings');

function is_bingads_uet_enabled() 
{
	global $bingads_uet_settings;
	return isset($bingads_uet_settings['bingads_uet_enabled']) && $bingads_uet_settings['bingads_uet_enabled'] == 'yes';
}

function bingads_uet_tag ($force = false)
{
	if ($force || is_bingads_uet_enabled())
	{
		global $bingads_uet_settings;
		return trim($bingads_uet_settings['bingads_uet_tag']);
	}
	
	return null;
}

define('BINGADS_UET_PAGE_SLUG', 'bingads-uet-configuration');

bingads_admin_notices();
function bingads_admin_notices ()
{
	global $pagenow;
	
	if (strlen(bingads_uet_tag(true)) == 0 )
	{
		function bingads_notices ()
		{
			?>
			
			<div class="updated fade">
				<p><strong>Bing Ads UET is almost ready.</strong> 
				You must <a href="admin.php?page=bingads-uet-configuration">setup your Universal Event Tracking (UET) tag</a> for it to work. 
				<a target="_blank" href="http://advertise.bingads.microsoft.com/en-us/uahelp-topic?querytype=keyword&query=ext53048&product=bing_ads&sku=">Learn more</a>
			</div>

			<?php
		}
		add_action('admin_notices', 'bingads_notices');
	}
}

function bingads_uet_configuration ()
{
	global $bingads_uet_settings
?>

<div class="wrap">
	<h2>Bing Ads UET Configurations</h2>
	<p>You can track conversions and other site activity for any of your campaigns by creating goals and adding the Bing Ads Universal Event Tracking tag to your WordPress site. <a target="_blank" href="http://advertise.bingads.microsoft.com/en-us/uahelp-topic?querytype=keyword&query=ext53048&product=bing_ads&sku=">Learn more</a></p>
	<form method="post" action="options.php">
	<?php
		settings_fields('bingads_uet_settings');
		do_settings_sections(BINGADS_UET_PAGE_SLUG);
	?>
	<p><input type="submit" class="button button-primary" value="Save Changes" /></p>
	</form>	
</div>

<?php	
}

add_action('admin_menu','add_bingads_uet_menu');
if (!function_exists('add_bingads_uet_menu'))
{
	function add_bingads_uet_menu ()
	{
		add_options_page('Bing Ads UET', 'Bing Ads UET', 'manage_options', BINGADS_UET_PAGE_SLUG, 'bingads_uet_configuration');
	}	
}

add_action('wp_footer', 'add_bingads_uet_scripts');
if (!function_exists('add_bingads_uet_scripts'))
{
	function add_bingads_uet_scripts ()
	{
		echo bingads_uet_tag();
	}
}


add_action('admin_init', 'bingads_uet_admin_init');
if (!function_exists('bingads_uet_admin_init'))
{
	function bingads_uet_admin_init ()
	{
		register_setting('bingads_uet_settings', 'bingads_uet_settings', 'bingads_uet_settings_sanitize');
		add_settings_section('bingads_uet_settings_section', __(''), false, BINGADS_UET_PAGE_SLUG);
		add_settings_field('bingads_uet_enabled', __('Enabled'), 'bingads_uet_setting_enabled', BINGADS_UET_PAGE_SLUG, 'bingads_uet_settings_section');
		add_settings_field('bingads_uet_tag', __('UET tag'), 'bingads_uet_setting_tag', BINGADS_UET_PAGE_SLUG, 'bingads_uet_settings_section');
	}	
}

if (!function_exists('bingads_uet_setting_tag'))
{
	function bingads_uet_setting_tag ()
	{
		?>
		
		<textarea name="bingads_uet_settings[bingads_uet_tag]" class="large-text code" rows="10" cols="10"><?php echo bingads_uet_tag(true); ?></textarea>
		
		<?php
	}
}

if (!function_exists('bingads_uet_setting_enabled'))
{
	function bingads_uet_setting_enabled ()
	{
		$checked = '';
		if (is_bingads_uet_enabled())
		{
			$checked = 'checked="checked" ';
		}
		
		?>
		
		<label for="bingads_uet_enabled">
			<input id="bingads_uet_enabled" name="bingads_uet_settings[bingads_uet_enabled]" type="checkbox" value="yes" <?php echo $checked; ?>/>
			Enable Bing Ads to track conversions and goals on your WordPress site.
		</label>

		<?php
	}
}

if (!function_exists('bingads_uet_settings_sanitize'))
{
	function bingads_uet_settings_sanitize ($settings)
	{
		return array (
			'bingads_uet_enabled' => $settings['bingads_uet_enabled'] == 'yes' ? 'yes' : '',
			'bingads_uet_tag' => ($settings['bingads_uet_tag']) 
		);
	}
}
