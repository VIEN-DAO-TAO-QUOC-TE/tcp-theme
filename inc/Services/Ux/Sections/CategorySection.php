<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class CategorySection
{
    public static function render(array $data): string
    {
        $parentCategoryId = (int) ($data['parentCategoryId'] ?? 0);
        
        if (!$parentCategoryId) {
            return '';
        }

        // Get child categories
        $childCategories = get_terms([
            'taxonomy' => 'category',
            'parent' => $parentCategoryId,
            'hide_empty' => false,
            'orderby' => 'term_order',
            'order' => 'ASC',
        ]);

        if (is_wp_error($childCategories) || empty($childCategories)) {
            return '';
        }

        $out = '';
        $out .= '[section label="category-grid" class="category-grid c-section" style="padding-top:0px;"]';
        $out .= '[row style=""][col span__sm="12"]';
        $out .= '[row_inner v_align="equal" class="category-grid__grid"]';

        foreach ($childCategories as $category) {
            $categoryLink = get_category_link($category->term_id);
            $categoryImage = get_field('category_featured_image', $category->taxonomy . '_' . $category->term_id);
            $categoryImage = !empty($categoryImage) ? (int) $categoryImage : 0;
            $categoryDesc = (string) ($category->description ?? '');
            $categoryCount = max(0, (int) ($category->count ?? 0));
            $categoryMeta = $categoryCount > 0
                ? sprintf(__('%s + Bài viết', 'tcp-theme'), number_format_i18n($categoryCount))
                : __('Khám phá ngay', 'tcp-theme');

            $out .= '[col_inner span="4" span__md="4" span__sm="12" class="category-grid__card-col"]';
            $out .= '[ux_image_box style="overlay" img="' . $categoryImage . '" text_align="left" class="c-media-card"]';
            $out .= '[ux_text class="c-media-card__title"]<h4>' . esc_html($category->name) . '</h4>[/ux_text]';
            
            if (!empty($categoryDesc)) {
                $out .= '[ux_text class="c-media-card__desc"]' . esc_html($categoryDesc) . '[/ux_text]';
            }

            $out .= '[ux_text class="c-media-card__meta"]' . esc_html($categoryMeta) . '[/ux_text]';
            
            $out .= '[button text="Khám phá ngay" radius="10" expand="true" class="category-grid__button" link="' . esc_attr($categoryLink) . '"]';
            $out .= '[/ux_image_box]';
            $out .= '[/col_inner]';
        }

        $out .= '[/row_inner][/col][/row][/section]';

        return do_shortcode($out);
    }
}
