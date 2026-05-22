<?php

namespace TCP\Theme\Services\Course;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class CourseTrainerUI
{
    use Singleton;

    public function render(\WC_Product $product, array $data): void
    {
        $mode = $data['tcp_course_trainer_mode'] ?? 'inline';
        $trainer = ($mode === 'relationship')
            ? $this->build_from_relationship($data)
            : $this->build_from_inline($data);

        if (empty($trainer['name']) && empty($trainer['bio']) && empty($trainer['avatar']) && empty($trainer['highlights'])) {
            return;
        }

        ob_start();
?>
        <section class="c-course-section c-course-trainer" id="trainer">
            <div class="c-course-trainer__card">

                <div class="c-course-trainer__top">
                    <div class="c-course-trainer__media">
                        <?php echo $this->render_avatar($trainer); ?>
                    </div>

                    <div class="c-course-trainer__info">
                        <div class="c-course-trainer__label">Trainer</div>

                        <?php if (!empty($trainer['name'])): ?>
                            <h2 class="c-course-trainer__name">
                                <?php if (!empty($trainer['link'])): ?>
                                    <a class="c-course-trainer__nameLink" href="<?php echo esc_url($trainer['link']); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($trainer['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo esc_html($trainer['name']); ?>
                                <?php endif; ?>
                            </h2>
                        <?php endif; ?>

                        <?php #if (!empty($trainer['title'])): 
                        if (false): ?>
                            <div class="c-course-trainer__title"><?php echo esc_html($trainer['title']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="c-course-trainer__body">
                    <?php if (!empty($trainer['bio'])): ?>
                        <div class="c-course-trainer__bio">
                            <?php echo wp_kses_post(nl2br(esc_html($trainer['bio']))); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($trainer['highlights']) && is_array($trainer['highlights'])): ?>
                        <ul class="c-course-trainer__highlights">
                            <?php foreach ($trainer['highlights'] as $item):
                                $txt = trim((string) $item);
                                if ($txt === '') continue;
                            ?>
                                <li class="c-course-trainer__highlight"><?php echo esc_html($txt); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif (!empty($trainer['highlights']) && is_string($trainer['highlights'])): ?>
                        <div class="c-course-trainer__highlights"><?php echo wp_kses_post($trainer['highlights']); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($data['tcp_course_trainer_description'])): ?>
                        <div class="c-course-trainer__description">
                            <?php echo wp_kses_post($data['tcp_course_trainer_description']); ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>
<?php

