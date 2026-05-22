<?php
/**
 * Single template for TCP Trainers.
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

get_header();

$postId = get_queried_object_id();
if ($postId <= 0) {
    $postId = absint((string) get_the_ID());
}

$getText = static function (string $key, string $default = '') use ($postId): string {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($key, $postId);
    if (is_string($value)) {
        $value = trim($value);
        return $value !== '' ? $value : $default;
    }

    return $default;
};

$trainerName = get_the_title($postId);
$trainerRole = $getText('trainer_role');
$trainerCompany = $getText('trainer_company');
$trainerExcerpt = $getText('trainer_excerpt');
$trainerLink = $getText('trainer_link');

$aboutPageId = 653;
$aboutPageLink = get_permalink($aboutPageId);
$homeLink = home_url('/');
$currentUrl = get_permalink($postId);

$rawContent = trim((string) get_post_field('post_content', $postId));
$contentHtml = '';
if ($rawContent !== '') {
    $contentHtml = wpautop(wp_kses_post($rawContent));
} elseif ($trainerExcerpt !== '') {
    $contentHtml = wpautop(esc_html($trainerExcerpt));
}

$heroLead = $trainerRole;
if ($trainerCompany !== '') {
    $heroLead = $heroLead !== '' ? ($heroLead . ' • ' . $trainerCompany) : $trainerCompany;
}

$image = null;
if (function_exists('get_field')) {
    $image = get_field('trainer_photo_transparent', $postId);
    if (!$image) {
        $image = get_field('trainer_photo', $postId);
    }
}

$imageHtml = '';
$imageAlt = $trainerName !== '' ? $trainerName : 'Trainer';
if (is_numeric($image)) {
    $imageHtml = wp_get_attachment_image((int) $image, 'full', false, [
        'class' => 'trainer-detail-page__photo',
        'alt' => $imageAlt,
        'loading' => 'eager',
        'decoding' => 'async',
    ]);
} elseif (is_array($image)) {
    $imageUrl = '';
    if (!empty($image['url']) && is_string($image['url'])) {
        $imageUrl = trim($image['url']);
    }
    if ($imageUrl !== '') {
        $alt = $imageAlt;
        if (!empty($image['alt']) && is_string($image['alt'])) {
            $alt = trim($image['alt']) !== '' ? trim($image['alt']) : $alt;
        }

        $imageHtml = '<img class="trainer-detail-page__photo" src="' . esc_url($imageUrl) . '" alt="' . esc_attr($alt) . '" loading="eager" decoding="async" />';
    }
}

if ($imageHtml === '') {
    $featuredUrl = get_the_post_thumbnail_url($postId, 'full');
    if (is_string($featuredUrl) && trim($featuredUrl) !== '') {
        $imageHtml = '<img class="trainer-detail-page__photo" src="' . esc_url(trim($featuredUrl)) . '" alt="' . esc_attr($imageAlt) . '" loading="eager" decoding="async" />';
    }
}

$shareUrl = rawurlencode($currentUrl ?: $homeLink);
$shareTitle = rawurlencode($trainerName);
$shareIconBase = get_stylesheet_directory_uri() . '/assets/images/share/';
$shareLinks = [
    [
        'label' => 'Facebook',
        'href' => 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl,
        'icon' => $shareIconBase . 'facebook.png',
        'svg' => '<path d="M13.5 3h2.5V0h-2.5C10.85 0 9 1.79 9 5v2H6v3h3v10h4V10h3.2l.5-3H13V5c0-.88.22-2 1.5-2Z"/>',
    ],
    [
        'label' => 'Instagram',
        'href' => $trainerLink !== '' ? $trainerLink : ($aboutPageLink ?: $homeLink),
        'icon' => $shareIconBase . 'instagram.png',
        'svg' => '<path d="M7.8 0h8.4A7.8 7.8 0 0 1 24 7.8v8.4a7.8 7.8 0 0 1-7.8 7.8H7.8A7.8 7.8 0 0 1 0 16.2V7.8A7.8 7.8 0 0 1 7.8 0Zm0 2.2A5.6 5.6 0 0 0 2.2 7.8v8.4A5.6 5.6 0 0 0 7.8 21.8h8.4a5.6 5.6 0 0 0 5.6-5.6V7.8a5.6 5.6 0 0 0-5.6-5.6H7.8Zm4.2 3.1a5.1 5.1 0 1 1 0 10.2 5.1 5.1 0 0 1 0-10.2Zm0 2.1a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm5.9-2.5a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z" transform="scale(.9)"/>'
    ],
    [
        'label' => 'X',
        'href' => 'https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareTitle,
        'icon' => $shareIconBase . 'x.png',
        'svg' => '<path d="M3 3h4.7l4.1 5.8L16.8 3H21l-7 9.4L21 21h-4.7l-4.4-6.2L7.2 21H3l7.4-9.9L3 3Zm3.2 1.8h1.7l10.1 14.3h-1.7L6.2 4.8Z"/>' ,
    ],
    [
        'label' => 'LinkedIn',
        'href' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl,
        'icon' => $shareIconBase . 'in.png',
        'svg' => '<path d="M4.5 8.2H.9V21h3.6V8.2Zm-1.8-1.8A2.1 2.1 0 1 0 2.7 2a2.1 2.1 0 0 0 0 4.4ZM21 21h-3.6v-6.6c0-1.6 0-3.7-2.3-3.7-2.4 0-2.7 1.8-2.7 3.6V21H8.8V8.2h3.5v1.7h.1c.5-.9 1.8-1.9 3.7-1.9 4 0 4.8 2.6 4.8 6V21Z"/>' ,
    ],
];

$relatedCourseIds = '479,472';
?>

<main class="trainer-detail-page">
    <div class="trainer-detail-page__container">
        <nav class="trainer-detail-page__breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url($homeLink); ?>">Home</a>
            <span class="trainer-detail-page__breadcrumb-sep">&gt;</span>
            <a href="<?php echo esc_url($aboutPageLink ?: $homeLink); ?>">Về chúng tôi</a>
            <span class="trainer-detail-page__breadcrumb-sep">&gt;</span>
            <span class="trainer-detail-page__breadcrumb-current"><?php echo esc_html($trainerName); ?></span>
        </nav>
    </div>

    <div class="trainer-detail-page__container">
        <header class="trainer-detail-page__header">
            <p class="trainer-detail-page__eyebrow">C-SHERPA</p>
            <h1 class="trainer-detail-page__title"><?php echo esc_html($trainerName); ?></h1>
        </header>
    </div>
    <div class="trainer-detail-page__container">
        <section class="trainer-detail-page__hero-card" aria-label="Trainer hero">
            <div class="trainer-detail-page__hero-art">
               <img src="<?php echo esc_url(get_field('trainer_photo', $postId)); ?>" alt="<?php echo esc_attr($trainerName); ?>" />
            </div>
        </section>
    </div>

        <section class="trainer-detail-page__bio" aria-label="Trainer introduction">
            <aside class="trainer-detail-page__share" aria-label="Share profile">
                <span class="trainer-detail-page__share-label">SHARE</span>
                <div class="trainer-detail-page__share-list">
                    <?php foreach ($shareLinks as $shareLink) : ?>
                        <a class="trainer-detail-page__share-btn" href="<?php echo esc_url($shareLink['href']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($shareLink['label']); ?>">
                            <?php if (!empty($shareLink['icon'])): ?>
                                <img class="trainer-detail-page__share-icon-image" src="<?php echo esc_url($shareLink['icon']); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async" />
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <?php echo $shareLink['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </svg>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="trainer-detail-page__bio-content">
                <?php if ($contentHtml !== '') : ?>
                    <div class="trainer-detail-page__content">
                        <?php echo $contentHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                <?php else : ?>
                    <div class="trainer-detail-page__content">
                        <p>Thông tin chi tiết của trainer đang được cập nhật.</p>
                    </div>
                <?php endif; ?>

                <?php if ($trainerLink !== '') : ?>
                    <div class="trainer-detail-page__bio-actions">
                        <a class="button primary trainer-detail-page__linkedin-btn" href="<?php echo esc_url($trainerLink); ?>" target="_blank" rel="noopener">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M4.5 8.2H.9V21h3.6V8.2Zm-1.8-1.8A2.1 2.1 0 1 0 2.7 2a2.1 2.1 0 0 0 0 4.4ZM21 21h-3.6v-6.6c0-1.6 0-3.7-2.3-3.7-2.4 0-2.7 1.8-2.7 3.6V21H8.8V8.2h3.5v1.7h.1c.5-.9 1.8-1.9 3.7-1.9 4 0 4.8 2.6 4.8 6V21Z"/>
                            </svg>
                            <span>Linkedin</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="trainer-detail-page__courses home-courses" aria-label="Related courses">
            <div class="trainer-detail-page__section-head container">
                <h2 class="trainer-detail-page__section-title">Những khóa học từ C-Sherpa</h2>
            </div>

            <div class="trainer-detail-page__courses-grid">
                <?php echo do_shortcode('[ux_products type="row" columns="4" columns__md="2" columns__sm="1" infinitive="false" equalize_box="true" ids="' . esc_attr($relatedCourseIds) . '" class="home-courses__grid related__trainer c-product-grid has-equal-box-heights"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>