<?php

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @link       https://chattymango.com/creative-tag-cloud/
* @since      0.1.0
* @package    creative_tag_cloud
* @subpackage creative_tag_cloud/admin
* @author     Christoph Amthor @ Chatty Mango
*/
class creative_tag_cloud_Admin {

	/**
	* The ID of this plugin.
	*
	* @since    1.0
	* @access   private
	* @var      string    $creative_tag_cloud    The ID of this plugin.
	*/
	private $creative_tag_cloud;

	/**
	* The version of this plugin.
	*
	* @since    1.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0
	* @param      string    $creative_tag_cloud       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $creative_tag_cloud, $version ) {

		$this->creative_tag_cloud = $creative_tag_cloud;
		$this->version = $version;

	}


	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0
	*/
	public function enqueue_scripts() {


		/**
		*	We need the accordion on the Settings page.
		*/
		if ( ! empty( $_GET['page'] ) && 'creative-tag-cloud-settings' == $_GET['page'] ) {

			wp_enqueue_script( 'jquery' );

			wp_enqueue_script( 'jquery-ui-core' );

			wp_enqueue_script( 'jquery-ui-accordion' );

		}

	}


	/**
	* Register all of the hooks related to the admin area functionality
	* of the plugin.
	*
	* @since    1.0
	* @access   public
	*/
	public function register_options_page() {

		/**
		*	Register the Settings page.
		*/
		add_options_page( 'Creative Tag Cloud', 'Creative Tag Cloud', 'edit_pages', 'creative-tag-cloud-settings', array( $this, 'settings_page' ) );


	}


