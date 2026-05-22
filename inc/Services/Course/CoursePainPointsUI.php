<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CoursePainPointsUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_pain_points_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_pain_points_title'] ?? ''));

        $items_raw = $data['tcp_course_pain_points'] ?? [];
        $items = $this->normalize_items($items_raw);

        // Nếu không có gì thì bỏ qua
        if (empty($items)) {
            return;
        }

        ob_start();
        ?>
        <!-- TCP: pain points start -->
        <section class="c-course-section c-course-painpoints" id="pain-points">
          <div class="c-course-painpoints__inner">

            <?php if ($eyebrow !== '' || $title !== ''): ?>
              <header class="c-course-section__header c-course-painpoints__header">
                <?php if ($eyebrow !== ''): ?>
                  <div class="c-course-section__eyebrow c-course-painpoints__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <?php endif; ?>

                <?php if ($title !== ''): ?>
                  <h2 class="c-course-section__title c-course-painpoints__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
              </header>
            <?php endif; ?>

            <?php if (!empty($items)): ?>
              <ul class="c-course-painpoints__list">
                <?php foreach ($items as $txt):
                  $txt = trim((string) $txt);
                  if ($txt === '') continue;
                ?>
                  <li class="c-course-painpoints__item">
                    <span class="c-course-painpoints__icon icon-circle-x" aria-hidden="true"></span>
                    <span class="c-course-painpoints__text"><?php echo esc_html($txt); ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

          </div>
        </section>
        <!-- TCP: pain points end -->
        <?php

        echo trim(ob_get_clean());
    }

    /**
     * Support:
     * - repeater rows: [ ['text' => '...'], ... ]
     * - or [ '...', '...' ]
     */
    private function normalize_items($raw): array
    {
        if (empty($raw)) return [];

        $out = [];

        // Repeater rows
        if (is_array($raw) && isset($raw[0]) && is_array($raw[0])) {
            foreach ($raw as $row) {
                $txt = '';
                if (isset($row['text'])) $txt = (string) $row['text'];
                else {
                    // fallback: lấy value đầu tiên
                    $first = reset($row);
                    if (is_string($first)) $txt = $first;
                }

                $txt = trim($txt);
                if ($txt !== '') $out[] = $txt;
            }
            return $out;
        }

        // Array string
        if (is_array($raw)) {
            foreach ($raw as $item) {
                $txt = trim((string) $item);
                if ($txt !== '') $out[] = $txt;
            }
            return $out;
        }

        // Single string
        if (is_string($raw)) {
            $txt = trim($raw);
            return $txt !== '' ? [$txt] : [];
        }

        return [];
    }
}
