<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseFaqUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_faq_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_faq_title'] ?? ''));

        $mode = $data['tcp_course_faq_mode'] ?? 'inline';

        $items = ($mode === 'relationship')
            ? $this->get_items_from_relationship($data)
            : $this->get_items_from_inline($data);

        if (empty($items)) {
            return;
        }

        // Build Flatsome accordion shortcode content
        $accordion_shortcode = $this->build_flatsome_accordion_shortcode($items);

        ob_start();
        ?>
        <!-- TCP: faq start -->
        <section class="c-course-section c-course-faq" id="faq">
          <div class="c-course-faq__inner">

            <?php if ($eyebrow !== '' || $title !== ''): ?>
              <header class="c-course-section__header c-course-faq__header">
                <?php if ($eyebrow !== ''): ?>
                  <div class="c-course-section__eyebrow c-course-faq__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <?php endif; ?>

                <?php if ($title !== ''): ?>
                  <h2 class="c-course-section__title c-course-faq__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
              </header>
            <?php endif; ?>

            <?php if (!empty($items)): ?>
              <div class="c-course-faq__accordion">
                <?php
                  // Flatsome shortcode output
                  echo do_shortcode($accordion_shortcode);
                ?>
              </div>
            <?php endif; ?>

          </div>
        </section>
        <!-- TCP: faq end -->
        <?php

        echo trim(ob_get_clean());
    }

    /**
     * Inline mode: repeater on product
     * items[]: question, answer (wysiwyg)
     */
    private function get_items_from_inline(array $data): array
    {
        $rows = $data['tcp_course_faq_items'] ?? [];
        if (empty($rows) || !is_array($rows)) return [];

        $out = [];

        foreach ($rows as $row) {
            if (!is_array($row)) continue;

            $q = trim((string) ($row['question'] ?? ''));
            $a = (string) ($row['answer'] ?? '');

            if ($q === '' && trim(wp_strip_all_tags($a)) === '') continue;

            $out[] = [
                'question' => $q,
                // answer là wysiwyg => giữ HTML có kiểm soát
                'answer'   => wp_kses_post($a),
            ];
        }

        return $out;
    }

    /**
     * Relationship mode: post_object multiple
     * Support both ID and WP_Post.
     *
     * Fallback mapping:
     * - question: faq_question | question | post_title
     * - answer:   faq_answer   | answer   | post_content
     */
    private function get_items_from_relationship(array $data): array
    {
        $refs = $data['tcp_course_faq_ref'] ?? [];
        if (empty($refs)) return [];

        if (!is_array($refs)) $refs = [$refs];

        $out = [];

        foreach ($refs as $ref) {
            $post_id = 0;

            if (is_object($ref) && isset($ref->ID)) $post_id = (int) $ref->ID;
            else $post_id = (int) $ref;

            if ($post_id <= 0) continue;

            $q = $this->acf_first_string($post_id, ['faq_question', 'question']);
            if ($q === '') $q = trim((string) get_the_title($post_id));

            $a = $this->acf_first_html($post_id, ['faq_answer', 'answer']);
            if ($a === '') {
                $a = (string) get_post_field('post_content', $post_id);
                $a = wp_kses_post(apply_filters('the_content', $a));
            }

            if ($q === '' && trim(wp_strip_all_tags($a)) === '') continue;

            $out[] = [
                'question' => $q,
                'answer'   => $a,
            ];
        }

        return $out;
    }

    private function build_flatsome_accordion_shortcode(array $items): string
    {
        // auto_open="true": mở item đầu
        $shortcode  = '[accordion auto_open="true"]';

        foreach ($items as $it) {
            $q = trim((string) ($it['question'] ?? ''));
            $a = (string) ($it['answer'] ?? '');

            if ($q === '' && trim(wp_strip_all_tags($a)) === '') continue;

            // Flatsome shortcode attribute cần escape quote
            $q_attr = esc_attr($q);

            $shortcode .= '[accordion-item title="' . $q_attr . '"]';
            // answer đã wp_kses_post từ upstream
            $shortcode .= $a;
            $shortcode .= '[/accordion-item]';
        }

        $shortcode .= '[/accordion]';

        return $shortcode;
    }

    private function acf_first_string(int $post_id, array $keys): string
    {
        if (!function_exists('get_field')) return '';

        foreach ($keys as $k) {
            $v = get_field($k, $post_id);
            if (is_string($v)) {
                $v = trim($v);
                if ($v !== '') return $v;
            }
        }

        return '';
    }

    private function acf_first_html(int $post_id, array $keys): string
    {
        if (!function_exists('get_field')) return '';

        foreach ($keys as $k) {
            $v = get_field($k, $post_id);
            if (is_string($v)) {
                $v = trim($v);
                if ($v !== '') return wp_kses_post($v);
            }
        }

        return '';
    }
}
