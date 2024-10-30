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
 * @since      0.1.0
 */

if ( ! class_exists( 'Creative_Tag_Cloud_Shortcode' ) ) {

    class Creative_Tag_Cloud_Shortcode {

        /**
         * Execute the shortcode
         *
         * @since    1.0
         */
        public static function creative_wave_tag_cloud( $atts ) {

            /**
             *     Contains the HTML code that will be echoed instead of the shortcode
             */
            $html = '';

            /**
             *     Extracting parameters from shortcode
             */
            extract( shortcode_atts( array(
                //'include'                 => '', // future use
                'taxonomy'           => 'post_tag',
                'div_id'             => 'creative_tag_cloud_' . uniqid(),
                'amount'             => 40,
                'width'              => '100%',
				'height'             => '800px',
				'hide_empty'		 => 1,
                'largest'            => 50,
                'smallest'           => 10,
                'color'              => 0,
                'margin_left'        => 5,
                'separator'          => '',
                'change_wavelength'  => 1,
                'opacity_decay'      => 0.8,
                'frequency'          => 10,
                'waves'              => 3,
                'line_height_factor' => 1.2,
                'tags_per_wave'      => 'auto',
                'custom_title'       => null,
            ), $atts ) );

            /**
             *     Prevent divisions by zero and other problems
             */

            if ( $waves < 1 ) {
                $waves = 1;
            }

            if ( $amount < 1 ) {
                $amount = 1;
            }

            if ( $smallest < 1 ) {
                $smallest = 1;
            }

            if ( $largest < $smallest ) {
                $largest = $smallest;
            }

            if ( $change_wavelength <= 0 ) {
                $change_wavelength = 1;
            }

            if ( $opacity_decay <= 0 ) {
                $opacity_decay = 0.8;
            }

            if ( $frequency < 1 ) {
                $frequency = 1;
            }

            if ( $line_height_factor <= 0 ) {
                $line_height_factor = 1;
            }

            if ( is_numeric( $tags_per_wave ) && $tags_per_wave < 1 ) {
                $tags_per_wave = 'auto';
            }

            $taxonomy_array = explode( ',', $taxonomy );

            $div_id = sanitize_title( $div_id );

            $svg_id = 'creative_tag_cloud_' . uniqid();

            /**
             * Retrieve the tags
             */
            $args = array(
                'hide_empty' => $hide_empty,
                'taxonomy'   => $taxonomy_array,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'amount'     => $amount,
            );

            $filters = Creative_Tag_Cloud_Tools::remove_all_filters( array('get_terms_orderby', 'get_terms', 'list_terms_exclusions' ) );

            $tags_obj = get_terms( $args );

            Creative_Tag_Cloud_Tools::restore_hooks( $filters );

            $tags = array();

            $total_tag_length = 0;

            if ( ! empty( $tags_obj ) && is_array( $tags_obj ) ) {

                foreach ( $tags_obj as $tag_obj ) {
                    $tags[] = array(
                        'term_id'     => $tag_obj->term_id,
                        'name'        => $tag_obj->name,
                        'count'       => $tag_obj->count,
                        'url'         => get_term_link( $tag_obj ),
                        'description' => $tag_obj->description,
                    );

                    $total_tag_length += mb_strlen( $tag_obj->name );
                }

                // $average_tag_length = $total_tag_length / count( $tags );

                $min_max = self::determine_min_max( $tags );

                $tags = self::group_tags_by_amount( $tags );

                $amount = count( $tags_obj );

                /**
                 *     Container for the tags
                 */
                $html .= '<div id="' . $div_id . '"><svg id="' . $svg_id . '" class="cm-wave-tags-container" style="width:' . $width . ';height:' . $height . ';" >';

                for ( $i = 0; $i < $waves; $i++ ) {

                    $html .= '<path id="' . $svg_id . '_path_' . $i . '" fill="none"  d=""/>';

                }

                /**
                 *     Initiate variables
                 */
                $text_path_content = array();

                $opacity = array();

                $tag_count = array();

                for ( $i = 0; $i < $waves; $i++ ) {

                    $text_path_content[$i] = '';
                    $opacity[$i]           = 1;
                    $tag_count[$i]         = 0;

                }

                $wave          = 0;
                $max_font_size = 0;
                $group_counter = 0;

                foreach ( $tags as $key_tag_group => $batch_of_tags ) {

                    $group_counter++;

                    /**
                     *     Prepare coloring by groups
                     */

                    if ( $color ) {

                        $color_class = ' creative-tag-cloud-color-' . $group_counter;

                    } else {

                        $color_class = '';

                    }

                    foreach ( $batch_of_tags['terms'] as $key_tag => $tag ) {

                        if ( is_numeric( $tags_per_wave ) && $tag_count[$wave] >= $tags_per_wave ) {

                            break;

                        }

                        $font_size = self::font_size( $tag['count'], $min_max['min'], $min_max['max'], $smallest, $largest );

                        if ( $font_size > $max_font_size ) {

                            $max_font_size = $font_size;

                        }

                        if ( ! empty( $custom_title ) ) {

                            $description = ! empty( $tag['description'] ) ? esc_html( $tag['description'] ) : '';

                            $title = preg_replace( "/(\{description\})/", $description, $custom_title );

                            $title = preg_replace( "/(\{count\})/", $tag['count'], $title );

                        } else {
                            // description and post count
                            $description = ! empty( $tag['description'] ) ? esc_html( $tag['description'] ) . ' ' : '';

                            $title = $description . '(' . $tag['count'] . ')';
                        }

                        /**
                         * Add a tag
                         */
                        $text_path_content[$wave] .= '
						<tspan dx=' . $margin_left . '  class="cm-wave-tag" fill-opacity="' . $opacity[$wave] . '" style=" opacity:' . $opacity[$wave] . '">
						<title>' . $title . '</title>
						<a href="' . $tag['url'] . '" class="cm-wave-tags-link' . $color_class . '" style="font-size:' . $font_size . 'px;">' .
                            $tag['name'] .
                            '</a>
						</tspan>';

                        /**
                         *     Add a separator
                         */

                        if ( ! empty( $separator ) ) {

                            $text_path_content[$wave] .= '
							<tspan dx=' . $margin_left . ' fill-opacity="' . $opacity[$wave] . '" class="' . $color_class . '" style="font-size:' . $font_size . 'px; opacity:' . $opacity[$wave] . '">' .
                                $separator .
                                '</tspan>';
                        }

                        /**
                         *     Reduce the opacity for this wave
                         */
                        $opacity[$wave] = round( $opacity[$wave] * $opacity_decay * 100 ) / 100;

                        $tag_count[$wave]++;

                        /**
                         *     Cycle through the waves
                         */
                        $wave++;

                        if ( $waves == $wave ) {

                            $wave = 0;

                        }

                    }

                }

                /**
                 *     Fill the waves with content and combine them
                 */

                for ( $i = 0; $i < $waves; $i++ ) {

                    $html .= '<text><textPath id="' . $svg_id . '_text_path_' . $i . '" xlink:href="#' . $svg_id . '_path_' . $i . '">' . $text_path_content[$i] . '</textPath></text>';

                }

                $html .= '
				</svg></div>';

                /**
                 *     Wait for DOM ready, otherwise width is somtimes not correctly determined.
                 */
                $html .= "<script>
				jQuery(document).ready(function(){
					chattyMangoBuildWaveTagCloud(
						'" . $svg_id . "',
						'" . $max_font_size . "',
						'" . $frequency . "',
						'" . $change_wavelength . "',
						'" . $waves . "',
						'" . $line_height_factor . "',
						'" . $tags_per_wave . "'
					);
				});
				</script>";
            }

            return $html;
        }

        /**
         * Execute the shortcode
         *
         * @since    1.0
         */
        public static function creative_spiral_tag_cloud( $atts ) {

            /**
             *     Contains the HTML code that will be echoed instead of the shortcode
             */
            $html = '';

            /**
             *     Extracting parameters from shortcode
             */
            extract( shortcode_atts( array(
                //'include'                 => '', // future use
                'taxonomy'           => 'post_tag',
                'div_id'             => 'creative_tag_cloud_' . uniqid(),
                'amount'             => 40,
                'width'              => '100%',
                'height'             => '800px',
				'hide_empty'		 => 1,
                'largest'            => 50,
                'smallest'           => 10,
                'color'              => 0,
                'margin_left'        => 5,
                'separator'          => '',
                'direction'          => 'ccw', // cw, ccw
                'start'              => 'top', // left, right, top, right or float [0,2pi]
                'cycles'             => 'auto', // auto or integer 1-15
                'line_height_factor' => 1.2,
                'custom_title'       => null,
                'reduce_factor'      => 1,
            ), $atts ) );

            if ( $smallest < 1 ) {
                $smallest = 1;
            }

            if ( $largest < $smallest ) {
                $largest = $smallest;
            }

            if ( $line_height_factor <= 0 ) {
                $line_height_factor = 1;
            }

            if ( $reduce_factor < 0.1 ) {
                $reduce_factor = 0.1;
            }

            $taxonomy_array = explode( ',', $taxonomy );

            $div_id = sanitize_title( $div_id );

            $svg_id = 'creative_tag_cloud_' . uniqid();

            /**
             *     Translate the start point into an angle
             */

            if ( strtolower( $direction ) == 'cw' ) {

                switch ( $start ) {
                case 'left':$start_angle = 0;
                    break;
                case 'right':$start_angle = pi();
                    break;
                case 'top':$start_angle = 0.5 * pi();
                    break;
                case 'bottom':$start_angle = 1.5 * pi();
                    break;
                default:$start_angle = ( is_numeric( $start ) ) ? $start : 0;
                    break;
                }

            } else {

                switch ( $start ) {
                case 'left':$start_angle = pi();
                    break;
                case 'right':$start_angle = 0;
                    break;
                case 'top':$start_angle = 1.5 * pi();
                    break;
                case 'bottom':$start_angle = 0.5 * pi();
                    break;
                default:$start_angle = ( is_numeric( $start ) ) ? $start : pi();
                    break;
                }

            }

            /**
             *     Retrieve the tags
             */
            $args = array(
                'hide_empty' => $hide_empty,
                'taxonomy'   => $taxonomy_array,
                'orderby'    => 'count',
                'order'      => 'DESC',
                'amount'     => $amount,
            );

            $filters = Creative_Tag_Cloud_Tools::remove_all_filters( array('get_terms_orderby', 'get_terms', 'list_terms_exclusions' ) );

            $tags_obj = get_terms( $args );

            Creative_Tag_Cloud_Tools::restore_hooks( $filters );

            $tags = array();

            $total_tag_length = 0;

            if ( ! empty( $tags_obj ) && is_array( $tags_obj ) ) {

                foreach ( $tags_obj as $tag_obj ) {
                    $tags[] = array(
                        'term_id'     => $tag_obj->term_id,
                        'name'        => $tag_obj->name,
                        'count'       => $tag_obj->count,
                        'url'         => get_term_link( $tag_obj ),
                        'description' => $tag_obj->description,
                    );

                    $total_tag_length += mb_strlen( $tag_obj->name );
                }

                $average_tag_length = $total_tag_length / count( $tags );

                $min_max = self::determine_min_max( $tags );

                $tags = self::group_tags_by_amount( $tags );

                $amount = count( $tags_obj );

                /**
                 *     Container for the tags
                 */
                $html .= '<div id="' . $div_id . '"><svg id="' . $svg_id . '" class="cm-spiral-tags-container" style="width:' . $width . ';height:' . $height . ';" >
				<path id="' . $svg_id . '_path" fill="none"  d=""/>
				<text>
				<textPath id="' . $svg_id . '_text_path" xlink:href="#' . $svg_id . '_path">';

                $max_font_size = 0;

                $total_font_size = 0;

                $group_counter = 0;

// $total_length = 0;

                foreach ( $tags as $batch_of_tags ) {

                    $group_counter++;

                    if ( $color ) {

                        $color_class = ' creative-tag-cloud-color-' . $group_counter;

                    } else {

                        $color_class = '';

                    }

                    $tspan_class = 'cm-spiral-tags';

                    if ( strtolower( $direction ) == 'cw' ) {

                        $tspan_class .= ' cm-spiral-tags-cw';

                    } elseif ( strtolower( $direction ) == 'ccw' ) {

                        $tspan_class .= ' cm-spiral-tags-ccw';

                    }

                    $tag_index = 0;

                    /**
                     *     Add all tags inside tspans
                     */
                    foreach ( $batch_of_tags['terms'] as $tag ) {

                        $font_size = self::font_size( $tag['count'], $min_max['min'], $min_max['max'], $smallest, $largest );

                        if ( $font_size > $max_font_size ) {

                            $max_font_size = $font_size;

                        }

                        if ( ! empty( $custom_title ) ) {

                            $description = ! empty( $tag['description'] ) ? esc_html( $tag['description'] ) : '';

                            $title = preg_replace( "/(\{description\})/", $description, $custom_title );

                            $title = preg_replace( "/(\{count\})/", $tag['count'], $title );

                        } else {
                            // description and post count
                            $description = ! empty( $tag['description'] ) ? esc_html( $tag['description'] ) . ' ' : '';

                            $title = $description . '(' . $tag['count'] . ')';
                        }

                        if ( 0 == $tag_index ) {

                            $dx = 0;

                        } else {

                            $dx = $margin_left;

                        }

                        $html .= '
						<tspan dx=' . $dx . ' class="' . $tspan_class . '">
						<title>' . $title . '</title>
						<a href="' . $tag['url'] . '" class="cm-spiral-tags-link' . $color_class . '" style="font-size:' . $font_size . 'px;">' .
                            $tag['name'] .
                            '</a>
						</tspan>';

// $total_length += mb_strlen( $tag['name'] ) * $font_size + $margin_left;

                        /**
                         *     Add a separator, if required
                         */
                        if ( ! empty( $separator ) && $tag_index < count( $tags ) ) {

                            $html .= '
							<tspan dx=' . $margin_left . ' class="' . $color_class . '" style="font-size:' . $font_size . 'px;">' .
                                $separator .
                                '</tspan>';

                            // $total_length += mb_strlen( $separator ) * $font_size + $margin_left;

                        }

                        $total_font_size += $font_size;

                        $tag_index++;

                    }

                }

                $html .= '</textPath>
				</text>
				</svg></div>';

                /**
                 *     Wait for DOM ready, otherwise width is somtimes not correctly determined.
                 */
                $html .= "<script>
				jQuery(document).ready(function(){
					chattyMangoBuildSpiralTagCloud(
						'" . $svg_id . "',
						'" . $max_font_size . "',
						'" . round( $total_font_size / $amount ) . "',
						'" . $direction . "',
						'" . $start_angle . "',
						'" . $cycles . "',
						'" . $line_height_factor . "',
						'" . $reduce_factor . "'
					);

				});
				</script>";
            }

            return $html;
        }

        /**
         * Helper for grouping tags and calculating the weight for sizing the font
         *
         * @param  array   $terms
         * @return array
         */
        static function group_tags_by_amount( $terms ) {
            $grouped_terms = array();

            /**
             *     We need an ascending index so that the object in JavaScript won't be in reverse order
             */
            $tag_index = 0;

            $max_count = 1;
// != 0 to avoid division by zero

            foreach ( $terms as $term ) {

                if ( ! isset( $grouped_terms[$tag_index] ) || $grouped_terms[$tag_index]['count'] != $term['count'] ) {

                    $tag_index++;

                    $grouped_terms[$tag_index] = array(
                        'count' => $term['count'],
                        'terms' => array(),
                    );

                    if ( $term['count'] > $max_count ) {

                        $max_count = $term['count'];

                    }

                }

                $grouped_terms[$tag_index]['terms'][] = $term;

            }

            /**
             *     We want to normalize for maximum weight = 1
             */
            $normalization_factor = 1 / $max_count;

            /**
             *     Calculate the weight, 2 digits accuracy
             */
            foreach ( $grouped_terms as $key => $group ) {

                $grouped_terms[$key]['weight'] = round( 100 * $grouped_terms[$key]['count'] * $normalization_factor ) / 100;

            }

            return $grouped_terms;

        }

        /**
         * Calculates the font size for the cloud tag for a particular tag ($min, $max and $size with same unit, e.g. pt.)
         *
         * @param  int   $count
         * @param  int   $min
         * @param  int   $max
         * @param  int   $smallest
         * @param  int   $largest
         * @return int
         */
        static function font_size( $count, $min, $max, $smallest, $largest ) {

            if ( $max > $min ) {

                $size = round(  ( $count - $min ) * ( $largest - $smallest ) / ( $max - $min ) + $smallest );

            } else {

                $size = round( $smallest );

            }

            return $size;

        }

        /*
         *  find minimum and maximum of quantity of posts for each tag
         *
         * @param
         * @return array $min_max
         */
        static function determine_min_max( $tags, $amount = 0 ) {

            $min_max = array(
                'min' => 0,
                'max' => 0,
            );

            $count = 0;

            if ( empty( $tags ) || ! is_array( $tags ) ) {

                return $min_max;

            }

            foreach ( $tags as $tag ) {

                $count++;

                if ( 0 != $amount && $amount > $count ) {

                    break;

                }

                if ( 0 == $min_max['min'] || $tag['count'] < $min_max['min'] ) {

                    $min_max['min'] = $tag['count'];

                }

                if ( $tag['count'] > $min_max['max'] ) {

                    $min_max['max'] = $tag['count'];

                }

            }

            return $min_max;

        }

    }

}
