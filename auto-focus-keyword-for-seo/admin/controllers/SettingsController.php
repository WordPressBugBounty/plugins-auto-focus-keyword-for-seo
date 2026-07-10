<?php

namespace Pagup\AutoFocusKeyword\Controllers;

use \Pagup\AutoFocusKeyword\Core\Option;
use \Pagup\AutoFocusKeyword\Core\Plugin;
use \Pagup\AutoFocusKeyword\Core\Request;
use \Pagup\AutoFocusKeyword\Traits\Helpers;

class SettingsController 
{
    use Helpers;

    public $batch_size = 20;

    public function add_settings()
    {
        add_menu_page (
			__( 'Auto Focus Keyword', 'auto-focus-keyword-for-seo' ),
			__( 'Auto Focus Keyword','auto-focus-keyword-for-seo' ),
			'manage_options',
			AFKW_NAME,
			array( &$this, 'page' ),
			'dashicons-yes-alt'
		);
    }

    public function page()
    {
        global $wpdb;

        $safe = [ "allow", "settings", "logs", "faq" ];
        $updated = "";

        if (isset($_POST['update'])) {
            // check if user is authorised
            if ( function_exists( 'current_user_can' ) && !current_user_can( 'manage_options' ) ) {
                die( 'Sorry, not allowed...' );
            }
        
            check_admin_referer( 'afkw__settings', 'afkw__nonce' );

            $post_types = [];
            if ( isset( $_POST['post_types'] ) && is_array( $_POST['post_types'] ) ) {
                $post_types = array_map( 'sanitize_key', wp_unslash( $_POST['post_types'] ) );
            }

            $blacklist = isset( $_POST['blacklist'] )
                ? trim( sanitize_textarea_field( wp_unslash( $_POST['blacklist'] ) ) )
                : '';

            $disable_auto_sync = isset( $_POST['disable_auto_sync'] )
                ? Request::safe( sanitize_key( wp_unslash( $_POST['disable_auto_sync'] ) ), $safe )
                : null;

            $remove_settings = isset( $_POST['remove_settings'] )
                ? Request::safe( sanitize_key( wp_unslash( $_POST['remove_settings'] ) ), $safe )
                : null;
        
            $options = [
                'post_types' => $post_types,
                'blacklist' => $blacklist,
                'disable_auto_sync' => $disable_auto_sync,
                'remove_settings' => $remove_settings,
            ];
            
            update_option( 'afkw_auto-focus-keyword-for-seo', $options );
        
            // update options
            $updated = '<div class="afkw-alert afkw-success" style="display: block; width: 100%;margin: 20px 0 0;"><strong>' . esc_html__( 'Settings saved.', 'auto-focus-keyword-for-seo' ) . '</strong></div>' ;
        }

        $options = new Option;

        //Set active class for navigation tabs
        $active_tab = ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $safe ) ? sanitize_key($_GET['tab']) : 'settings' );

        $get_pro = sprintf( wp_kses( __( '<a href="%s">Get Pro version</a> to enable', 'auto-focus-keyword-for-seo' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( 'admin.php?page='.AFKW_NAME.'-pricing' ) );

        $focus_keyword_coverage = $this->get_focus_keyword_coverage();
        $total_items_require_sync = [
            'pages' => (int) $focus_keyword_coverage['pages'],
            'items' => (int) $focus_keyword_coverage['missing'],
        ];
        $nonce = wp_create_nonce( 'autokeywords' );

        $sync_logs = get_option('afkw_autokeyword_logs');

        if (is_array($sync_logs)) {
            // Sort the array by the 'updated_at' field
            usort($sync_logs, function($a, $b) {
                return $b['updated_at'] <=> $a['updated_at'];
            });
        } else {
            $sync_logs = [];
        }

        $allowed_post_types = Option::check('post_types') ? Option::get('post_types') : [];
        $posts = $this->get_items( get_posts(array(
            'post_type' => $allowed_post_types,
            'orderby'   => 'title',
            'order'   => 'ASC',
            'fields' => 'ids',
            'numberposts' => -1
        )), true);
        
        wp_localize_script( 'afkw__script', 'data', array(
            'total_pages_and_items' => $total_items_require_sync,
            'batch_size' => $this->batch_size,
            'syncDate' => get_option( "afkw_autokeyword_sync" ),
            'sync_logs' => $sync_logs,
            'posts' => $posts,
            'blacklist' => $this->blacklist(),
            'focus_keyword_coverage' => $focus_keyword_coverage,
            'nonce' => $nonce,
        ));

        // var_dump($options->all());
        // var_dump($this->blacklist());

        $post_types = $this->cpts( ['attachment'] );
        $supported_seo_plugin = $this->meta_key() !== '';
        
        if( $active_tab == 'settings' ) {

            return Plugin::view('settings', compact('active_tab', 'updated', 'options', 'total_items_require_sync', 'post_types', 'get_pro', 'supported_seo_plugin'));

        }

        if ( $active_tab == 'logs' ) {

            return Plugin::view("logs", compact('active_tab'));

        }

        if ( $active_tab == 'faq' ) {

            return Plugin::view("faq", compact('active_tab'));

        }
    }

    public function get_total_pages_and_items(): array
    {
        $coverage = $this->get_focus_keyword_coverage();

        return [
            'pages' => (int) $coverage['pages'],
            'items' => (int) $coverage['missing'],
        ];
    }

    public function get_focus_keyword_coverage(): array
    {
        global $wpdb;

        $meta_key = $this->meta_key();
        $provider = $this->focus_keyword_provider();

        if ( $meta_key === '') {
            return [
                'pages' => (int) 0,
                'total' => (int) 0,
                'covered' => (int) 0,
                'missing' => (int) 0,
                'coverage_percent' => (int) 0,
                'seo_plugin' => '',
                'supported' => false,
            ];
        }

        $exclude = $this->blacklist();

        if (!empty($exclude)) {
            $exclude = array_filter($exclude, 'is_numeric');
            $exclude_ids = array_map(function($id) {
                return (int) $id;
            }, $exclude);
            $exclude_ids_placeholder = implode(', ', array_fill(0, count($exclude_ids), '%d'));
            $exclude_condition = $wpdb->prepare("AND p.ID NOT IN ({$exclude_ids_placeholder})", ...$exclude_ids);
        } else {
            $exclude_condition = "";
        }

        $post_types = $this->post_types();

        if ( trim( $post_types ) === '' ) {
            return [
                'pages' => (int) 0,
                'total' => (int) 0,
                'covered' => (int) 0,
                'missing' => (int) 0,
                'coverage_percent' => (int) 0,
                'seo_plugin' => $provider,
                'supported' => true,
            ];
        }

        if ( $this->uses_aioseo_focus_keyword_storage() ) {
            $aioseo_posts_table = $wpdb->prefix . 'aioseo_posts';
            $missing_condition = $this->aioseo_missing_focus_keyword_condition( 'ap' );

            $row = $wpdb->get_row("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN {$missing_condition} THEN 1 ELSE 0 END) AS missing
                FROM {$wpdb->posts} p
                LEFT JOIN {$aioseo_posts_table} ap
                ON p.ID = ap.post_id
                WHERE p.post_type IN ($post_types)
                AND p.post_status = 'publish'
                AND p.post_title != ''
                {$exclude_condition}
            ", ARRAY_A);
        } else {
            $row = $wpdb->get_row($wpdb->prepare("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN pm.meta_key IS NULL OR pm.meta_value = '' THEN 1 ELSE 0 END) AS missing
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm
                ON p.ID = pm.post_id AND pm.meta_key = %s
                WHERE p.post_type IN ($post_types)
                AND p.post_status = 'publish'
                AND p.post_title != ''
                {$exclude_condition}
            ", $meta_key), ARRAY_A);
        }

        $total = isset( $row['total'] ) ? (int) $row['total'] : 0;
        $missing = isset( $row['missing'] ) ? (int) $row['missing'] : 0;
        $covered = max( 0, $total - $missing );
        $coverage_percent = $total > 0 ? (int) round( ( $covered / $total ) * 100 ) : 100;
        $totalPages = ceil($missing / $this->batch_size); // Calculate the total number of pages

        return [
            'pages' => (int) $totalPages,
            'total' => (int) $total,
            'covered' => (int) $covered,
            'missing' => (int) $missing,
            'coverage_percent' => (int) $coverage_percent,
            'seo_plugin' => $provider,
            'supported' => true,
        ];
    }

    private function focus_keyword_provider(): string
    {
        if ( class_exists( 'WPSEO_Meta' ) ) {
            return 'Yoast SEO';
        }

        if ( class_exists( 'RankMath' ) ) {
            return 'Rank Math';
        }

        if ( defined( 'SEOPRESS_VERSION' ) || function_exists( 'seopress_get_service' ) ) {
            return 'SEOPress';
        }

        if ( defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' ) ) {
            return 'All in One SEO';
        }

        return '';
    }

    
}
