<?php
/**
 * Adds link fields to the output.
 */

namespace OWC\PDC\Base\RestAPI\ItemFields;

use WP_Post;
use OWC\PDC\Base\Support\CreatesFields;

/**
 * Adds link fields to the output.
 */
class LinksField extends CreatesFields
{

    /**
     * Generate the links field.
     *
     * @param WP_Post $post
     *
     * @return array
     */
    public function create(WP_Post $post): array
    {
        return array_map(function ($link) {
            return [
                'title' => esc_attr(strip_tags($link['pdc_links_title'])),
                'url'   => esc_url($link['pdc_links_url'])
            ];
        }, $this->getLinks($post));
    }

    /**
     * Get links of a post, if URL & title are present.
     *
     * @param WP_Post $post
     *
     * @return array
     */
    private function getLinks(WP_Post $post)
    {
        return array_filter(get_post_meta($post->ID, '_owc_pdc_links_group', true) ?: [], function ($link) {
            return ! empty($link['pdc_links_url']) && ! empty($link['pdc_links_title']);
        });
    }

}