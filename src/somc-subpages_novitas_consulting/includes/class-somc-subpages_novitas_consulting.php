<?php

/**
 * The core plugin class.
 *
 * This is used to define public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @since      1.0.0
 * @package    Somc_Subpages_Novitas_Consulting
 * @subpackage Somc_Subpages_Novitas_Consulting/includes
 * @author     Andrew Pool <ap@novitas.as>
 */
class Somc_Subpages_Novitas_Consulting {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that are used by the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Somc_Subpages_Novitas_Consulting_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, and sets the hooks for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'somc-subpages_novitas_consulting';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->define_public_hooks();
        $this->register_widgets();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Creates an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-somc-subpages_novitas_consulting-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-somc-subpages_novitas_consulting-public.php';

        /**
         * The class responsible for defining the sub pages sidebar widget
         */
        require_once plugin_dir_path( __FILE__ ) . 'class-somc-subpages-novitas-consulting-widget.php';

		$this->loader = new Somc_Subpages_Novitas_Consulting_Loader();

	}

	/**
	 * Registers all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Somc_Subpages_Novitas_Consulting_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_shortcode('somc-subpages-novitas-consulting', $plugin_public, 'render_subpages_tree');
	}

    /**
     * Registers the sub pages widget.
     *
     * @since    1.0.0
     */
    public function register_widgets(){
        add_action( 'widgets_init', function(){
            register_widget( 'Somc_Subpages_Novitas_Consulting_Widget' );
        });
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Somc_Subpages_Novitas_Consulting_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}