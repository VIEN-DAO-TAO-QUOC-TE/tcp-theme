<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseTestimonialsUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_testimonials_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_testimonials_title'] ?? ''));

        $mode = $data['tcp_course_testimonials_mode'] ?? 'inline';

        $items = ($mode === 'relationship')
            ? $this->get_items_from_relationship($data)
            : $this->get_items_from_inline($data);

        if (empty($items)) {
            return;
        }

        ob_start();
?>
        <!-- TCP: testimonials start -->
        <section class="c-course-section c-course-testimonials" id="testimonials">
            <div class="c-course-testimonials__inner">

                <?php if ($eyebrow !== '' || $title !== ''): ?>
                    <header class="c-course-section__header c-course-testimonials__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-testimonials__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>

                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-testimonials__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($items)): ?>
                    <div class="c-course-testimonials__list">
                        <?php foreach ($items as $t):
                            $args = [
                                'quote'  => (string) ($t['quote'] ?? ''),
                                'name'   => (string) ($t['name'] ?? ''),
                                'role'   => (string) ($t['role'] ?? ''),
                                'avatar' => (string) ($t['avatar'] ?? ''),
                            ];

                            // Skip nếu trống hết
                            if (trim($args['quote']) === '' && trim($args['name']) === '' && trim($args['role']) === '') continue;

                            get_template_part('loops/c-testimonial-card', null, $args);
                        endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <!-- TCP: testimonials end -->
<?php

        echo trim(ob_get_clean());
    }

    /**
     * Mode: inline (repeater on product)
     * Expect rows: quote, name, role, avatar (id/url/false)
     */
    private function get_items_from_inline(array $data): array
    {
        $rows = $data['tcp_course_testimonials'] ?? [];
        if (empty($rows) || !is_array($rows)) return [];

        $out = [];

        foreach ($rows as $row) {
            if (!is_array($row)) continue;

            $avatar = $row['avatar'] ?? '';
            $avatar_url = $this->normalize_avatar($avatar);

            $out[] = [
                'quote'  => (string) ($row['quote'] ?? ''),
                'name'   => (string) ($row['name'] ?? ''),
                'role'   => (string) ($row['role'] ?? ''),
                'avatar' => $avatar_url,
            ];
        }

        return $out;
    }

    /**
     * Mode: relationship (post_object multiple)
     * Support both ID and WP_Post.
     * Fields fallback order:
     * - quote: testimonial_quote | quote | post_content
     * - name:  testimonial_name  | name  | post_title
     * - role:  testimonial_role  | role  | ''
     * - avatar: testimonial_avatar (id/url) | avatar | featured image url
     */
    private function get_items_from_relationship(array $data): array
    {
        $refs = $data['tcp_course_testimonials_ref'] ?? [];
        if (empty($refs)) return [];

        // ACF có thể trả array ID hoặc array WP_Post
        if (!is_array($refs)) {
            $refs = [$refs];
        }

        $out = [];

        foreach ($refs as $ref) {
            $post_id = 0;

            if (is_object($ref) && isset($ref->ID)) $post_id = (int) $ref->ID;
            else $post_id = (int) $ref;

            if ($post_id <= 0) continue;

            $quote = $this->acf_first_string($post_id, ['review_content', 'quote']);
            if ($quote === '') {
                $quote = trim((string) get_post_field('post_content', $post_id));
            }

            $name = $this->acf_first_string($post_id, ['review_student_name', 'name']);
            if ($name === '') {
                $name = trim((string) get_the_title($post_id));
            }

            $role = $this->acf_first_string($post_id, ['review_student_title', 'role']);

            // avatar: try ACF first, then featured image
            $avatar_raw = $this->acf_first_value($post_id, ['review_avatar', 'avatar']);
            $avatar_url = $this->normalize_avatar($avatar_raw);

            if ($avatar_url === '') {
                $thumb_id = (int) get_post_thumbnail_id($post_id);
                if ($thumb_id > 0) {
                    $avatar_url = wp_get_attachment_image_url($thumb_id, 'thumbnail') ?: '';
                }
            }

            $out[] = [
                'quote'  => $quote,
                'name'   => $name,
                'role'   => $role,
                'avatar' => $avatar_url,
            ];
        }

        return $out;
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

    private function acf_first_value(int $post_id, array $keys)
    {
        if (!function_exists('get_field')) return null;

        foreach ($keys as $k) {
            $v = get_field($k, $post_id);
            if (!empty($v)) return $v;
        }
        return null;
    }

    /**
     * Avatar input can be:
     * - attachment ID
     * - URL string
     * - array (ACF image array)
     * - empty/false
     */
    private function normalize_avatar($avatar): string
    {
        if (empty($avatar)) return '';

        // ID
        if (is_numeric($avatar)) {
            return wp_get_attachment_image_url((int) $avatar, 'thumbnail') ?: '';
        }

        // URL string
        if (is_string($avatar)) {
            return trim($avatar);
        }

        // ACF image array
        if (is_array($avatar)) {
            if (!empty($avatar['url']) && is_string($avatar['url'])) return trim($avatar['url']);
            if (!empty($avatar['ID']) && is_numeric($avatar['ID'])) {
                return wp_get_attachment_image_url((int) $avatar['ID'], 'thumbnail') ?: '';
            }
        }

        return '';
    }
}
