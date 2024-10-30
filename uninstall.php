<?php
/**
* Fired when the plugin is uninstalled.
*
* @link       https://chattymango.com/creative-tag-cloud/
* @since      0.1.0
*
* @package    creative_tag_cloud
* @author     Christoph Amthor @ Chatty Mango
* @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPLv3
*
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit;

}

/*
* Purge cache
*/
require_once plugin_dir_path( __FILE__ ) . 'includes/class-chatty-mango-cache.php';

if ( class_exists( 'ChattyMango_CreativeTagCloud_Cache' ) ) {

	$tag_group_object_cache = get_option( 'tag_group_object_cache', ChattyMango_CreativeTagCloud_Cache::FILE );

	$cache = new ChattyMango_CreativeTagCloud_Cache();
	$cache->type( $tag_group_object_cache )
	->path( WP_CONTENT_DIR . '/chatty-mango/cache/' )
	->purge_all();

}


/**
*	Delete options
*/
delete_option( 'cm_tag_cloud_latest_version' );

delete_option( 'cm_tag_cloud_latest_version_url' );

delete_option( 'chatty_mango_packages' );

if ( file_exists( WP_CONTENT_DIR . '/chatty-mango/cache' ) && is_dir( WP_CONTENT_DIR . '/chatty-mango/cache' ) ) {
	/**
	* Attempt to empty and remove the chatty-mango cache directory
	*/
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( WP_CONTENT_DIR . '/chatty-mango/cache/' ) ) as $file) {
		// filter out "." and ".."
		if ($file->isDir()) continue;

		@unlink( $file->getPathname() );

	}

	@rmdir( WP_CONTENT_DIR . '/chatty-mango/cache' );

}
