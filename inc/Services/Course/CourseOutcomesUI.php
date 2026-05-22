<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseOutcomesUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_outcomes_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_outcomes_title'] ?? ''));
        $desc    = trim((string) ($data['tcp_course_outcomes_desc'] ?? ''));

        $list_raw = $data['tcp_course_outcomes_list'] ?? [];
        $items = $this->normalize_list($list_raw);

        // Không có gì thì skip
        if (empty($items)) {
            return;
        }

        ob_start();
        ?>
        <!-- TCP: outcomes start -->
        <section class="c-course-section c-course-outcomes" id="outcomes">
          <div class="c-course-outcomes__inner">

            <?php if ($eyebrow !== '' || $title !== '' || $desc !== ''): ?>
              <header class="c-course-section__header c-course-outcomes__header">
                <?php if ($eyebrow !== ''): ?>
                  <div class="c-course-section__ eyebrow c-course-outcomes__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <?php endif; ?>

                <?php if ($title !== ''): ?>
                  <h2 class="c-course-section__title c-course-outcomes__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($desc !== ''): ?>
                  <div class="c-course-section__desc c-course-outcomes__desc"><?php echo wp_kses_post(nl2br(esc_html($desc))); ?></div>
                <?php endif; ?>
              </header>
            <?php endif; ?>

            <?php if (!empty($items)): ?>
              <ul class="c-course-outcomes__list">
                <?php foreach ($items as $txt):
                  $txt = trim((string) $txt);
                  if ($txt === '') continue;
                ?>
                  <li class="c-course-outcomes__item">
                    <span class="c-course-outcomes__icon icon-circle-check" aria-hidden="true"></span>
                    <span class="c-course-outcomes__text"><?php echo esc_html($txt); ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

          </div>
        </section>
        <!-- TCP: outcomes end -->
        <?php

        echo trim(ob_get_clean());
    }

    /**
     * Support:
     * - repeater rows: [ ['text' => '...'], ... ] (nếu ACF đặt key 'text')
     * - list of strings: ['...', '...']
     */
    private function normalize_list($raw): array
    {
        if (empty($raw)) return [];

        $out = [];

        // Repeater rows
        if (is_array($raw) && isset($raw[0]) && is_array($raw[0])) {
            foreach ($raw as $row) {
                $txt = '';
                if (isset($row['text'])) $txt = (string) $row['text'];
                else {
                    $first = reset($row);
                    if (is_string($first)) $txt = $first;
                }
                $txt = trim($txt);
                if ($txt !== '') $out[] = $txt;
            }
            return $out;
        }

        // Array strings
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
