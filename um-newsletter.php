<?php
/**
 * Plugin Name:     Ultimate Member - Newsletter
 * Description:     Extension to Ultimate Member for adding a checkbox for the Newsletter plugin subscription selection and editable at the User Account Page.
 * Version:         1.2.1 
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v3 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.9.1
 */
if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

class UM_Newsletter_Predefined_Field {

    public $meta_key = 'newsletter';

    function __construct( ) {

        add_filter( 'manage_users_columns',                  array( $this, 'um_manage_users_columns_newsletter' ));
        add_filter( 'manage_users_custom_column',            array( $this, 'um_manage_users_custom_column_newsletter' ), 10, 3 );
        add_filter( 'um_predefined_fields_hook',             array( $this, 'um_predefined_fields_newsletter' ), 10, 1 );
        add_filter( 'um_account_tab_general_fields',         array( $this, 'um_account_predefined_fields_newsletter' ), 10, 2 );
        add_filter( 'um_account_pre_updating_profile_array', array( $this, 'um_account_pre_updating_profile_newsletter' ), 10, 1 );
        add_filter( 'manage_users_sortable_columns',         array( $this, 'um_register_sortable_columns_newsletter' ), 10, 1 );
        add_action( 'pre_get_users',                         array( $this, 'um_pre_get_users_sort_columns_newsletter' ), 10, 1 );
        add_filter( 'um_registration_set_extra_data',        array( $this, 'um_registration_set_extra_data_newsletter' ), 10, 3 );
    }

    public function um_registration_set_extra_data_newsletter( $user_id, $args, $form_data ) {

        if ( isset( $form_data['mode'] ) && $form_data['mode'] == 'register' ) {
            if ( ! isset( $args[$this->meta_key] )) {
                update_user_meta( $user_id, $this->meta_key, array( esc_html__( 'No', 'ultimate-member' )) );
            }
        }

        return $args;
    }

    public function um_pre_get_users_sort_columns_newsletter( $query ) {

        if ( $query->get( 'orderby' ) == 'um_column_newsletter' ) {
             $query->set( 'orderby',  'meta_value' );
             $query->set( 'meta_key', $this->meta_key );
        }
    }

    public function um_register_sortable_columns_newsletter( $columns ) {

        $columns['um_column_newsletter'] = 'um_column_newsletter';

        return $columns;
    }

    public function um_manage_users_columns_newsletter( $columns ) {

        $columns['um_column_newsletter'] = esc_html__( 'Newsletter', 'ultimate-member' );

        return $columns;
    }

    public function um_manage_users_custom_column_newsletter( $value, $column_name, $user_id ) {

        if ( $column_name == 'um_column_newsletter' ) {

            um_fetch_user( $user_id );
            $value = um_user( $this->meta_key );

            $value = maybe_unserialize( $value );
            if ( is_array( $value ) && isset( $value[0] )) {
                $value = $value[0];
            } else {
                $value = esc_html__( 'Unknown', 'ultimate-member' );
            }
        }

        return $value;
    }

    public function um_predefined_fields_newsletter( $predefined_fields ) {

        $predefined_fields[$this->meta_key] = array(
            
                        'title'    => esc_html__( 'Newsletter', 'ultimate-member' ),
                        'metakey'  => $this->meta_key,
                        'type'     => 'checkbox',
                        'label'    => esc_html__( 'Newsletter', 'ultimate-member' ),
                        'required' => 0,
                        'public'   => 1,
                        'editable' => 1,
                        'options'  => array( esc_html__( 'Yes', 'ultimate-member' ), esc_html__( 'No', 'ultimate-member' )),
        );

        return $predefined_fields;
    }

    public function um_account_predefined_fields_newsletter( $args, $shortcode_args ) {

        $args .= ',' . $this->meta_key;
        $args = str_replace( ',single_user_password', '', $args ) . ',single_user_password';

        return $args;
    }

    public function um_account_pre_updating_profile_newsletter( $changes ) {

        if ( ! isset( $changes[$this->meta_key] )) {
            $changes[$this->meta_key] = array( esc_html__( 'No', 'ultimate-member' ));
        }

        return $changes;
    }
}

new UM_Newsletter_Predefined_Field();

