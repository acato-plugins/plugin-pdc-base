<?php

namespace OWC\PDC\Base\RestAPI\ItemFields;

use OWC\PDC\Base\Support\CreatesFields;
use WP_Post;

class FAQField extends CreatesFields
{

    /**
     * Generate the FAQ field.
     *
     * @param WP_Post $post
     *
     * @return array
     */
    public function create(WP_Post $post): array
    {

        if ( ! class_exists('OWC_PDC_FAQ\Core\PostTypes\PdcItem') ) {
            return [];
        }

        return array_map(function($faq) {
            return [
                'question' => esc_attr($faq['pdc_faq_question']),
                'answer'   => $faq['pdc_faq_answer']
            ];
        }, $this->getFAQ($post));
    }

    /**
     * Get faqs of a post.
     *
     * @param WP_Post $post
     *
     * @return array
     */
    private function getFAQ(WP_Post $post)
    {
        return array_filter(get_post_meta($post->ID, '_owc_pdc_faq_group', true) ?: [], function($faq) {
            return ! empty($faq['pdc_faq_question']) && ! empty($faq['pdc_faq_answer']);
        });
    }

}