<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseCurriculumUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_curriculum_eyebrow_heading'] ?? ''));
        $title   = trim((string) ($data['tcp_course_curriculum_title'] ?? ''));

        $modules = is_array($data['tcp_course_curriculum_modules'] ?? null)
            ? $data['tcp_course_curriculum_modules']
            : [];

        if (empty($modules)) {
            return;
        }

        $stats = $this->build_stats($modules);
        $accordion_shortcode = $this->build_flatsome_accordion_shortcode($modules,  $product->get_id());

        ob_start();
?>
        <!-- TCP: curriculum start -->
        <section class="c-course-section c-course-curriculum" id="curriculum">
            <div class="c-course-curriculum__inner">

                <?php if ($eyebrow !== '' || $title !== '' || $stats['summary'] !== ''): ?>
                    <header class="c-course-section__header c-course-curriculum__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-curriculum__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>

                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-curriculum__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>

                        <?php # if ($stats['summary'] !== ''): 
                        ?>
                        <!-- <div class="c-course-section__summary c-course-curriculum__summary"><?php #echo esc_html($stats['summary']); 
                                                                                                    ?></div> -->
                        <?php #  endif; 
                        ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($modules)): ?>
                    <div class="c-course-curriculum__accordion">
                        <?php echo do_shortcode($accordion_shortcode); ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <!-- TCP: curriculum end -->
<?php

        echo trim(ob_get_clean());
    }

    /**
     * Build summary text like: "6 Module, 20 Video, 2 Giờ học"
     */
    private function build_stats(array $modules): array
    {
        $module_count = 0;
        $lesson_count = 0;
        $total_seconds = 0;

        foreach ($modules as $m) {
            if (!is_array($m)) continue;
            $module_count++;

            $lessons = is_array($m['module_lessons'] ?? null) ? $m['module_lessons'] : [];
            foreach ($lessons as $l) {
                if (!is_array($l)) continue;
                $lesson_count++;

                $dur = trim((string) ($l['lesson_duration'] ?? ''));
                $total_seconds += $this->parse_duration_to_seconds($dur);
            }
        }

        $time_label = $this->format_seconds_to_vn($total_seconds);

        $summary_parts = [];
        if ($module_count > 0) $summary_parts[] = $module_count . ' Module';
        if ($lesson_count > 0) $summary_parts[] = $lesson_count . ' Video';
        if ($time_label !== '') $summary_parts[] = $time_label;

        return [
            'module_count' => $module_count,
            'lesson_count' => $lesson_count,
            'total_seconds' => $total_seconds,
            'summary' => implode(', ', $summary_parts),
        ];
    }

    /**
     * Build Flatsome accordion shortcode from modules
     * - Each module = accordion-item
     * - Inside: HTML list of lessons
     */
    private function build_flatsome_accordion_shortcode(array $modules, int $product_id): string
    {

        // auto_open: mở module đầu tiên
        $shortcode = '[accordion auto_open="true"]';

        foreach ($modules as $idx => $m) {
            if (!is_array($m)) continue;

            $m_title = trim((string) ($m['module_title'] ?? ''));
            $m_desc  = trim((string) ($m['module_desc'] ?? ''));
            $lessons = is_array($m['module_lessons'] ?? null) ? $m['module_lessons'] : [];

            $module_title = $m_title !== '' ? $m_title : ('Module ' . ($idx + 1));
            $module_title_attr = esc_attr($module_title);

            // Build module inner HTML
            $content = '';

            if ($m_desc !== '') {
                $content .= '<div class="c-course-curriculum__moduleDesc">' . wp_kses_post(nl2br(esc_html($m_desc))) . '</div>';
            }

            if (!empty($lessons)) {
                $content .= '<ul class="c-course-curriculum__lessons">';
                foreach ($lessons as $l) {
                    if (!is_array($l)) continue;

                    $l_title = trim((string) ($l['lesson_title'] ?? ''));
                    $l_dur   = trim((string) ($l['lesson_duration'] ?? ''));
                    $is_prev = !empty($l['is_preview']);
                    $video   = trim((string) ($l['lesson_video_url'] ?? ''));

                    if ($l_title === '' && $l_dur === '' && !$is_prev) continue;

                    $content .= '<li class="c-course-curriculum__lesson">';

                    // Left: title
                    $content .= '<span class="c-course-curriculum__lessonTitle">' . esc_html($l_title) . '</span>';

                    // Right: actions (preview + duration)
                    $content .= '<span class="c-course-curriculum__lessonMeta">';

                    if ($is_prev && $video !== '') {
                        $group = 'tcp-preview-p' . $product_id;

                        $content .= '<a class="c-course-curriculum__lessonPreview"'
                            . ' href="' . esc_url($video) . '"'
                            . ' data-fancybox="' . esc_attr($group) . '"'
                            . ' data-type="youtube"'
                            . ' data-caption="' . esc_attr($l_title) . '"'
                            . ' aria-label="Học thử: ' . esc_attr($l_title) . '">'
                            . '<span class="icon-circle-play" aria-hidden="true"></span>'
                            . '<span class="c-course-curriculum__lessonPreviewText">Học thử</span>'
                            . '</a>';
                    }


                    if ($l_dur !== '') {
                        $content .= '<span class="c-course-curriculum__lessonDur">' . esc_html($l_dur) . '</span>';
                    }

                    $content .= '</span>'; // lessonMeta
                    $content .= '</li>';
                }
                $content .= '</ul>';
            }

            $shortcode .= '[accordion-item title="' . $module_title_attr . '"]';
            $shortcode .= $content;
            $shortcode .= '[/accordion-item]';
        }

        $shortcode .= '[/accordion]';

        return $shortcode;
    }

    /**
     * Parse duration:
     * - "mm:ss" or "hh:mm:ss" -> seconds
     * - "10:00" => 600
     * - if invalid => 0
     */
    private function parse_duration_to_seconds(string $dur): int
    {
        $dur = trim($dur);
        if ($dur === '') return 0;

        // allow "mm:ss" or "hh:mm:ss"
        $parts = explode(':', $dur);
        $parts = array_map('trim', $parts);

        if (count($parts) === 2) {
            $m = is_numeric($parts[0]) ? (int) $parts[0] : 0;
            $s = is_numeric($parts[1]) ? (int) $parts[1] : 0;
            return max(0, $m * 60 + $s);
        }

        if (count($parts) === 3) {
            $h = is_numeric($parts[0]) ? (int) $parts[0] : 0;
            $m = is_numeric($parts[1]) ? (int) $parts[1] : 0;
            $s = is_numeric($parts[2]) ? (int) $parts[2] : 0;
            return max(0, $h * 3600 + $m * 60 + $s);
        }

        return 0;
    }

    /**
     * Format seconds to Vietnamese label
     * Example:
     * - 0 => ""
     * - 600 => "10 phút"
     * - 7200 => "2 giờ"
     * - 7500 => "2 giờ 5 phút"
     */
    private function format_seconds_to_vn(int $seconds): string
    {
        if ($seconds <= 0) return '';

        $hours = intdiv($seconds, 3600);
        $mins  = intdiv($seconds % 3600, 60);

        if ($hours > 0 && $mins > 0) return $hours . ' giờ ' . $mins . ' phút';
        if ($hours > 0) return $hours . ' giờ';
        return $mins . ' phút';
    }
}
