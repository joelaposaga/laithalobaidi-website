<?php
/**
 * Multiselect control class.
 *
 * @package    ButterBean
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2015-2016, Justin Tadlock
 * @link       https://github.com/justintadlock/butterbean
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Textarea control class.
 *
 * @since  1.0.0
 * @access public
 */
class ButterBean_Control_Multiselect extends ButterBean_Control {

    /**
     * The type of control.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'multiselect';

    public $l10n = array();

    /**
     * Creates a new control object.
     *
     * @since  1.0.0
     * @access public
     * @param  object  $manager
     * @param  string  $name
     * @param  array   $args
     * @return void
     */
    public function __construct( $manager, $name, $args = array() ) {
        parent::__construct( $manager, $name, $args );

        $this->l10n = wp_parse_args(
            $this->l10n,
            array(
                'all'      => esc_html__( 'All', 'butterbean' ),
                'selected'      => esc_html__( 'Selected', 'butterbean' ),
                'add_new'      => esc_html__( 'Add new', 'butterbean' ),
            )
        );
    }
    /**
     * Adds custom data to the json array. This data is passed to the Underscore template.
     *
     * @since  1.0.0
     * @access public
     * @return void
     */
    public function to_json() {
        parent::to_json();

        $value = $this->get_value();
        if(!empty($value)) {
            $value = explode(',', $value);
        }

        $this->json['l10n'] = $this->l10n;

        $this->json['value'] = $value;
    }
}
