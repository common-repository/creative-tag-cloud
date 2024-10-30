<?php

/**
 * Define the widget functionality
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
 * @package    creative-tag-cloud
 * @subpackage creative-tag-cloud/includes
 *
 * @author     Christoph Amthor @ Chatty Mango
 * @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPLv3
 *
 * @link       https://chattymango.com/creative-tag-cloud/
 * @since      0.1.0
 */

if ( ! class_exists( 'Creative_Spiral_Tag_Cloud_Widget' ) ) {

    /**
     *     Tag cloud with tags along a spiral
     */
    class Creative_Spiral_Tag_Cloud_Widget extends WP_Widget {

        /**
         * Registering the widget
         *
         * @since    1.0
         */
        function __construct() {
            parent::__construct(

                // Base ID
                'creative_spiral_tag_cloud_widget',

                // Widget name
                __( 'Creative Spiral Cloud', 'creative-tag-cloud' ),

                // Widget description
                array( 'description' => __( 'Display a tag cloud in a spiral shape.', 'creative-tag-cloud' ) )
            );
        }

        /**
         * Doing it's widget things
         *
         * @since    1.0
         */
        public function widget( $args, $instance ) {
            $title = apply_filters( 'widget_title', $instance['title'] );

            // before and after widget arguments are defined by themes
            echo $args['before_widget'];

            if ( ! empty( $title ) ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $creative_tag_cloud_widget_atts = array();

            foreach ( $instance as $key => $value ) {

                if ( 'title' == $key ) {
                    continue;
                }

                if ( 'div_id' == $key && empty( $value ) ) {
                    continue;
                }

                $creative_tag_cloud_widget_atts[$key] = $value;

            }

            echo Creative_Tag_Cloud_Shortcode::creative_spiral_tag_cloud( $creative_tag_cloud_widget_atts );

            echo $args['after_widget'];
        }

        /**
         * Backend of the widget
         *
         * @since    1.0
         */
        public function form( $instance ) {

            if ( isset( $instance['title'] ) ) {
                $title              = $instance['title'];
                $div_id             = $instance['div_id'];
                $taxonomy           = $instance['taxonomy'];
                $amount             = $instance['amount'];
                $width              = $instance['width'];
                $height             = $instance['height'];
                $margin_left        = $instance['margin_left'];
                $separator          = $instance['separator'];
                $largest            = $instance['largest'];
                $smallest           = $instance['smallest'];
                $color              = $instance['color'];
                $custom_title       = $instance['custom_title'];
                $direction          = $instance['direction'];
                $start              = $instance['start'];
                $cycles             = $instance['cycles'];
                $line_height_factor = $instance['line_height_factor'];
                $reduce_factor      = $instance['reduce_factor'];
                // added later
                $hide_empty         = isset($instance['hide_empty']) ? $instance['hide_empty'] : 1;
            } else {
                $title              = ''; // __( 'New title', 'creative-tag-cloud' );
                $taxonomy           = 'post_tag';
                $div_id             = '';
                $amount             = 40;
                $hide_empty         = 1;
                $width              = '100%';
                $height             = '500px';
                $margin_left        = 5;
                $separator          = '-';
                $largest            = 50;
                $smallest           = 10;
                $color              = 0;
                $custom_title       = '';
                $direction          = 'CCW';
                $start              = 'top';
                $cycles             = 'auto';
                $line_height_factor = 1.2;
                $reduce_factor      = 1;
            }

            ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' );?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'div_id' ); ?>"><?php _e( 'ID for the enclosing div:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'div_id' ); ?>" name="<?php echo $this->get_field_name( 'div_id' ); ?>" type="text" value="<?php echo $div_id; ?>" />
					<small><?php _e( '(Leave empty if no custom styling required.)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $width; ?>" />
					<small><?php _e( '(enter as 100%, 200px, ...)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $height; ?>" />
					<small><?php _e( '(enter as 100%, 200px, ...)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'creative-tag-cloud' );?></label>
					<select multiple name="<?php echo $this->get_field_name( 'taxonomy' ); ?>[]" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
						<?php
$options    = get_taxonomies( array( 'public' => true ), 'names' );
            $taxonomies = explode( ',', $taxonomy );

            foreach ( $options as $option ) {
                echo '<option value="' . $option . '" id="' . $option . '"', in_array( $option, $taxonomies ) ? ' selected="selected"' : '', '>', $option, '</option>';
            }

            ?>
					</select>
					<small><?php _e( '(Hold ctrl/cmd to select multiple taxonomies.)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'amount' ); ?>"><?php _e( 'Maximum Amount of Tags:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" type="text" value="<?php echo $amount; ?>" />
				</p>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $hide_empty );?> />
						<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php _e( 'Hide Tags Without Posts', 'creative-tag-cloud' );?></label>
					</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><?php _e( 'Size of Largest Tag:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" type="text" value="<?php echo $largest; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><?php _e( 'Size of Smallest Tag:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" type="text" value="<?php echo $smallest; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'margin_left' ); ?>"><?php _e( 'Margin Left:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'margin_left' ); ?>" name="<?php echo $this->get_field_name( 'margin_left' ); ?>" type="text" value="<?php echo $margin_left; ?>" />
					<small><?php _e( '(in px)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" type="text" value="<?php echo $separator; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'custom_title' ); ?>"><?php _e( 'Custom Title:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'custom_title' ); ?>" name="<?php echo $this->get_field_name( 'custom_title' ); ?>" type="text" value="<?php echo $custom_title; ?>" />
					<small><?php _e( '(Shows up as tooltip over tags. Placeholders: {count}, {description})', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'line_height_factor' ); ?>"><?php _e( 'Line Height Factor:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'line_height_factor' ); ?>" name="<?php echo $this->get_field_name( 'line_height_factor' ); ?>" type="text" value="<?php echo $line_height_factor; ?>" />
					<small><?php _e( '(Changes the distance between the cycles. Good values are 0.8 - 1.5)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'reduce_factor' ); ?>"><?php _e( 'Spiral Reduce Factor:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'reduce_factor' ); ?>" name="<?php echo $this->get_field_name( 'reduce_factor' ); ?>" type="text" value="<?php echo $reduce_factor; ?>" />
					<small><?php _e( '(How much the spiral becomes tighter to the center. Good values are 1 - 10)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'direction' ); ?>"><?php _e( 'Direction', 'creative-tag-cloud' );?></label>
					<select name="<?php echo $this->get_field_name( 'direction' ); ?>" id="<?php echo $this->get_field_id( 'direction' ); ?>" class="widefat">
						<?php
$options = array( 'CW', 'CCW' );

            foreach ( $options as $option ) {
                echo '<option value="' . $option . '" id="' . $option . '"', $direction == $option ? ' selected="selected"' : '', '>', $option, '</option>';
            }

            ?>
					</select>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'start' ); ?>"><?php _e( 'Start:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'start' ); ?>" name="<?php echo $this->get_field_name( 'start' ); ?>" type="text" value="<?php echo $start; ?>" />
					<small><?php _e( '("top", "right", "bottom", "left", or a number 0 - 2π)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'cycles' ); ?>"><?php _e( 'Cycles:', 'creative-tag-cloud' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'cycles' ); ?>" name="<?php echo $this->get_field_name( 'cycles' ); ?>" type="text" value="<?php echo $cycles; ?>" />
					<small><?php _e( '("auto" or a number)', 'creative-tag-cloud' )?></small>
				</p>
				<p>
					<input id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $color );?> />
					<label for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"><?php _e( 'Display Colors', 'creative-tag-cloud' );?></label>
					<small><?php _e( '(Specified in your Style Sheet as .creative-tag-cloud-color-1, -2 and so on.)', 'creative-tag-cloud' )?></small>
				</p>
				<?php
}

        public function update( $new_instance, $old_instance ) {

            /**
             *     Retrieving sent values and setting defaults
             */
            $instance = array();

            $instance['title']              = ( isset( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['div_id']             = ( isset( $new_instance['div_id'] ) ) ? strip_tags( $new_instance['div_id'] ) : '';
            $instance['taxonomy']           = ( ! empty( $new_instance['taxonomy'] ) ) ? implode( ',', $new_instance['taxonomy'] ) : 'post_tag';
            $instance['amount']             = ( ! empty( $new_instance['amount'] ) ) ? strip_tags( $new_instance['amount'] ) : 40;
            $instance['hide_empty']         = ( ! empty( $new_instance['hide_empty'] ) ) ? strip_tags( $new_instance['hide_empty'] ) : 0;
            $instance['margin_left']        = ( ! empty( $new_instance['margin_left'] ) ) ? strip_tags( $new_instance['margin_left'] ) : 5;
            $instance['separator']          = ( isset( $new_instance['separator'] ) ) ? strip_tags( $new_instance['separator'] ) : '';
            $instance['largest']            = ( ! empty( $new_instance['largest'] ) ) ? strip_tags( $new_instance['largest'] ) : 50;
            $instance['smallest']           = ( ! empty( $new_instance['smallest'] ) ) ? strip_tags( $new_instance['smallest'] ) : 10;
            $instance['direction']          = ( ! empty( $new_instance['direction'] ) ) ? strip_tags( $new_instance['direction'] ) : 'CCW';
            $instance['start']              = ( ! empty( $new_instance['start'] ) ) ? strip_tags( $new_instance['start'] ) : 'top';
            $instance['cycles']             = ( ! empty( $new_instance['cycles'] ) ) ? strip_tags( $new_instance['cycles'] ) : 'auto';
            $instance['width']              = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '100%';
            $instance['height']             = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '500px';
            $instance['line_height_factor'] = ( ! empty( $new_instance['line_height_factor'] ) ) ? strip_tags( $new_instance['line_height_factor'] ) : 1.2;
            $instance['reduce_factor']      = ( ! empty( $new_instance['reduce_factor'] ) ) ? strip_tags( $new_instance['reduce_factor'] ) : 1;
            $instance['color']              = ( ! empty( $new_instance['color'] ) ) ? strip_tags( $new_instance['color'] ) : 0;
            $instance['custom_title']       = ( isset( $new_instance['custom_title'] ) ) ? strip_tags( $new_instance['custom_title'] ) : '';

            return $instance;
        }

    }

}

if ( ! class_exists( 'Creative_Wave_Tag_Cloud_Widget' ) ) {

    /**
     *     Tag cloud with horizontally and vertically "boxed" tags
     */
    class Creative_Wave_Tag_Cloud_Widget extends WP_Widget {

        /**
         * Registering the widget
         *
         * @since    1.0
         */
        function __construct() {
            parent::__construct(

                // Base ID
                'creative_wave_tag_cloud_widget',

                // Widget name
                __( 'Creative Wave Cloud', 'creative-tag-cloud' ),

                // Widget description
                array( 'description' => __( 'Display a tag cloud in a wavy shape.', 'creative-tag-cloud' ) )
            );
        }

        /**
         * Doing it's widget things
         *
         * @since    1.0
         */
        public function widget( $args, $instance ) {
            $title = apply_filters( 'widget_title', $instance['title'] );

            // before and after widget arguments are defined by themes
            echo $args['before_widget'];

            if ( ! empty( $title ) ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $creative_tag_cloud_widget_atts = array();

            foreach ( $instance as $key => $value ) {

                if ( 'title' == $key ) {
                    continue;
                }

                if ( 'div_id' == $key && empty( $value ) ) {
                    continue;
                }

                $creative_tag_cloud_widget_atts[$key] = $value;

            }

            echo Creative_Tag_Cloud_Shortcode::creative_wave_tag_cloud( $creative_tag_cloud_widget_atts );

            echo $args['after_widget'];
        }

        /**
         * Backend of the widget
         *
         * @since    1.0
         */
        public function form( $instance ) {

            if ( isset( $instance['title'] ) ) {
                $title              = $instance['title'];
                $div_id             = $instance['div_id'];
                $taxonomy           = $instance['taxonomy'];
                $amount             = $instance['amount'];
                $width              = $instance['width'];
                $height             = $instance['height'];
                $color              = $instance['color'];
                $custom_title       = $instance['custom_title'];
                $separator          = $instance['separator'];
                $largest            = $instance['largest'];
                $smallest           = $instance['smallest'];
                $margin_left        = $instance['margin_left'];
                $change_wavelength  = $instance['change_wavelength'];
                $opacity_decay      = $instance['opacity_decay'];
                $frequency          = $instance['frequency'];
                $waves              = $instance['waves'];
                $line_height_factor = $instance['line_height_factor'];

                // added later
                $hide_empty         = isset($instance['hide_empty']) ? $instance['hide_empty'] : 1;
            } else {
                $title              = ''; // __( 'New title', 'creative-tag-cloud' );
                $div_id             = '';
                $taxonomy           = 'post_tag';
                $amount             = 40;
                $hide_empty         = 1;
                $width              = '100%';
                $height             = '400px';
                $color              = 0;
                $custom_title       = '';
                $separator          = '';
                $largest            = 50;
                $smallest           = 10;
                $margin_left        = 5;
                $change_wavelength  = 1;
                $opacity_decay      = 0.8;
                $frequency          = 5;
                $waves              = 3;
                $line_height_factor = 1.2;
            }

            ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' );?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'div_id' ); ?>"><?php _e( 'ID for the enclosing div:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'div_id' ); ?>" name="<?php echo $this->get_field_name( 'div_id' ); ?>" type="text" value="<?php echo $div_id; ?>" />
						<small><?php _e( '(Leave empty if no custom styling required.)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $width; ?>" />
						<small><?php _e( '(enter as 100%, 200px, ...)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $height; ?>" />
						<small><?php _e( '(enter as 100%, 200px, ...)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'creative-tag-cloud' );?></label>
						<select multiple name="<?php echo $this->get_field_name( 'taxonomy' ); ?>[]" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
							<?php
$options    = get_taxonomies( array( 'public' => true ), 'names' );
            $taxonomies = explode( ',', $taxonomy );

            foreach ( $options as $option ) {
                echo '<option value="' . $option . '" id="' . $option . '"', in_array( $option, $taxonomies ) ? ' selected="selected"' : '', '>', $option, '</option>';
            }

            ?>
						</select>
						<small><?php _e( '(Hold ctrl/cmd to select multiple taxonomies.)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'amount' ); ?>"><?php _e( 'Maximum Amount of Tags:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" type="text" value="<?php echo $amount; ?>" />
					</p>
					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $hide_empty );?> />
						<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php _e( 'Hide Tags Without Posts', 'creative-tag-cloud' );?></label>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><?php _e( 'Size of Largest Tag:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" type="text" value="<?php echo $largest; ?>" />
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><?php _e( 'Size of Smallest Tag:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" type="text" value="<?php echo $smallest; ?>" />
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" type="text" value="<?php echo $separator; ?>" />
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'custom_title' ); ?>"><?php _e( 'Custom Title:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'custom_title' ); ?>" name="<?php echo $this->get_field_name( 'custom_title' ); ?>" type="text" value="<?php echo $custom_title; ?>" />
						<small><?php _e( '(Shows up as tooltip over tags. Placeholders: {count}, {description})', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'line_height_factor' ); ?>"><?php _e( 'Line Height Factor:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'line_height_factor' ); ?>" name="<?php echo $this->get_field_name( 'line_height_factor' ); ?>" type="text" value="<?php echo $line_height_factor; ?>" />
						<small><?php _e( '(Changes the distance between the waves. Good values are 0.8 - 1.5)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'margin_left' ); ?>"><?php _e( 'Margin left of tags:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'margin_left' ); ?>" name="<?php echo $this->get_field_name( 'margin_left' ); ?>" type="text" value="<?php echo $margin_left; ?>" />
						<small><?php _e( 'Add a number (default in px)', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'waves' ); ?>"><?php _e( 'Number of waves:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'waves' ); ?>" name="<?php echo $this->get_field_name( 'waves' ); ?>" type="text" value="<?php echo $waves; ?>" />
						<small><?php _e( '1 or more.', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'frequency' ); ?>"><?php _e( 'Frequency:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'frequency' ); ?>" name="<?php echo $this->get_field_name( 'frequency' ); ?>" type="text" value="<?php echo $frequency; ?>" />
						<small><?php _e( 'Good values are 2 - 10.', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'change_wavelength' ); ?>"><?php _e( 'Change wave length:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'change_wavelength' ); ?>" name="<?php echo $this->get_field_name( 'change_wavelength' ); ?>" type="text" value="<?php echo $change_wavelength; ?>" />
						<small><?php _e( 'Good values are 0.90 - 1.', 'creative-tag-cloud' )?></small>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id( 'opacity_decay' ); ?>"><?php _e( 'Opacity decay:', 'creative-tag-cloud' );?></label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'opacity_decay' ); ?>" name="<?php echo $this->get_field_name( 'opacity_decay' ); ?>" type="text" value="<?php echo $opacity_decay; ?>" />
						<small><?php _e( 'Good values are 0.8 - 1.', 'creative-tag-cloud' )?></small>
					</p>

					<p>
						<input id="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $color );?> />
						<label for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"><?php _e( 'Display Colors', 'creative-tag-cloud' );?></label>
						<small><?php _e( '(Specified in your Style Sheet as .creative-tag-cloud-color-1, -2 and so on.)', 'creative-tag-cloud' )?></small>
					</p>
					<?php
}

        public function update( $new_instance, $old_instance ) {

            /**
             *     Retrieving sent values and setting defaults
             */
            $instance = array();

            $instance['title']              = ( isset( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['div_id']             = ( isset( $new_instance['div_id'] ) ) ? strip_tags( $new_instance['div_id'] ) : '';
            $instance['taxonomy']           = ( ! empty( $new_instance['taxonomy'] ) ) ? implode( ',', $new_instance['taxonomy'] ) : 'post_tag';
            $instance['amount']             = ( ! empty( $new_instance['amount'] ) ) ? strip_tags( $new_instance['amount'] ) : 40;
            $instance['hide_empty']         = ( ! empty( $new_instance['hide_empty'] ) ) ? strip_tags( $new_instance['hide_empty'] ) : 0;
            $instance['width']              = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '100%';
            $instance['height']             = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '400px';
            $instance['color']              = ( ! empty( $new_instance['color'] ) ) ? strip_tags( $new_instance['color'] ) : 0;
            $instance['separator']          = ( isset( $new_instance['separator'] ) ) ? strip_tags( $new_instance['separator'] ) : '';
            $instance['largest']            = ( ! empty( $new_instance['largest'] ) ) ? strip_tags( $new_instance['largest'] ) : 50;
            $instance['smallest']           = ( ! empty( $new_instance['smallest'] ) ) ? strip_tags( $new_instance['smallest'] ) : 10;
            $instance['margin_left']        = ( ! empty( $new_instance['margin_left'] ) ) ? strip_tags( $new_instance['margin_left'] ) : 5;
            $instance['frequency']          = ( ! empty( $new_instance['frequency'] ) ) ? strip_tags( $new_instance['frequency'] ) : 10;
            $instance['change_wavelength']  = ( ! empty( $new_instance['change_wavelength'] ) ) ? strip_tags( $new_instance['change_wavelength'] ) : 1;
            $instance['opacity_decay']      = ( ! empty( $new_instance['opacity_decay'] ) ) ? strip_tags( $new_instance['opacity_decay'] ) : 0.8;
            $instance['waves']              = ( ! empty( $new_instance['waves'] ) ) ? strip_tags( $new_instance['waves'] ) : 3;
            $instance['line_height_factor'] = ( ! empty( $new_instance['line_height_factor'] ) ) ? strip_tags( $new_instance['line_height_factor'] ) : 1.2;
            $instance['custom_title']       = ( isset( $new_instance['custom_title'] ) ) ? strip_tags( $new_instance['custom_title'] ) : '';

            return $instance;
        }

    }

}
