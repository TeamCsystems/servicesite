<?php
/**
 * Truelysell Elementor Address Box class.
 *
 * @category   Class
 * @package    ElementorAwesomesauce
 * @subpackage WordPress
 * @author     Ben Marshall <me@benmarshall.me>
 * @copyright  2020 Ben Marshall
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://www.benmarshall.me/build-custom-elementor-widgets/,
 *             Build Custom Elementor Widgets)
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorTruelysell\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class Alertbox extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'truelysell-alert-box';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Truelysell Alert box', 'truelysell_elementor' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-alert';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'truelysell' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	// 	'latitude' 	=> '', 
		 	// 'longitude' 	=> '', 
		 	// 'background' => '',
	   //      'from_vs'  	=> '',
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'truelysell_elementor' ),
			)
		);
		$this->add_control(
			'content',
			[
				'label' => __( 'Message', 'truelysell_elementor'),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 5,
				'placeholder' => __( 'Enter your notification text', 'truelysell_elementor' ),
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Notice type tag ', 'truelysell_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'success',
				'options' => [
					'success' 	=> __( 'Success', 'truelysell_elementor' ),
					'error' 	=> __( 'Error', 'truelysell_elementor' ),
					'warning' 	=> __( 'Warning', 'truelysell_elementor' ),
					'notice' 	=> __( 'Notice', 'truelysell_elementor' ),
					
				],
			]
		);


		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display(); ?>
		<div class="notification closeable <?php echo esc_attr($settings['type']); ?>"><p><?php echo esc_attr($settings['content']); ?></p><a class="close" href="#"></a></div>
		<?php
	}

	protected function _content_template() {
		?>
		
		<div class="notification closeable {{{ settings.type }}}"><p>{{{ settings.content }}}</p><a class="close" href="#"></a></div>
		
		<?php
	}


}