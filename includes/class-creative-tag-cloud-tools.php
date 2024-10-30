<?php

/**
 * Define the shortcode functionality
 *
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * @package    creative_tag_cloud
 * @subpackage creative_tag_cloud/includes
 *
 * @author     Christoph Amthor @ Chatty Mango
 * @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPLv3
 *
 * @link       https://chattymango.com/creative-tag-cloud/
 * @since      0.3.1
 */

if ( ! class_exists( 'Creative_Tag_Cloud_Tools' ) ) {

    class Creative_Tag_Cloud_Tools {

    /**
         * Wrapper for remove_all_filters(), returning previously registered functions
         *
         * @param  array|string $hook_names (array of comma-separated list)
         * @return array
         */
        static function remove_all_filters( $hook_names ) {

            if ( empty( $hook_names ) ) {

                return array();

            }

            if ( ! is_array( $hook_names ) ) {

                $hook_names = array_map( 'trim', explode( ',', $hook_names ) );

            }

            $hook_array = array();

            foreach ( $hook_names as $hook_name ) {

                $hook_array[] = array(
                    'name'        => $hook_name,
                    'subscribers' => self::get_all_hooks( $hook_name ),
                );

                remove_all_filters( $hook_name );

            }

            return $hook_array;

        }

        /**
         * Restores all hook from self::remove_all_filters()
         *
         * @param  array  $hook_array
         * @return void
         */
        static function restore_hooks( $hook_array ) {

            if ( empty( $hook_array ) ) {

                return;
            
            }

            foreach ( $hook_array as $hooks ) {

                foreach ( $hooks['subscribers'] as $subscriber ) {

                    add_filter( $hooks['name'], $subscriber['function'], $subscriber['priority'], $subscriber['accepted_args'] );

                }

            }

        }


        /**
         * List all hooks
         *
         * https://stackoverflow.com/a/26680808
         *
         * @param  string  $hook
         * @return array
         */
        static function get_all_hooks( $hook = '' ) {
            global $wp_filter;

            $hooks = array();

            if ( isset( $wp_filter[$hook]->callbacks ) ) {
                array_walk( $wp_filter[$hook]->callbacks, function ( $callbacks, $priority ) use ( &$hooks ) {

                    foreach ( $callbacks as $id => $callback ) {
                        $hooks[] = array_merge( ['id' => $id, 'priority' => $priority], $callback );
                    }

                } );
            } else {
                return [];
            }

            foreach ( $hooks as &$item ) {

                // skip if callback does not exist
                if ( ! is_callable( $item['function'] ) ) {
                    continue;
                }

                // function name as string or static class method eg. 'Foo::Bar'
                if ( is_string( $item['function'] ) ) {
                    $ref          = strpos( $item['function'], '::' ) ? new ReflectionClass( strstr( $item['function'], '::', true ) ) : new ReflectionFunction( $item['function'] );
                    $item['file'] = $ref->getFileName();
                    $item['line'] = get_class( $ref ) == 'ReflectionFunction'
                    ? $ref->getStartLine()
                    : $ref->getMethod( substr( $item['function'], strpos( $item['function'], '::' ) + 2 ) )->getStartLine();

                    // array( object, method ), array( string object, method ), array( string object, string 'parent::method' )
                } elseif ( is_array( $item['function'] ) ) {

                    $ref = new ReflectionClass( $item['function'][0] );

                    // $item['function'][0] is a reference to existing object
                    $item['function'] = array(
                        // is_object($item['function'][0]) ? get_class($item['function'][0]) : $item['function'][0], // We need the object! Using the class here leads to "Using $this when not in object context" errors
                        $item['function'][0],
                        $item['function'][1],
                    );
                    $item['file'] = $ref->getFileName();
                    $item['line'] = strpos( $item['function'][1], '::' )
                    ? $ref->getParentClass()->getMethod( substr( $item['function'][1], strpos( $item['function'][1], '::' ) + 2 ) )->getStartLine()
                    : $ref->getMethod( $item['function'][1] )->getStartLine();

                    // closures
                } elseif ( is_callable( $item['function'] ) ) {
                    $ref              = new ReflectionFunction( $item['function'] );
                    $item['function'] = get_class( $item['function'] );
                    $item['file']     = $ref->getFileName();
                    $item['line']     = $ref->getStartLine();
                }

            }

            return $hooks;
        }

    }

}
