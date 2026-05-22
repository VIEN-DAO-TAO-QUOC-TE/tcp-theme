<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseJoinDetailsUI
{
    use Singleton;

    protected function init(): void
    {
        add_action('acf/init', [$this, 'register_acf_fields']);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key'    => 'group_tcp_course_join',
            'title'  => 'Cách thức tham gia',
            'fields' => [
                [
                    'key'  => 'field_tcp_course_join_eyebrow',
                    'name' => 'tcp_course_join_eyebrow',
                    'label' => 'Eyebrow',
                    'type' => 'text',
                    'placeholder' => 'CÁCH THỨC THAM GIA',
                ],
                [
                    'key'  => 'field_tcp_course_join_title',
                    'name' => 'tcp_course_join_title',
                    'label' => 'Tiêu đề',
                    'type' => 'text',
                ],
                [
                    'key'  => 'field_tcp_course_join_intro_blocks',
                    'name' => 'tcp_course_join_intro_blocks',
                    'label' => 'Block thông tin chung (Đối tượng / Yêu cầu …)',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Thêm block',
                    'sub_fields' => [
                        [
                            'key' => 'field_tcp_course_join_intro_icon',
                            'name' => 'icon',
                            'label' => 'Icon class',
                            'type' => 'text',
                            'default_value' => 'icon-users',
                        ],
                        [
                            'key' => 'field_tcp_course_join_intro_label',
                            'name' => 'label',
                            'label' => 'Nhãn',
                            'type' => 'text',
                        ],
                        [
                            'key' => 'field_tcp_course_join_intro_lines',
                            'name' => 'lines',
                            'label' => 'Nội dung (mỗi dòng một bullet)',
                            'type' => 'textarea',
                            'rows' => 4,
                        ],
                    ],
                ],
                [
                    'key'  => 'field_tcp_course_join_levels',
                    'name' => 'tcp_course_join_levels',
                    'label' => 'Các level (Level 1, Level 2, Level 3)',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Thêm level',
                    'sub_fields' => [
                        ['key' => 'field_tcp_course_join_level_name', 'name' => 'name', 'label' => 'Tên level', 'type' => 'text'],
                        ['key' => 'field_tcp_course_join_level_schedule', 'name' => 'schedule', 'label' => 'Lịch học / thời lượng', 'type' => 'textarea', 'rows' => 3],
                        ['key' => 'field_tcp_course_join_level_price', 'name' => 'price', 'label' => 'Giá', 'type' => 'text', 'placeholder' => '3.900.000 triệu /học viên'],
                        ['key' => 'field_tcp_course_join_level_inc_label', 'name' => 'includes_label', 'label' => 'Nhãn quà tặng', 'type' => 'text', 'default_value' => 'Học viên sẽ nhận'],
                        ['key' => 'field_tcp_course_join_level_inc_lines', 'name' => 'includes_lines', 'label' => 'Danh sách quà tặng (mỗi dòng một mục)', 'type' => 'textarea', 'rows' => 4],
                    ],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_enabled',
                    'name' => 'tcp_course_join_combo_enabled',
                    'label' => 'Bật combo',
                    'type' => 'true_false',
                    'ui'   => 1,
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_name',
                    'name' => 'tcp_course_join_combo_name',
                    'label' => 'Tên combo',
                    'type' => 'text',
                    'default_value' => 'Combo 3 level',
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_schedule',
                    'name' => 'tcp_course_join_combo_schedule',
                    'label' => 'Lịch / thời lượng',
                    'type' => 'textarea',
                    'rows' => 2,
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_old_price',
                    'name' => 'tcp_course_join_combo_old_price',
                    'label' => 'Giá gốc (strikethrough)',
                    'type' => 'text',
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_new_price',
                    'name' => 'tcp_course_join_combo_new_price',
                    'label' => 'Giá ưu đãi',
                    'type' => 'text',
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_bonus_label',
                    'name' => 'tcp_course_join_combo_bonus_label',
                    'label' => 'Nhãn danh sách bonus',
                    'type' => 'text',
                    'default_value' => 'Bonus đi kèm',
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
                [
                    'key'  => 'field_tcp_course_join_combo_bonus',
                    'name' => 'tcp_course_join_combo_bonus',
                    'label' => 'Bonus đi kèm (mỗi dòng một mục)',
                    'type' => 'textarea',
                    'rows' => 4,
                    'conditional_logic' => [[['field' => 'field_tcp_course_join_combo_enabled', 'operator' => '==', 'value' => '1']]],
                ],
            ],
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'product']]],
            'menu_order' => 27,
            'position'   => 'normal',
        ]);
    }

    public function render(\WC_Product $product, array $data): void
    {
        $eyebrow = trim((string) ($data['tcp_course_join_eyebrow'] ?? ''));
        $title   = trim((string) ($data['tcp_course_join_title'] ?? ''));
        $intro_blocks = is_array($data['tcp_course_join_intro_blocks'] ?? null) ? $data['tcp_course_join_intro_blocks'] : [];
        $levels  = is_array($data['tcp_course_join_levels'] ?? null) ? $data['tcp_course_join_levels'] : [];
        $combo_on = !empty($data['tcp_course_join_combo_enabled']);

        if (empty($intro_blocks) && empty($levels) && !$combo_on && $title === '') {
            return;
        }

        ob_start();
        ?>
        <section class="c-course-section c-course-join" id="join">
            <div class="c-course-join__inner">
                <?php if ($eyebrow !== '' || $title !== ''): ?>
                    <header class="c-course-section__header c-course-join__header">
                        <?php if ($eyebrow !== ''): ?>
                            <div class="c-course-section__eyebrow c-course-join__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                        <?php endif; ?>
                        <?php if ($title !== ''): ?>
                            <h2 class="c-course-section__title c-course-join__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if (!empty($intro_blocks)): ?>
                    <div class="c-course-join__intro">
                        <?php foreach ($intro_blocks as $block):
                            $icon = trim((string) ($block['icon'] ?? 'icon-users'));
                            $label = trim((string) ($block['label'] ?? ''));
                            $lines = $this->split_lines((string) ($block['lines'] ?? ''));
                            if ($label === '' && empty($lines)) continue;
                        ?>
                            <div class="c-course-join__introBlock">
                                <span class="c-course-join__introIcon <?php echo esc_attr($icon); ?>" aria-hidden="true"></span>
                                <div class="c-course-join__introBody">
                                    <?php if ($label !== ''): ?>
                                        <div class="c-course-join__introLabel"><?php echo esc_html($label); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($lines)): ?>
                                        <ul class="c-course-join__introLines">
                                            <?php foreach ($lines as $line): ?>
                                                <li><?php echo esc_html($line); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($levels)): ?>
                    <div class="c-course-join__levels">
                        <?php foreach ($levels as $lvl):
                            $name = trim((string) ($lvl['name'] ?? ''));
                            $schedule = trim((string) ($lvl['schedule'] ?? ''));
                            $price = trim((string) ($lvl['price'] ?? ''));
                            $inc_label = trim((string) ($lvl['includes_label'] ?? 'Học viên sẽ nhận'));
                            $inc_lines = $this->split_lines((string) ($lvl['includes_lines'] ?? ''));
                            if ($name === '' && $schedule === '' && $price === '' && empty($inc_lines)) continue;
                        ?>
                            <article class="c-course-join__levelCard">
                                <div class="c-course-join__levelHead">
                                    <?php if ($name !== ''): ?>
                                        <h3 class="c-course-join__levelName"><?php echo esc_html($name); ?></h3>
                                    <?php endif; ?>
                                    <?php if ($schedule !== ''): ?>
                                        <div class="c-course-join__levelSchedule"><?php echo wp_kses_post(nl2br(esc_html($schedule))); ?></div>
                                    <?php endif; ?>
                                    <?php if ($price !== ''): ?>
                                        <div class="c-course-join__levelPrice"><?php echo esc_html($price); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($inc_label !== '' || !empty($inc_lines)): ?>
                                    <div class="c-course-join__levelIncludes">
                                        <?php if ($inc_label !== ''): ?>
                                            <div class="c-course-join__levelIncludesLabel">
                                                <span class="c-course-join__levelIncludesIcon icon-gift" aria-hidden="true"></span>
                                                <?php echo esc_html($inc_label); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($inc_lines)): ?>
                                            <ul class="c-course-join__levelIncludesList">
                                                <?php foreach ($inc_lines as $line): ?>
                                                    <li><?php echo esc_html($line); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($combo_on):
                    $combo_name = trim((string) ($data['tcp_course_join_combo_name'] ?? ''));
                    $combo_schedule = trim((string) ($data['tcp_course_join_combo_schedule'] ?? ''));
                    $combo_old = trim((string) ($data['tcp_course_join_combo_old_price'] ?? ''));
                    $combo_new = trim((string) ($data['tcp_course_join_combo_new_price'] ?? ''));
                    $combo_bonus_label = trim((string) ($data['tcp_course_join_combo_bonus_label'] ?? ''));
                    $combo_bonus = $this->split_lines((string) ($data['tcp_course_join_combo_bonus'] ?? ''));
                ?>
                    <article class="c-course-join__combo">
                        <div class="c-course-join__comboHead">
                            <?php if ($combo_name !== ''): ?>
                                <h3 class="c-course-join__comboName"><?php echo esc_html($combo_name); ?></h3>
                            <?php endif; ?>
                            <?php if ($combo_schedule !== ''): ?>
                                <div class="c-course-join__comboSchedule"><?php echo wp_kses_post(nl2br(esc_html($combo_schedule))); ?></div>
                            <?php endif; ?>
                            <?php if ($combo_old !== '' || $combo_new !== ''): ?>
                                <div class="c-course-join__comboPrice">
                                    <?php if ($combo_old !== ''): ?>
                                        <span class="c-course-join__comboOld"><?php echo esc_html($combo_old); ?></span>
                                    <?php endif; ?>
                                    <?php if ($combo_new !== ''): ?>
                                        <span class="c-course-join__comboNew"><?php echo esc_html($combo_new); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($combo_bonus)): ?>
                            <div class="c-course-join__comboBonus">
                                <?php if ($combo_bonus_label !== ''): ?>
                                    <div class="c-course-join__comboBonusLabel">
                                        <span class="c-course-join__comboBonusIcon icon-gift" aria-hidden="true"></span>
                                        <?php echo esc_html($combo_bonus_label); ?>
                                    </div>
                                <?php endif; ?>
                                <ul class="c-course-join__comboBonusList">
                                    <?php foreach ($combo_bonus as $line): ?>
                                        <li><?php echo esc_html($line); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endif; ?>
            </div>
        </section>
        <?php
        echo trim(ob_get_clean());
    }

    private function split_lines(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') return [];
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $out = [];
        foreach ((array) $lines as $line) {
            $line = trim((string) $line);
            if ($line !== '') $out[] = $line;
        }
        return $out;
    }
}
