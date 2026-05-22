<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseMicroUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_micro_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_micro_title'] ?? ''));
        $note    = trim((string) ($data['tcp_course_micro_note'] ?? ''));

        $items_raw = $data['tcp_course_micro_items'] ?? [];
        $items = $this->normalize_items($items_raw);

        if (empty($items)) {
            return;
        }

        ob_start();
?>
        <!-- TCP: micro learning start -->
        <section class="c-course-section c-course-micro" id="micro-learning">
            <div class="c-course-micro__inner">

                <?php if ($eyebrow !== '' || $title !== ''): ?>
                    <header class="c-course-section__header c-course-micro__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-micro__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>

                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-micro__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($items)): ?>
                    <div class="c-course-micro__grid">
                        <?php foreach ($items as $card):
                            $card_title = trim((string) ($card['title'] ?? ''));
                            $card_desc  = trim((string) ($card['desc'] ?? ''));
                            $icon_id    = (int) ($card['icon_id'] ?? 0);

                            if ($card_title === '' && $card_desc === '' && $icon_id <= 0) continue;

                            $icon_html = '';
                            if ($icon_id > 0) {
                                $icon_html = wp_get_attachment_image(
                                    $icon_id,
                                    'full',
                                    true,
                                    [
                                        'class' => 'c-course-micro__iconImg',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                );
                            }
                        ?>
                            <article class="c-course-micro__card">
                                <?php if ($icon_html !== ''): ?>
                                    <div class="c-course-micro__icon">
                                        <?php echo $icon_html; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="c-course-micro__content">
                                    <?php if ($card_title !== ''): ?>
                                        <h3 class="c-course-micro__cardTitle"><?php echo esc_html($card_title); ?></h3>
                                    <?php endif; ?>

                                    <?php if ($card_desc !== ''): ?>
                                        <div class="c-course-micro__desc"><?php echo wp_kses_post(nl2br(esc_html($card_desc))); ?></div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($note !== ''): ?>
                    <div class="c-course-micro__note"><?php echo wp_kses_post(nl2br(esc_html($note))); ?></div>
                <?php endif; ?>

            </div>
        </section>
        <!-- TCP: micro learning end -->
<?php

        echo trim(ob_get_clean());
    }

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
