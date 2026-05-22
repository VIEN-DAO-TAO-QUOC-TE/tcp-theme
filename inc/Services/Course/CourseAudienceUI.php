<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseAudienceUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_audience_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_audience_title'] ?? ''));

        $items_raw = $data['tcp_course_audience_items'] ?? [];
        $items = $this->normalize_items($items_raw);

        if (empty($items)) {
            return;
        }

        ob_start();
?>
        <!-- TCP: audience start -->
        <section class="c-course-section c-course-audience" id="audience">
            <div class="c-course-audience__inner">

                <?php if ($eyebrow !== '' || $title !== ''): ?>
                    <header class="c-course-section__header c-course-audience__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-audience__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>

                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-audience__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($items)): ?>
                    <div class="c-course-audience__grid">
                        <?php foreach ($items as $card):
                            $card_title = trim((string) ($card['title'] ?? ''));
                            $card_desc  = trim((string) ($card['desc'] ?? ''));
                            $icon_id    = (int) ($card['icon_id'] ?? 0);

                            if ($card_title === '' && $card_desc === '' && $icon_id <= 0) continue;

                            $icon_html = '';
                            if ($icon_id > 0) {
                                // icon là image id theo JSON course
                                $icon_html = wp_get_attachment_image(
                                    $icon_id,
                                    'full',
                                    true,
                                    [
                                        'class' => 'c-course-audience__iconImg',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                );
                            }
                        ?>
                            <article class="c-course-audience__card">
                                <?php if ($icon_html !== ''): ?>
                                    <div class="c-course-audience__icon">
                                        <?php echo $icon_html; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="c-course-audience__icon c-course-audience__icon--placeholder" aria-hidden="true"></div>
                                <?php endif; ?>

                                <div class="c-course-audience__content">
                                    <?php if ($card_title !== ''): ?>
                                        <h3 class="c-course-audience__cardTitle"><?php echo esc_html($card_title); ?></h3>
                                    <?php endif; ?>

                                    <?php if ($card_desc !== ''): ?>
                                        <div class="c-course-audience__desc"><?php echo wp_kses_post(nl2br(esc_html($card_desc))); ?></div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <!-- TCP: audience end -->
<?php

        echo trim(ob_get_clean());
    }

    /**
     * Expect repeater rows:
     * [
     *   ['icon' => <id>, 'title' => '...', 'desc' => '...'],
     *   ...
     * ]
     */
    private function normalize_items($raw): array
    {
        if (empty($raw) || !is_array($raw)) return [];

        $out = [];

        foreach ($raw as $row) {
            if (!is_array($row)) continue;

            $icon = $row['icon'] ?? 0; // JSON course: icon return_format = id
            $icon_id = is_numeric($icon) ? (int) $icon : 0;

            $out[] = [
                'icon_id' => $icon_id,
                'title'   => (string) ($row['title'] ?? ''),
                'desc'    => (string) ($row['desc'] ?? ''),
            ];
        }

        return $out;
    }
}
