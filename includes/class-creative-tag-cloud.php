<?php

/**
* The file that defines the core plugin class
*
* @link       https://chattymango.com/creative-tag-cloud/
* @since      0.1.0
* @package    creative_tag_cloud
* @subpackage creative_tag_cloud/includes
* @author     Christoph Amthor @ Chatty Mango
*/
class creative_tag_cloud {

	/**
	* The loader that's responsible for maintaining and registering all hooks that power
	* the plugin.
	*
	* @since    1.0
	* @access   protected
	* @var      creative_tag_cloud_Loader    $loader    Maintains and registers all hooks for the plugin.
	*/
	protected $loader;

	/**
	* The unique identifier of this plugin.
	*
	* @since    1.0
	* @access   protected
	* @var      string    $creative_tag_cloud    The string used to uniquely identify this plugin.
	*/
	protected $creative_tag_cloud;

	/**
	* The current version of the plugin.
	*
	* @since    1.0
	* @access   protected
	* @var      string    $version    The current version of the plugin.
	*/
	protected $version;

	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    1.0
	*/
	public function __construct() {


		$this->version = $this->get_version_from_plugin_data( CREATIVE_TAG_CLOUD_PLUGIN_ABSOLUTE_NAME );

		define( 'CREATIVE_TAG_CLOUD_VERSION', $this->version );

		$this->creative_tag_cloud = 'creative-tag-cloud';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}


	/**
	* get the version
	*
	*
	* @param string $path Absolute path of this plugin
	* @return void
	*/
	private function get_version_from_plugin_data( $path )
	{

		if ( !function_exists('get_plugin_data') ){

			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		}

		$plugin_header = get_plugin_data( $path, false, false );

		if ( isset( $plugin_header['Version'] ) ) {

			return $plugin_header['Version'];

		} else {

			return '1.0';

		}

	}


	/**
	* Load the required dependencies for this plugin.
	*
	* Include the following files that make up the plugin:
	*
	* - creative_tag_cloud_Loader. Orchestrates the hooks of the plugin.
	* - creative_tag_cloud_i18n. Defines internationalization functionality.
	* - creative_tag_cloud_Admin. Defines all hooks for the admin area.
	* - creative_tag_cloud_Public. Defines all hooks for the public side of the site.
	*
	* Create an instance of the loader which will be used to register the hooks
	* with WordPress.
	*
	* @since    1.0
	* @access   private
	*/
	private function load_dependencies() {

		/**
		* The class responsible for orchestrating the actions and filters of the
		* core plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-creative-tag-cloud-loader.php';

		/**
		* The class responsible for defining internationalization functionality
		* of the plugin.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-creative-tag-cloud-i18n.php';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-creative-tag-cloud-admin.php';

		/**
		* The class responsible for defining all actions that occur in the public-facing
		* side of the site.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-creative-tag-cloud-public.php';

		/**
		* The class responsible for defining all actions that occur when executing
		* shortcodes
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-creative-tag-cloud-shortcode.php';

		/**
		* The class responsible for defining all actions regarding widgets
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-creative-tag-cloud-widget.php';

		/**
		* The class responsible for defining all actions regarding caching
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chatty-mango-cache.php';

		/**
		* The class responsible for defining all actions regarding development feeds
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chatty-mango-feed.php';

		/**
		* Helper class
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-creative-tag-cloud-tools.php';

		$this->loader = new creative_tag_cloud_Loader();

	}

	/**
	* Define the locale for this plugin for internationalization.
	*
	* Uses the creative_tag_cloud_i18n class in order to set the domain and to register the hook
	* with WordPress.
	*
	* @since    1.0
	* @access   private
	*/
	private function set_locale() {

		$plugin_i18n = new creative_tag_cloud_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	* Register all of the hooks related to the admin area functionality
	* of the plugin.
	*
	* @since    1.0
	* @access   private
	*/
	private function define_admin_hooks() {

		$plugin_admin = new creative_tag_cloud_Admin( $this->get_creative_tag_cloud(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_cmtc_ajax_get_feed', $plugin_admin, 'cmtc_ajax_get_feed' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_options_page' );

		$this->loader->add_action( 'plugin_action_links_' . CREATIVE_TAG_CLOUD_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_settings_link' );

	}


	/**
	* Register all of the hooks related to the public-facing functionality
	* of the plugin.
	*
	* @since    1.0
	* @access   private
	*/
	private function define_public_hooks() {

		$plugin_public = new creative_tag_cloud_Public( $this->get_creative_tag_cloud(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'widgets_init', $plugin_public, 'load_widget' );

		add_shortcode( 'creative_wave_tag_cloud', array( 'Creative_Tag_Cloud_Shortcode', 'creative_wave_tag_cloud' ) );
		add_shortcode( 'creative_spiral_tag_cloud', array( 'Creative_Tag_Cloud_Shortcode', 'creative_spiral_tag_cloud' ) );

	}

	/**
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @since    1.0
	*/
	public function run() {
		$this->loader->run();
	}

	/**
	* The name of the plugin used to uniquely identify it within the context of
	* WordPress and to define internationalization functionality.
	*
	* @since     1.0
	* @return    string    The name of the plugin.
	*/
	public function get_creative_tag_cloud() {
		return $this->creative_tag_cloud;
	}

	/**
	* The reference to the class that orchestrates the hooks with the plugin.
	*
	* @since     1.0
	* @return    creative_tag_cloud_Loader    Orchestrates the hooks of the plugin.
	*/
	public function get_loader() {
		return $this->loader;
	}

	/**
	* Retrieve the version number of the plugin.
	*
	* @since     1.0
	* @return    string    The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}



}
