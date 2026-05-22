<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Services\CourseLoopMetaUI;

defined('ABSPATH') || exit;

final class CourseStickyHeaderUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        if (!is_product()) return;

        $product_id = $product->get_id();
        $title      = get_the_title($product_id);

        // Wrapper sticky header (ẩn mặc định, JS sẽ toggle .is-visible)
        echo '<div class="c-course-stickyHeader" data-course-sticky-header aria-hidden="true">';
        echo '  <div class="c-course-stickyHeader__inner container">';

        echo '    <div class="c-course-stickyHeader__title">' . esc_html($title) . '</div>';

        echo '    <div class="c-course-stickyHeader__meta">';

        if (method_exists(CourseLoopMetaUI::instance(), 'render_compact')) {
             CourseLoopMetaUI::instance()->render_compact($product, $product->get_id(), ['preset' => 'full_icon']);
        }
       
        echo '    </div>';

        echo '  </div>';
        echo '</div>';
    }
}
