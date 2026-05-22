<?php

/**
 * Template Name: TCP - C-Sherpa Next
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

get_header();

$fieldText = static function (string $key, string $default = ''): string {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($key);
    if (!is_string($value)) {
        return $default;
    }

    $value = trim($value);
    return $value === '' ? $default : $value;
};

$fieldHtml = static function (string $key, string $default = ''): string {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($key);
    if (!is_string($value)) {
        return $default;
    }

    $value = trim($value);
    return $value === '' ? $default : $value;
};

$fieldRows = static function (string $key): array {
    if (!function_exists('get_field')) {
        return [];
    }

    $rows = get_field($key);
    return is_array($rows) ? $rows : [];
};

$fieldImageUrl = static function (string $key, string $default = ''): string {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($key);

    if (is_string($value)) {
        $value = trim($value);

        // URL string
        if ($value !== '' && preg_match('#^https?://#i', $value)) {
            return $value;
        }

        // Attachment ID in string format
        if ($value !== '' && ctype_digit($value)) {
            $url = wp_get_attachment_image_url((int) $value, 'full');
            return is_string($url) && $url !== '' ? $url : $default;
        }

        return $value !== '' ? $value : $default;
    }

    // Attachment ID in numeric format
    if (is_int($value) || is_float($value)) {
        $url = wp_get_attachment_image_url((int) $value, 'full');
        return is_string($url) && $url !== '' ? $url : $default;
    }

    // ACF image array format
    if (is_array($value)) {
        if (isset($value['url']) && is_string($value['url']) && trim($value['url']) !== '') {
            return trim($value['url']);
        }

        if (isset($value['ID']) && (is_int($value['ID']) || ctype_digit((string) $value['ID']))) {
            $url = wp_get_attachment_image_url((int) $value['ID'], 'full');
            return is_string($url) && $url !== '' ? $url : $default;
        }
    }

    return $default;
};

$fieldLines = static function (string $key, string $default = ''): array {
    if (!function_exists('get_field')) {
        $source = $default;
    } else {
        $value = get_field($key);
        $source = is_string($value) ? trim($value) : '';
        if ($source === '') {
            $source = $default;
        }
    }

    $source = html_entity_decode((string) $source, ENT_QUOTES, 'UTF-8');
    $source = preg_replace('/<\/?br\s*\/?>(?i)/', "\n", $source) ?? $source;
    $source = wp_strip_all_tags($source);
    $parts = preg_split('/\r\n|\r|\n/', $source) ?: [];

    return array_values(array_filter(array_map(static fn(string $line): string => trim($line), $parts)));
};

$crumbHome = $fieldText('tcp_csherpa_breadcrumb_home', 'Home');
$crumbHomeLink = $fieldText('tcp_csherpa_breadcrumb_home_link', '/');
$crumbParent = $fieldText('tcp_csherpa_breadcrumb_parent', 'Doanh nghiệp');
$crumbParentLink = $fieldText('tcp_csherpa_breadcrumb_parent_link', '#');
$crumbCurrent = $fieldText('tcp_csherpa_breadcrumb_current', 'C-Sherpa Next');

$heroImage = $fieldText('tcp_csherpa_hero_bg', TCP_THEME_URI . 'assets/images/csherpa-next-hero.png');
$heroEyebrow = $fieldText('tcp_csherpa_hero_eyebrow', 'DOANH NGHIỆP');
$heroTitle = $fieldText('tcp_csherpa_hero_title', 'C-Sherpa Next');
$heroHeadline = $fieldText('tcp_csherpa_hero_headline', "Chương trình tư vấn đào tạo\nvà phát triển thế hệ lãnh đạo kế thừa");
$heroDesc = $fieldHtml('tcp_csherpa_hero_desc', '');
$heroButtonText = $fieldText('tcp_csherpa_hero_button_text', 'Đăng ký tư vấn');
$heroButtonLink = $fieldText('tcp_csherpa_hero_button_link', '#lien-he');

$metaCols = [
    [
        'label' => $fieldText('tcp_csherpa_meta_duration_label', 'Thời lượng'),
        'items' => $fieldLines('tcp_csherpa_meta_duration_items', "3-6 tháng\n1-2 năm"),
    ],
    [
        'label' => $fieldText('tcp_csherpa_meta_format_label', 'Hình thức'),
        'items' => $fieldLines('tcp_csherpa_meta_format_items', "Tư vấn tổ chức\nHuấn luyện nhóm\nKhai vấn cá nhân"),
    ],
    [
        'label' => $fieldText('tcp_csherpa_meta_method_label', 'Phương pháp'),
        'items' => $fieldLines('tcp_csherpa_meta_method_items', "UPWARDS™\nAdvisory Board\nLeadership Development Plan"),
    ],
];

$trietLyTitle = $fieldText('tcp_csherpa_triet_ly_title', 'Triết lý sản phẩm');
$trietLyContent = $fieldHtml('tcp_csherpa_triet_ly_content', '');
$doiTuongTitle = $fieldText('tcp_csherpa_doi_tuong_title', 'Đối tượng');
$doiTuongContent = $fieldHtml('tcp_csherpa_doi_tuong_content', '');
$upwardsTitle = $fieldText('tcp_csherpa_upwards_title', 'Phương pháp luận Upwards™');
$upwardsContent = $fieldHtml('tcp_csherpa_upwards_content', '');
$infoEyebrow = $fieldText('tcp_csherpa_info_eyebrow', 'C-SHERPA NEXT');
$infoTitleBlack = $fieldText('tcp_csherpa_info_title_black', 'Xác lập');
$infoTitleHighlight = $fieldText('tcp_csherpa_info_title_highlight', 'hệ thống kế thừa có định hướng');
$infoTitle = $infoTitleBlack . "\n" . $infoTitleHighlight;
$infoIconFallbacks = [
    TCP_THEME_URI . 'assets/images/csherpa-next-value-1.png',
    TCP_THEME_URI . 'assets/images/csherpa-next-value-2.png',
    TCP_THEME_URI . 'assets/images/csherpa-next-value-3.png',
];

$infoCards = [
    ['title' => $trietLyTitle, 'content' => $trietLyContent],
    ['title' => $doiTuongTitle, 'content' => $doiTuongContent],
    ['title' => $upwardsTitle, 'content' => $upwardsContent],
];

$hasInfoCards = false;
foreach ($infoCards as $infoCard) {
    if (trim((string) $infoCard['title']) !== '' || trim((string) $infoCard['content']) !== '') {
        $hasInfoCards = true;
        break;
    }
}

$bannerImage = $fieldImageUrl('tcp_csherpa_banner_image', TCP_THEME_URI . 'assets/images/csherpa-next-banner.png');
$bannerImageMobile = $fieldImageUrl('tcp_csherpa_banner_image_mobile', $bannerImage);

$valuesEyebrow = $fieldText('tcp_csherpa_values_eyebrow', 'C-SHERPA NEXT');
$valuesTitle = $fieldText('tcp_csherpa_values_title', 'Giá trị C-Sherpa Next mang lại cho tổ chức');
$valuesRows = $fieldRows('tcp_csherpa_values_items');
$valueIconFallbacks = [
    TCP_THEME_URI . 'assets/images/csherpa-next-value-1.png',
    TCP_THEME_URI . 'assets/images/csherpa-next-value-2.png',
    TCP_THEME_URI . 'assets/images/csherpa-next-value-3.png',
    TCP_THEME_URI . 'assets/images/csherpa-next-value-4.png',
];

$hasValues = false;
foreach ($valuesRows as $row) {
    $title = isset($row['title']) && is_string($row['title']) ? trim($row['title']) : '';
    $desc = isset($row['desc']) && is_string($row['desc']) ? trim($row['desc']) : '';
    $icon = '';
    if (isset($row['icon']) && is_string($row['icon'])) {
        $icon = trim($row['icon']);
    } elseif (isset($row['icon']) && is_array($row['icon']) && isset($row['icon']['url']) && is_string($row['icon']['url'])) {
        $icon = trim($row['icon']['url']);
    }

    if ($title !== '' || $desc !== '' || $icon !== '') {
        $hasValues = true;
        break;
    }
}

$roadmapEyebrow = $fieldText('tcp_csherpa_roadmap_eyebrow', 'LỘ TRÌNH');
$roadmapTitle = $fieldText('tcp_csherpa_roadmap_title', '6 giai đoạn C-Sherpa Next');
$roadmapRows = $fieldRows('tcp_csherpa_roadmap_items');

if (empty($roadmapRows)) {
    $roadmapRows = [
        ['phase' => 'Giai đoạn 01', 'title' => 'Khởi tạo và xác định mục tiêu'],
        ['phase' => 'Giai đoạn 02', 'title' => 'Đánh giá hiện trạng tổ chức'],
        ['phase' => 'Giai đoạn 03', 'title' => 'Xác định năng lực trọng yếu'],
        ['phase' => 'Giai đoạn 04', 'title' => 'Thiết kế lộ trình phát triển'],
        ['phase' => 'Giai đoạn 05', 'title' => 'Triển khai tư vấn và coaching'],
        ['phase' => 'Giai đoạn 06', 'title' => 'Đo lường, hiệu chỉnh và chuyển giao'],
    ];
}

$roadmapIconFallbacks = [
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h5l2 2h11v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path d="M3 7V5a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v2"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="2.5"/><circle cx="15" cy="8" r="2.5"/><path d="M4 18a5 5 0 0 1 10 0"/><path d="M10 18a5 5 0 0 1 10 0"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M10 13h6"/><path d="M10 17h6"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3"/><path d="M5 19a7 7 0 0 1 14 0"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="12" height="16" rx="2"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h4"/></svg>',
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6l7-3 7 3 4-2v14l-4 2-7-3-7 3z"/><path d="M10 3v14"/><path d="M17 6v14"/></svg>',
];

$contactEyebrow = $fieldText('tcp_csherpa_contact_eyebrow', 'LIÊN HỆ');
$contactTitle = $fieldText('tcp_csherpa_contact_title', 'Nhận tư vấn C-Sherpa Next cho doanh nghiệp của bạn');
$contactIntro = $fieldText('tcp_csherpa_contact_intro', 'Để lại thông tin, đội ngũ chuyên gia sẽ liên hệ và tư vấn lộ trình phù hợp cho doanh nghiệp của bạn.');
$contactFormShortcode = $fieldText('tcp_csherpa_contact_form_shortcode', '');
?>

<main class="csherpa-page">
    <div class="container">
        <div class="csherpa-breadcrumb" aria-label="Breadcrumb">
            <?php echo do_shortcode('[rank_math_breadcrumb]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
            ?>
        </div>
    </div>

    <section class="csherpa-hero" style="--csherpa-hero-bg-image: linear-gradient(180deg, rgba(99, 102, 241, 1) 0%, rgba(99, 102, 241, 1) 100%), url('<?php echo esc_url($heroImage); ?>'); --csherpa-hero-bg-color: rgba(99, 102, 241, 1); --csherpa-hero-bg-blend: color, normal;">
        <p class="csherpa-kicker"><?php echo esc_html($heroEyebrow); ?></p>

        <div class="row csherpa-hero__row reserve-tablet">
            <div class="col large-6 medium-6 small-12 csherpa-hero__col csherpa-hero__col--meta">
                <div class="col-inner">
                    <h1 class="csherpa-hero__title"><?php echo esc_html($heroTitle); ?></h1>
                    <p class="csherpa-hero__headline">
                        <?php foreach ($fieldLines('tcp_csherpa_hero_headline', $heroHeadline) as $line) : ?>
                            <span><?php echo esc_html($line); ?></span>
                        <?php endforeach; ?>
                    </p>

                    <div class="csherpa-meta">
                        <?php foreach ($metaCols as $metaCol) : ?>
                            <?php
                            $items = [];
                            foreach ((array) $metaCol['items'] as $metaItem) {
                                if (is_string($metaItem) && trim($metaItem) !== '') {
                                    $items[] = trim($metaItem);
                                }
                            }
                            ?>
                            <?php if (!empty($items)) : ?>
                                <div>
                                    <p class="csherpa-meta__label"><?php echo esc_html((string) $metaCol['label']); ?></p>
                                    <ul class="csherpa-meta__list">
                                        <?php foreach ($items as $item) : ?>
                                            <li><?php echo esc_html(preg_replace('/\s+/', ' ', wp_strip_all_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')))); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col large-6 medium-6 small-12 csherpa-hero__col csherpa-hero__col--content">
                <div class="col-inner">
                    <?php if (trim($heroDesc) !== '') : ?>
                        <div class="csherpa-hero__desc"><?php echo wp_kses_post($heroDesc); ?></div>
                    <?php endif; ?>
                    <?php if (trim($heroButtonText) !== '') : ?>
                        <p style="margin: 24px 0 0;">
                            <a class="button primary" href="<?php echo esc_url($heroButtonLink); ?>"><?php echo esc_html($heroButtonText); ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if (trim($heroImage) !== '') : ?>
                        <div class="csherpa-hero__mobile-image show-for-small">
                            <img src="<?php echo esc_url($heroImage); ?>" alt="<?php echo esc_attr($heroTitle); ?>" loading="lazy" decoding="async">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($hasInfoCards) : ?>
        <section class="csherpa-info">
            <div class="container">
                <div class="csherpa-info__inner">
                    <div class="csherpa-info__lead">
                        <p class="csherpa-kicker"><?php echo esc_html($infoEyebrow); ?></p>
                        <h2 class="csherpa-info__heading">
                            <span>
                                <?php echo esc_html($infoTitleBlack); ?> <span class="csherpa-info__heading--highlight"><?php echo esc_html($infoTitleHighlight); ?></span>
                            </span>
                        </h2>
                    </div>

                    <div class="csherpa-info__cards">
                        <?php foreach ($infoCards as $i => $infoCard) : ?>
                            <?php
                            $cardTitle = trim((string) $infoCard['title']);
                            $cardContent = trim((string) $infoCard['content']);
                            if ($cardTitle === '' && $cardContent === '') {
                                continue;
                            }
                            $cardIcon = $infoIconFallbacks[$i] ?? '';
                            ?>
                            <article class="csherpa-card">
                                <div class="csherpa-card__head">
                                    <?php if ($cardIcon !== '') : ?><img class="csherpa-card__icon" src="<?php echo esc_url($cardIcon); ?>" alt="" loading="lazy" /><?php endif; ?>
                                    <?php if ($cardTitle !== '') : ?><h3><?php echo esc_html($cardTitle); ?></h3><?php endif; ?>
                                </div>
                                <?php if ($cardContent !== '') : ?>
                                    <div class="csherpa-card__content"><?php echo wp_kses_post((string) $infoCard['content']); ?></div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="csherpa-banner" aria-hidden="true">
        <div class="container">
            <picture>
                <?php if (trim($bannerImageMobile) !== '') : ?>
                    <source media="(max-width: 849px)" srcset="<?php echo esc_url($bannerImageMobile); ?>" />
                <?php endif; ?>
                <img src="<?php echo esc_url($bannerImage); ?>" alt="" loading="lazy" />
            </picture>
        </div>
    </section>

    <?php if ($hasValues) : ?>
        <section class="csherpa-values-wrap">
            <div class="container">
                <p class="csherpa-kicker"><?php echo esc_html($valuesEyebrow); ?></p>
                <h2 class="csherpa-title"><?php echo esc_html($valuesTitle); ?></h2>
                <div class="csherpa-values">
                    <?php foreach ($valuesRows as $i => $row) : ?>
                        <?php
                        $title = isset($row['title']) && is_string($row['title']) ? trim($row['title']) : '';
                        $desc = isset($row['desc']) && is_string($row['desc']) ? trim($row['desc']) : '';
                        $icon = '';
                        if (isset($row['icon']) && is_string($row['icon'])) {
                            $icon = trim($row['icon']);
                        } elseif (isset($row['icon']) && is_array($row['icon']) && isset($row['icon']['url']) && is_string($row['icon']['url'])) {
                            $icon = trim($row['icon']['url']);
                        }
                        if ($icon === '' && isset($valueIconFallbacks[$i])) {
                            $icon = $valueIconFallbacks[$i];
                        }
                        if ($title === '' && $desc === '') {
                            continue;
                        }
                        ?>
                        <article class="csherpa-values__item">
                            <span class="csherpa-values__blur" aria-hidden="true"></span>
                            <?php if ($icon !== '') : ?><img class="csherpa-values__icon" src="<?php echo esc_url($icon); ?>" alt="" loading="lazy" /><?php endif; ?>
                            <?php if ($title !== '') : ?><h4><?php echo esc_html($title); ?></h4><?php endif; ?>
                            <?php if ($desc !== '') : ?><p><?php echo esc_html($desc); ?></p><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="csherpa-journey" id="lo-trinh">
        <div class="container">
            <div class="csherpa-journey__inner">
                <div>
                    <p class="csherpa-kicker"><?php echo esc_html($roadmapEyebrow); ?></p>
                    <h2 class="csherpa-title"><?php echo esc_html($roadmapTitle); ?></h2>

                    <div class="csherpa-roadmap">
                        <?php foreach ($roadmapRows as $idx => $row) : ?>
                            <?php
                            $icon = '';
                            $phase = isset($row['phase']) && is_string($row['phase']) ? trim($row['phase']) : '';
                            $title = isset($row['title']) && is_string($row['title']) ? trim($row['title']) : '';
                            if (isset($row['icon']) && is_string($row['icon'])) {
                                $icon = trim($row['icon']);
                            } elseif (isset($row['icon']) && is_array($row['icon']) && isset($row['icon']['url']) && is_string($row['icon']['url'])) {
                                $icon = trim($row['icon']['url']);
                            }
                            if ($phase === '' && $title === '') {
                                continue;
                            }
                            $fallbackIconSvg = $roadmapIconFallbacks[$idx] ?? '';
                            ?>
                            <article class="csherpa-roadmap__item">
                                <span class="csherpa-roadmap__icon" aria-hidden="true">
                                    <?php if ($icon !== '') : ?>
                                        <img src="<?php echo esc_url($icon); ?>" alt="" loading="lazy" />
                                    <?php elseif ($fallbackIconSvg !== '') : ?>
                                        <?php echo $fallbackIconSvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <?php echo esc_html((string) ($idx + 1)); ?>
                                    <?php endif; ?>
                                </span>
                                <div>
                                    <?php if ($phase !== '') : ?><p class="csherpa-roadmap__phase"><?php echo esc_html($phase); ?></p><?php endif; ?>
                                    <?php if ($title !== '') : ?><p class="csherpa-roadmap__title"><?php echo esc_html($title); ?></p><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="csherpa-journey-meta">
                        <?php foreach ($metaCols as $metaCol) : ?>
                            <?php
                            $items = [];
                            foreach ((array) $metaCol['items'] as $metaItem) {
                                if (is_string($metaItem) && trim($metaItem) !== '') {
                                    $items[] = trim($metaItem);
                                }
                            }
                            ?>
                            <?php if (!empty($items)) : ?>
                                <div class="csherpa-journey-meta__col">
                                    <p class="csherpa-journey-meta__label"><?php echo esc_html((string) $metaCol['label']); ?></p>
                                    <div class="csherpa-journey-meta__chips">
                                        <?php foreach ($items as $item) : ?>
                                            <span><?php echo esc_html($item); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                </div>

                <div class="csherpa-support" id="lien-he">
                    <p class="csherpa-kicker text-center"><?php echo esc_html($contactEyebrow); ?></p>
                    <h2 class="csherpa-title"><?php echo esc_html($contactTitle); ?></h2>
                    <p class="csherpa-support__intro"><?php echo esc_html($contactIntro); ?></p>

                    <div class="csherpa-support__form">
                        <?php if ($contactFormShortcode !== '') : ?>
                            <?php echo do_shortcode($contactFormShortcode); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                            ?>
                        <?php else : ?>
                            <form class="csherpa-fallback-form" onsubmit="return false;">
                                <label>Họ và tên*<input type="text" placeholder="Nhập họ và tên" /></label>
                                <label>Email*<input type="email" placeholder="Điền địa chỉ email" /></label>
                                <label>Số điện thoại*<input type="tel" placeholder="Nhập số điện thoại" /></label>
                                <label>Bạn cần hỗ trợ*
                                    <select>
                                        <option>Chọn mục thông tin cần hỗ trợ</option>
                                    </select>
                                </label>
                                <button type="submit">Gửi thông tin</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
