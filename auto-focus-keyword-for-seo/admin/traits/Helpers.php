<?php

namespace Pagup\AutoFocusKeyword\Traits;

use Pagup\AutoFocusKeyword\Core\Option;
trait Helpers
{
    public function post_types() : string {
        global $wpdb;
        $allowed_post_types = ( Option::check( 'post_types' ) ? Option::get( 'post_types' ) : [] );
        if ( in_array( 'product', $allowed_post_types ) ) {
            unset($allowed_post_types[array_search( 'product', $allowed_post_types )]);
        }
        // Create a string of placeholders and prepare the whole list of post types
        $placeholders = implode( ', ', array_fill( 0, count( $allowed_post_types ), '%s' ) );
        $post_types = $wpdb->prepare( $placeholders, $allowed_post_types );
        // $post_types is now a string ready to use in an IN clause
        return $post_types;
    }

    public function cpts( $excludes = [] ) {
        // All CPTs.
        $post_types = get_post_types( array(
            'public' => true,
        ), 'objects' );
        // remove Excluded CPTs from All CPTs.
        foreach ( $excludes as $exclude ) {
            unset($post_types[$exclude]);
        }
        $types = [];
        foreach ( $post_types as $post_type ) {
            $label = get_post_type_labels( $post_type );
            $types[$label->name] = $post_type->name;
        }
        return $types;
    }

    public function meta_key() : string {
        if ( class_exists( 'WPSEO_Meta' ) ) {
            $meta_key = '_yoast_wpseo_focuskw';
        } elseif ( class_exists( 'RankMath' ) ) {
            $meta_key = 'rank_math_focus_keyword';
        } elseif ( defined( 'SEOPRESS_VERSION' ) || function_exists( 'seopress_get_service' ) ) {
            $meta_key = '_seopress_analysis_target_kw';
        } elseif ( defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' ) ) {
            $meta_key = 'aioseo_table';
        } else {
            $meta_key = '';
        }
        return $meta_key;
    }

    public function uses_aioseo_focus_keyword_storage() : bool {
        return $this->meta_key() === 'aioseo_table';
    }

    public function focus_keyword_provider() : string {
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

    public function get_focus_keyword_value( $post_id ) : string {
        $post_id = (int) $post_id;

        if ( $post_id <= 0 ) {
            return '';
        }

        if ( $this->uses_aioseo_focus_keyword_storage() ) {
            global $wpdb;

            $table_name = $wpdb->prefix . 'aioseo_posts';
            $keyphrases = $wpdb->get_var( $wpdb->prepare(
                "SELECT keyphrases FROM {$table_name} WHERE post_id = %d LIMIT 1",
                $post_id
            ) );

            return $this->extract_aioseo_focus_keyword( $keyphrases );
        }

        $meta_key = $this->meta_key();

        if ( $meta_key === '' ) {
            return '';
        }

        return (string) get_post_meta( $post_id, $meta_key, true );
    }

    public function update_focus_keyword_value( $post_id, $focus_keyword ) : bool {
        $post_id = (int) $post_id;
        $focus_keyword = sanitize_text_field( $focus_keyword );

        if ( $post_id <= 0 || $focus_keyword === '' ) {
            return false;
        }

        if ( $this->uses_aioseo_focus_keyword_storage() ) {
            return $this->update_aioseo_focus_keyword( $post_id, $focus_keyword );
        }

        $meta_key = $this->meta_key();

        if ( $meta_key === '' ) {
            return false;
        }

        return (bool) update_post_meta( $post_id, $meta_key, $focus_keyword );
    }

    public function clear_focus_keyword_value( $post_id ) : bool {
        $post_id = (int) $post_id;

        if ( $post_id <= 0 ) {
            return false;
        }

        if ( $this->uses_aioseo_focus_keyword_storage() ) {
            return $this->update_aioseo_focus_keyword( $post_id, '' );
        }

        $meta_key = $this->meta_key();

        if ( $meta_key === '' ) {
            return false;
        }

        return (bool) delete_post_meta( $post_id, $meta_key );
    }

    public function aioseo_missing_focus_keyword_condition( $table_alias = 'ap' ) : string {
        $table_alias = preg_replace( '/[^A-Za-z0-9_]/', '', (string) $table_alias );

        if ( $table_alias === '' ) {
            $table_alias = 'ap';
        }

        return "({$table_alias}.keyphrases = '' OR {$table_alias}.keyphrases IS NULL OR {$table_alias}.keyphrases = '[]' OR {$table_alias}.keyphrases LIKE '{\"focus\":{\"keyphrase\":\"\"%')";
    }

    private function extract_aioseo_focus_keyword( $keyphrases ) : string {
        if ( empty( $keyphrases ) ) {
            return '';
        }

        $data = json_decode( (string) $keyphrases, true );

        if ( ! is_array( $data ) || empty( $data['focus']['keyphrase'] ) ) {
            return '';
        }

        return (string) $data['focus']['keyphrase'];
    }

    private function update_aioseo_focus_keyword( $post_id, $focus_keyword ) : bool {
        global $wpdb;

        $table_name = $wpdb->prefix . 'aioseo_posts';
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT id, keyphrases FROM {$table_name} WHERE post_id = %d LIMIT 1",
            $post_id
        ), ARRAY_A );

