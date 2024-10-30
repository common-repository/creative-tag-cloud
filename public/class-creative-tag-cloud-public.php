<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * @package    creative_tag_cloud
 * @subpackage creative_tag_cloud/public
 *
 * @author     Christoph Amthor @ Chatty Mango
 * @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPLv3
 *
 * @link       https://chattymango.com/creative-tag-cloud/
 * @since      0.1.0
 */
class creative_tag_cloud_Public {

    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var string $creative_tag_cloud The ID of this plugin.
     * @since    1.0
     */
    private $creative_tag_cloud;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var string $version The current version of this plugin.
     * @since    1.0
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0
     *
     * @param string $creative_tag_cloud The name of the plugin.
     * @param string $version            The version of this plugin.
     */
    public function __construct( $creative_tag_cloud, $version ) {

        $this->creative_tag_cloud = $creative_tag_cloud;
        $this->version            = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->creative_tag_cloud, plugin_dir_url( __FILE__ ) . 'css/creative-tag-cloud.css', array(), $this->version, 'all' );

        /**
         * Offer the possibility to customize colors by overriding the default
         */
        $upload_dir = wp_upload_dir();

        if ( is_array( $upload_dir ) && file_exists( $upload_dir['basedir'] . '/creative-tag-cloud-color.css' ) ) {
            wp_enqueue_style( 'creative-tag-cloud-color', $upload_dir['baseurl'] . '/creative-tag-cloud-color.css', array(), $this->version, 'all' );
        } else {
            wp_enqueue_style( 'creative-tag-cloud-color', plugin_dir_url( __FILE__ ) . 'css/creative-tag-cloud-color.css', array(), $this->version, 'all' );
        }

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0
     */
    public function enqueue_scripts() {

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

            wp_enqueue_script( $this->creative_tag_cloud, plugin_dir_url( __FILE__ ) . 'js/creative-tag-cloud.js', array( 'jquery' ), $this->version, false );

        } else {

            wp_enqueue_script( $this->creative_tag_cloud, plugin_dir_url( __FILE__ ) . 'js/creative-tag-cloud.min.js', array( 'jquery' ), $this->version, false );

        }

    }

    /**
     * Register the widget
     *
     * @since    1.0
     */
    public function load_widget() {
        register_widget( 'creative_wave_tag_cloud_widget' );
        register_widget( 'creative_spiral_tag_cloud_widget' );
    }

}