	/**
	* Displays the admin page
	*
	*
	* @param void
	* @return html
	*/
	public function settings_page() {

		$html = '<div class="wrap">
		<h2>Creative Tag Cloud</h2>';

		$active_tab = 0;

		if ( ! empty( $_GET['active-tab'] ) ) {

			$active_tab = sanitize_title( $_GET['active-tab'] );

		} else {

			$active_tab = 'tagclouds';

		}

		$tabs = array(
			'tagclouds' 	=> __( 'Tag Clouds', 'creative-tag-cloud' ),
			'support' 		=> __( 'Support', 'creative-tag-cloud' ),
			'about' 			=> __( 'About', 'creative-tag-cloud' ),
		);

		$html .= '<h2 class="nav-tab-wrapper">';

		foreach ( $tabs as $slug => $label ) {
			$html .= '<a href="options-general.php?page=creative-tag-cloud-settings&amp;active-tab=' . $slug . '" class="nav-tab ';

			if ( $slug == $active_tab) {
				$html .= 'nav-tab-active';
			}

			$html .= '">' . $label .'</a>';
		}
		$html .= '</h2>';


		$html .= '<p>&nbsp;</p>';

		if ( 'about' == $active_tab ) {
			$html .=   '<h2>' . __( 'About', 'creative-tag-cloud' ) . '</h2>';
			$html .=
			'<h4>Creative Tag Cloud, Version: ' . CREATIVE_TAG_CLOUD_VERSION . '</h4>
			<ul>
			<li>Developed by <a href="https://chattymango.com/?pk_campaign=ctc&pk_kwd=dashboard" target="_blank">Christoph Amthor</a></li>
			</ul>';

			$html .= '
			<h3>' . __( 'License', 'creative-tag-cloud' ) . '</h3>
			<ul>
			<li>' . __( 'GPLv3', 'creative-tag-cloud' ) . '</li>
			<li>' . sprintf( __( 'This plugin uses <a %s>jQuery UI</a>. (bundled with WordPress)', 'creative-tag-cloud' ), 'href="http://jqueryui.com/" target="_blank"') . '</li>
			</ul>';


		} else if ( 'support' == $active_tab ) {
			$html .= '<div style="float:left;width:300px; margin:20px;">';

			$html .= '
			<h2>' . __( 'Get Support', 'creative-tag-cloud' ) . '</h2>' .
			'<p>' . sprintf( __( 'You can find detailed instructions and examples in the <a %s>documentation</a>.', 'creative-tag-cloud' ), 'href="https://documentation.chattymango.com/documentation/creative-tag-cloud/?pk_campaign=ctc&pk_kwd=dashboard" target="_blank"' ) . '</p>'.
			sprintf( __( 'If you still have a question or find a bug, please visit the <a %s>support forum</a>.', 'creative-tag-cloud' ), 'href="https://wordpress.org/support/plugin/creative-tag-cloud/" target="_blank"' ) . '</p>';

				$html .= '</div>
				<div style="float:left; margin:20px;">';

				$html .= '<h2>' . __( 'Latest Development News', 'creative-tag-cloud' ) . '</h2>'.
				'
				<table class="widefat fixed" cellspacing="0" style="max-width:600px;">
				<thead>
				<tr>
				<th style="width:200px;"></th>
				<th></th>
				</tr>
				</thead>
				<tbody id="cmtc_feed_container"><tr><td colspan="2" style="text-align:center;">' .
				__( 'Loading...', 'creative-tag-cloud' ) .
				'</td></tr></tbody>
				</table>

				<script>
				jQuery(document).ready(function(){
					var tg_feed_amount = jQuery("#tg_feed_amount").val();
					var data = {
						action: "cmtc_ajax_get_feed",
						url: "' . CREATIVE_TAG_CLOUD_UPDATES_RSS_URL . '",
						amount: 5
					};

					jQuery.post("';

					$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
					$html .= admin_url( 'admin-ajax.php', $protocol ) .

					'", data, function (data) {
						var status = jQuery(data).find("response_data").text();
						if (status == "success") {
							var output = jQuery(data).find("output").text();
							jQuery("#cmtc_feed_container").html(output);
						}
					});
				});
				</script>
				</div>';

				$html .= '<div style="float:left; clear:both;">
				<h3>' . __( 'Debugging Information', 'creative-tag-cloud' ) . '</h3>';


				$html .= '<div class="cmtc_admin_accordion" style="cursor:pointer;">
				<h4>' . __( 'Server', 'creative-tag-cloud' ) . '</h4>';

				$html .= '<table class="widefat fixed">';

				$html .= '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
				$html .= '<tr><td>PHP Memory Limit</td><td>' . ini_get('memory_limit') . '</td></tr>';

				$html .= '</table></div>';

				$html .= '<div class="cmtc_admin_accordion" style="cursor:pointer;">
				<h4>' . __( 'WordPress', 'creative-tag-cloud' ) . '</h4>';

				$html .= '<table class="widefat fixed">';

				$html .= '<tr><td>WordPress Version</td><td>' . get_bloginfo('version') . '</td></tr>';
				$html .= '<tr><td>Site URL</td><td>' . site_url() . '</td></tr>';
				$html .= '<tr><td>Home URL</td><td>' . home_url() . '</td></tr>';
				$html .= '</table></div>';

				/* constants */
				$wp_constants = array(
					'WP_DEBUG',
					'WP_ERROR_LOG',
					'WP_DEBUG_DISPLAY',
					'ABSPATH',
					'WP_HOME',
					'MULTISITE',
					'WP_CACHE',
					'COMPRESS_SCRIPTS',
					'FS_CHMOD_DIR',
					'FS_CHMOD_FILE',
					'FORCE_SSL_ADMIN'
				);

				$html .= '<div class="cmtc_admin_accordion" style="cursor:pointer;">
				<h4>' . __( 'Constants', 'creative-tag-cloud' ) . '</h4>';

				$html .= '<table class="widefat fixed">';

				$constants = get_defined_constants();

				sort( $wp_constants );

				foreach ( $wp_constants as $wp_constant ) {

					if ( isset( $constants[$wp_constant] ) ) {

						$html .= '<tr><td>' . $wp_constant . '</td><td>' . $this->echo_var( $constants[$wp_constant] ) . '</td></tr>';

					} else {

						$html .= '<tr><td>' . $wp_constant . '</td><td>not set</td></tr>';

					}
				}

				ksort( $constants );

				foreach ( $constants as $key => $value ) {

					if ( preg_match( "/^CREATIVE_TAG_CLOUD_/", $key ) == 1 ) {

						$html .= '<tr><td>' . $key . '</td><td>' . $this->echo_var( $value ) . '</td></tr>';

					}
				}
				$html .= '</table></div>';

				$html .= '</div>';

			} else {
				$html .=   '<h2>' . sprintf( __( 'Tag Clouds', 'creative-tag-cloud' ) . '</h2>
				<p>' . __('The plugin comes with a <a %s>widget</a> for each type of tag cloud. You can also insert one of the shortcodes below in a page or post. The parameters for the widgets and the shortcodes are the same.', 'creative-tag-cloud' ), 'href="' . admin_url( 'widgets.php' ) . '"' ) . '</p>
				<h4>' . __( 'Note', 'creative-tag-cloud' ) . '</h4>
				<p>' . __( 'Due to the way how the plugin determines the remaining space, the tag cloud always starts with the largest tags (i.e. descending order by post count).', 'creative-tag-cloud' ) . '</p>
				<p>' . __( 'Finding the right values depends on many factors, such as post count, the difference between popular and average or unpopular tags, the amount of tags for each group of tags according to their post counts and the like. It will often be necessary that you fine-tune the available parameters manually until you achieve the desired result.', 'creative-tag-cloud' ) . '</p>';

				$html .= '<h3>' . __('Shortcodes', 'creative-tag-cloud' ) . '</h3>
				<p>' . __('Click for more information.', 'creative-tag-cloud' ) . '</p>
				<div class="cmtc_admin_accordion" style="cursor:pointer;">
				<h4>' . __( 'Spiral Tag Cloud', 'creative-tag-cloud' ) . '</h4>';
				$html .= '<div style="margin:10px;">
				<h4>' . __( 'Format', 'creative-tag-cloud' ) . ':</h4>
				<p><pre>[creative_spiral_tag_cloud]</pre></p>
				<h4>' . __( 'Example', 'creative-tag-cloud' ) . ':</h4>
				<p><pre>[creative_spiral_tag_cloud amount=40 separator="|" smallest=10 largest=15 taxonomy="post_tag,product_tag" color=1]</pre></p>

				<ul>';
				$html .= '<li><b>div_id</b>: ' . __( "ID of the enclosing div for custom styling. This is by default a random value so that multiple tag clouds on the same page won't conflict.", 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>taxonomy</b>: ' . __( 'Comma-separated list of taxonomy to use. Default is "post_tag".', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>amount</b>: ' . __( 'Maximum amount of tags to display. Default is 40. The amount is also limited by how many tags fit into the coil.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>hide_empty</b>: ' . __( 'Hide tags without posts. Default is 1. Use O (off) or 1 (on)', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>width</b>: ' . __( 'Width of the container. Default is 100%. Enter the number with the unit sign, such as px or %.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>height</b>: ' . __( 'Height of the container. Default is 500px. Enter the number with the unit sign, such as px or %.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>margin_left</b>: ' . __( 'Margin left of each tag and separator. Default is 5. The unit defaults to px.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>separator</b>: ' . __( 'Optional separator between the tags. Default is empty.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>largest</b>: ' . __( 'Largest font size of tag. Default is 50.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>smallest</b>: ' . __( 'Smallest font size of tag. Default is 10.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>direction</b>: ' . __( 'CW (clockwise) or CCW (counterclockwise). The default is CCW.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>start</b>: ' . __( 'Where the largest tag will start. Values can be "top", "bottom", "left", "right" or a number from 0 to 2*PI (PI is about 3.1415). Default is "top".', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>cycles</b>: ' . __( 'Number of cycles (round): a number, or "auto" (the plugin tries to fit all tags on the spiral), or "size" (the plugin tries to fit the spiral into the given space). This parameter is only approximate, since the available space and the font size may allow only less cycles. The default is "auto".', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>line_height_factor </b>: ' . __( 'Distance between cycles (rounds). Good values are 0.8 - 1.5. Default is 1.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>reduce_factor </b>: ' . __( 'How much the spiral should become tighter towards the center. Good values are 1 - 10. Default is 1.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>custom_title </b>: ' . __('Custom title shown as tooltip. No HTML, but you can use {count} and {description} as placeholders. Default: empty (using description and tag count)', 'creative-tag-cloud') . '</li>';
				$html .= '<li><b>color</b>: ' . __( 'Whether to use color (=1) or not (=0, default) for different post counts. The colors are set in a style sheet (CSS) by the classes .creative-tag-cloud-color-1, .creative-tag-cloud-color-2, and so on. The numbers correspond to the sequential order of the groups of tags with the same post count. The plugin comes with a sample set of colors.', 'creative-tag-cloud' ) . '</li>';
				$html .= '</ul>
				</div>';

				$html .= '<h4>' . __( 'Wave Tag Cloud', 'creative-tag-cloud' ) . '</h4>';
				$html .= '<div style="margin:10px;">

				<h4>' . __( 'Format', 'creative-tag-cloud' ) . '</h4>
				<p><pre>[creative_wave_tag_cloud]</pre></p>
				<h4>' . __( 'Example', 'creative-tag-cloud' ) . ':</h4>
				<p><pre>[creative_wave_tag_cloud smallest=2 largest=50 amount=40 waves=5 color=1]</pre></p>

				<ul>';
				$html .= '<li><b>div_id</b>: ' . __( "ID of the enclosing div for custom styling. This is by default a random value so that multiple tag clouds on the same page won't conflict.", 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>taxonomy</b>: ' . __( 'Comma-separated list of taxonomy to use. Default is "post_tag".', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>amount</b>: ' . __( 'Maximum amount of tags to display. Default is 40. The amount is also limited by how many tags fit into the coil.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>hide_empty</b>: ' . __( 'Hide tags without posts. Default is 1. Use O (off) or 1 (on)', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>tags_per_wave</b>: ' . __( "Maximum number of tags per wave. Number or 'auto', which tries to remove tags that don't fit on the wave. Default is 'auto'.", 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>width</b>: ' . __( 'Width of the container. Default is 100%. Enter the number with the unit sign, such as px or %.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>height</b>: ' . __( 'Height of the container. Default is 500px. Enter the number with the unit sign, such as px or %.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>margin_left</b>: ' . __( 'Margin left of each tag and separator. Default is 5. The unit defaults to px.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>separator</b>: ' . __( 'Optional separator between the tags. Default is empty.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>waves</b>: ' . __( 'Number of waves above each other. Each waves begins with the largest tags on the left. Default is 3.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>frequency</b>: ' . __( 'The frequency determines who tight the waves are bent. Default is 10.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>change_wavelength</b>: ' . __( 'This factor changes the frequency towards the smaller tags. 1 mean no change, a factor like 1.2 slightly increases the frequency. Default is 1.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>opacity_decay</b>: ' . __( 'Determines a reduction of the opacity value, that means that smaller tags become more transparent. Default is 0.8.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>largest</b>: ' . __( 'Largest font size of tag. Default is 50.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>smallest</b>: ' . __( 'Smallest font size of tag. Default is 10.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>line_height_factor </b>: ' . __( 'Distance between waves. Good values are 0.8 - 1.5. Default is 1.', 'creative-tag-cloud' ) . '</li>';
				$html .= '<li><b>custom_title </b>: ' . __('Custom title shown as tooltip. No HTML, but you can use {count} and {description} as placeholders. Default: empty (using description and tag count)', 'creative-tag-cloud') . '</li>';
				$html .= '<li><b>color</b>: ' . __( ' Whether to use color (=1) or not (=0, default) for different post counts. The colors are set in a style sheet (CSS) by the classes .creative-tag-cloud-color-1, .creative-tag-cloud-color-2, and so on. The numbers correspond to the sequential order of the groups of tags with the same post count. The plugin comes with a sample set of colors.', 'creative-tag-cloud' ) . '</li>';
				$html .= '</ul>
				</div>';

				$html .= '</div>';
			}

			$html .= '</div>';

			$html .= '
			<!-- begin Creative Tag Cloud plugin -->
			<script type="text/javascript">
			jQuery(function() {
				var icons = {
					header: "dashicons dashicons-arrow-right",
					activeHeader: "dashicons dashicons-arrow-down"
				};
				jQuery( ".cmtc_admin_accordion" ).accordion({
					icons:icons,
					collapsible: true,
					active: false,
					heightStyle: "content"
				});
			});
			</script>
			<!-- end Creative Tag Cloud -->
			';

			echo $html;

		}


		/**
		* AJAX handler to get a feed
		*
		* @param void
		* @return string
		* @since 1.0
		*/
		static function cmtc_ajax_get_feed()
		{

			$response = new WP_Ajax_Response;

			if ( isset( $_REQUEST['url'] ) ) {
				$url = esc_url_raw( $_REQUEST['url'] );
			} else {
				$url = '';
			}

			if ( isset( $_REQUEST['amount'] ) ) {
				$amount = (int) $_REQUEST['amount'];
			} else {
				$amount = 5;
			}

			/**
			* Assuming that the posts URL is the $url minus the trailing /feed
			*/
			$posts_url = preg_replace( '/(.+)feed\/?/i', '$1', $url );

			$rss = new ChattyMango_CreativeTagCloud_Feed;

			$rss->debug( WP_DEBUG )->url( $url );
			$cache = $rss->cache_get();

			if ( empty( $cache ) ) {

				$cache = $rss->posts_url( $posts_url )->load()->parse()->render( $amount );

			}

			$response->add( array(
				'data' => 'success',
				'supplemental' => array(
					'output' => $cache,
				),
			) );

			// Cannot use the method $response->send() because it includes die()
			header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );
			echo "<?xml version='1.0' encoding='" . get_option( 'blog_charset' ) . "' standalone='yes'?><wp_ajax>";
			foreach ( (array) $response->responses as $response_item ){
				echo $response_item;
			}
			echo '</wp_ajax>';


			// check if we received expired cache content
			if ( false !== $cache && $rss->expired ) {

				// load in background for next time
				$rss->posts_url( $posts_url )->load()->parse()->render( $amount );

				if ( WP_DEBUG ) {

					error_log('Preloaded feed into cache.');

				}
			}

			if ( wp_doing_ajax() ) {

				wp_die();

			} else {

				die();

			}
		}


		/**
		* Prepares variable for echoing as string
		*
		* @param mixed $var Mixed type that needs to be echoed as string.
		* @return return string
		* @since 1.0
		*/
		private function echo_var( $var = '' )
		{

			if ( is_bool( $var ) ) {

				return $var ? 'true' : 'false';

			} elseif ( is_array( $var ) )  {

				return print_r( $var, true );

			} else {

				return (string) $var;

			}
		}


		/**
		* Adds a link to the plugin entry in the list
		*
		* @param array $links
		* @return array
		*/
		public function add_plugin_settings_link( $links )
		{

			$settings_link = '<a href="' . admin_url( 'options-general.php?page=creative-tag-cloud-settings' ) . '">' . __( 'Help', 'creative-tag-cloud' ) . '</a>';

			array_unshift( $links, $settings_link );

			return $links;

		}


	}