        echo trim(ob_get_clean());
    }


    /**
     * Mode: relationship (tcp_trainer post)
     */
    private function build_from_relationship(array $data): array
    {
        $ref = $data['tcp_course_trainer_ref'] ?? 0;

        // ACF có thể trả WP_Post hoặc ID
        $trainer_id = 0;
        if (is_object($ref) && isset($ref->ID)) {
            $trainer_id = (int) $ref->ID;
        } else {
            $trainer_id = (int) $ref;
        }

        if ($trainer_id <= 0) {
            return [
                'name' => '',
                'title' => '',
                'avatar' => '',
                'link' => '',
                'bio' => '',
                'highlights' => [],
            ];
        }

        // Verify post type nếu muốn chặt chẽ
        $pt = get_post_type($trainer_id);
        if ($pt !== 'tcp_trainer') {
            // Không đúng post type => bỏ qua
            return [
                'name' => '',
                'title' => '',
                'avatar' => '',
                'link' => '',
                'bio' => '',
                'highlights' => [],
            ];
        }

        $name    = get_the_title($trainer_id);
        $role    = function_exists('get_field') ? (string) get_field('trainer_role', $trainer_id) : '';
        $company = function_exists('get_field') ? (string) get_field('trainer_company', $trainer_id) : '';

        // Gộp title theo kiểu: "Chức danh · Công ty"
        $title = trim($role);
        if ($company !== '') {
            $title = $title !== '' ? ($title . ' · ' . $company) : $company;
        }

        $avatar_url = function_exists('get_field') ? (string) get_field('trainer_photo', $trainer_id) : '';
        $link       = function_exists('get_field') ? (string) get_field('trainer_link', $trainer_id) : '';
        $excerpt    = function_exists('get_field') ? (string) get_field('trainer_excerpt', $trainer_id) : '';

        /**
         * Highlights:
         */
        $highlights = [];

        $content = trim((string) get_post_field('post_content', $trainer_id));
        if ($content !== '') {
            // Tách theo dòng, bỏ dòng trống, bỏ ký tự bullet đầu dòng nếu có
            // $lines = preg_split("/\r\n|\n|\r/", $content);
            // if (is_array($lines)) {
            //     foreach ($lines as $line) {
            //         $line = trim($line);
            //         if ($line === '') continue;
            //         $line = preg_replace('/^[-•\*\s]+/u', '', $line); // remove bullet prefix
            //         if ($line !== '') $highlights[] = $line;
            //     }
            // }

            $highlights = $content;
        }

        return [
            'name'       => $name,
            'title'      => $title,
            'avatar'     => $avatar_url, // url
            'link'       => $link,
            'bio'        => $excerpt,
            'highlights' => $highlights,
        ];
    }

    /**
     * Mode: inline (fields nằm trên product)
     */
    private function build_from_inline(array $data): array
    {
        $name  = (string) ($data['tcp_course_trainer_name'] ?? '');
        $title = (string) ($data['tcp_course_trainer_title'] ?? '');
        $bio   = (string) ($data['tcp_course_trainer_bio'] ?? '');
        $link  = ''; // inline chưa có field link trong JSON course, nên để rỗng

        // avatar có thể là id/url/false
        $avatar = $data['tcp_course_trainer_avatar'] ?? '';
        $avatar_url = '';

        if (is_numeric($avatar)) {
            $avatar_url = wp_get_attachment_image_url((int) $avatar, 'medium') ?: '';
        } elseif (is_string($avatar)) {
            $avatar_url = $avatar;
        }

        // highlights: repeater hoặc mảng string
        $highlights_raw = $data['tcp_course_trainer_highlights'] ?? [];
        $highlights = $this->normalize_highlights($highlights_raw);

        return [
            'name'       => $name,
            'title'      => $title,
            'avatar'     => $avatar_url,
            'link'       => $link,
            'bio'        => $bio,
            'highlights' => $highlights,
        ];
    }

    private function normalize_highlights($raw): array
    {
        if (empty($raw)) return [];

        $out = [];

        // case: repeater [{text: "..."}]
        if (is_array($raw) && isset($raw[0]) && is_array($raw[0])) {
            foreach ($raw as $row) {
                $txt = '';
                if (isset($row['text'])) $txt = (string) $row['text'];
                elseif (isset($row['tcp_course_trainer_highlight_text'])) $txt = (string) $row['tcp_course_trainer_highlight_text'];
                else {
                    // fallback: lấy value đầu tiên nếu có
                    $first = reset($row);
                    if (is_string($first)) $txt = $first;
                }

                $txt = trim($txt);
                if ($txt !== '') $out[] = $txt;
            }
            return $out;
        }

        // case: mảng string ["...", "..."]
        if (is_array($raw)) {
            foreach ($raw as $item) {
                $txt = trim((string) $item);
                if ($txt !== '') $out[] = $txt;
            }
            return $out;
        }

        // case: string 1 dòng
        if (is_string($raw)) {
            $txt = trim($raw);
            return $txt !== '' ? [$txt] : [];
        }

        return [];
    }

    private function render_avatar(array $trainer): string
    {
        $avatar = (string) ($trainer['avatar'] ?? '');
        $name   = (string) ($trainer['name'] ?? '');
        $link   = (string) ($trainer['link'] ?? '');

        $img = '';
        if ($avatar !== '') {
            $img = '<img class="c-course-trainer__avatar" src="' . esc_url($avatar) . '" alt="' . esc_attr($name) . '" loading="lazy" decoding="async" />';
        } else {
            // fallback: placeholder 
            $img = '<div class="c-course-trainer__avatar c-course-trainer__avatar--placeholder" aria-hidden="true"></div>';
        }

        // Nếu có link profile thì bọc avatar bằng <a>
        if ($link !== '') {
            return '<a class="c-course-trainer__avatarLink" href="' . esc_url($link) . '" target="_blank" rel="noopener">' . $img . '</a>';
        }

        return $img;
    }
}