        $keyphrases = [];

        if ( isset( $row['keyphrases'] ) && ! empty( $row['keyphrases'] ) ) {
            $decoded = json_decode( (string) $row['keyphrases'], true );
            $keyphrases = is_array( $decoded ) ? $decoded : [];
        }

        if ( empty( $keyphrases['focus'] ) || ! is_array( $keyphrases['focus'] ) ) {
            $keyphrases['focus'] = [];
        }

        $keyphrases['focus']['keyphrase'] = $focus_keyword;

        if ( ! isset( $keyphrases['focus']['score'] ) ) {
            $keyphrases['focus']['score'] = 0;
        }

        if ( empty( $keyphrases['focus']['analysis'] ) || ! is_array( $keyphrases['focus']['analysis'] ) ) {
            $keyphrases['focus']['analysis'] = [];
        }

        if ( empty( $keyphrases['additional'] ) || ! is_array( $keyphrases['additional'] ) ) {
            $keyphrases['additional'] = [];
        }

        $now = current_time( 'mysql' );
        $payload = [
            'keyphrases' => wp_json_encode( $keyphrases ),
            'updated' => $now,
        ];

        if ( isset( $row['id'] ) ) {
            return false !== $wpdb->update(
                $table_name,
                $payload,
                [ 'id' => (int) $row['id'] ],
                [ '%s', '%s' ],
                [ '%d' ]
            );
        }

        return false !== $wpdb->insert(
            $table_name,
            [
                'post_id' => $post_id,
                'keyphrases' => $payload['keyphrases'],
                'created' => $now,
                'updated' => $now,
            ],
            [ '%d', '%s', '%s', '%s' ]
        );
    }

    /**
     * Get the list of blacklist URL's string from Options, converts it to an array, and use the array map function to convert each URL to ID.
     * 
     * @return array
     */
    public function blacklist() : array {
        $blacklist = ( Option::check( 'blacklist' ) ? Option::get( 'blacklist' ) : [] );
        if ( empty( $blacklist ) ) {
            return $blacklist;
        }
        $blacklist = array_map( 'intval', explode( ',', $blacklist ) );
        return $blacklist;
    }

    /**
     * Get list of items with id, title, url. set $keyword to true to get yoast focus keyword
     * 
     * @param array $ids
     * @param boolean $type
     * @return array $list
     */
    public function get_items( $ids, $type = false ) {
        $list = [];
        $i = 0;
        foreach ( $ids as $id ) {
            // Create Array of Objects
            $post_type = ( $type === true ? " (" . $this->post_type( $id ) . ")" : "" );
            $title = get_the_title( $id );
            $title = html_entity_decode( $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            if ( !empty( $title ) ) {
                $post = [
                    'value' => $id,
                    'label' => $title . $post_type,
                ];
                array_push( $list, $post );
            }
            $i++;
        }
        return $list;
    }

    /**
     * Get post type label from post type object
     * 
     * @param int $post_id
     * @return string
     */
    public function post_type( $post_id ) {
        $post_type_obj = get_post_type_object( get_post_type( $post_id ) );
        return $post_type_obj->labels->singular_name;
    }

}
