<?php
/**
 * Template Name: TCP - Doanh Nghiep
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

$eyebrow = $fieldText('tcp_business_eyebrow', 'DOANH NGHIỆP');
$title = $fieldText('tcp_business_title', 'Sản phẩm dành cho doanh nghiệp');
$description = $fieldHtml('tcp_business_description', '');
$cards = $fieldRows('tcp_business_cards');
$buttonTextFallback = $fieldText('tcp_business_button_text', 'Tìm hiểu ngay');
$emptyState = $fieldText('tcp_business_empty_state', 'Chưa có nội dung nào được cấu hình.');
?>



<main id="main" class="site-main tcp-business-page" role="main">
    <div class="container doanh-nghiep-page">
        <div class="category-archive__breadcrumb tcp-business-page__breadcrumb" style="padding-top:40px;">
            <?php echo do_shortcode('[rank_math_breadcrumb]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>

        <div class="category-posts category-posts--child-grid">
            <header class="archive-header">
                <?php if ($eyebrow !== '') : ?>
                    <p class="archive-eyebrow"><?php echo esc_html(mb_strtoupper($eyebrow)); ?></p>
                <?php endif; ?>

                <h1 class="page-title"><?php echo esc_html($title); ?></h1>

                <?php if ($description !== '') : ?>
                    <div class="category-description"><?php echo wp_kses_post($description); ?></div>
                <?php endif; ?>
            </header>

            <?php if (!empty($cards)) : ?>
                <section class="section category-grid c-section" id="section_doanh_nghiep">
                    <div class="section-bg fill"></div>
                    <div class="section-content relative">
                        <div class="row category-grid__grid">
                            <?php foreach ($cards as $card) : ?>
                                <?php
                                $cardTitle = isset($card['title']) && is_string($card['title']) ? trim($card['title']) : '';
                                $cardExcerpt = isset($card['excerpt']) && is_string($card['excerpt']) ? trim($card['excerpt']) : '';
                                $cardLink = isset($card['link']) && is_string($card['link']) && trim($card['link']) !== '' ? trim($card['link']) : '#';
                                $cardButtonText = isset($card['button_text']) && is_string($card['button_text']) && trim($card['button_text']) !== '' ? trim($card['button_text']) : $buttonTextFallback;
                                $cardImage = '';
                                if (isset($card['image']) && is_string($card['image'])) {
                                    $cardImage = trim($card['image']);
                                } elseif (isset($card['image']) && is_array($card['image']) && isset($card['image']['url']) && is_string($card['image']['url'])) {
                                    $cardImage = trim($card['image']['url']);
                                }
                                if ($cardImage === '') {
                                    $cardImage = TCP_THEME_URI . 'screenshot.png';
                                }
                                $postCount = isset($card['post_count']) && is_numeric($card['post_count']) ? (int)$card['post_count'] : null;
                                ?>
                                <div class="col category-grid__card-col medium-4 small-12 large-4">
                                    <div class="col-inner">
                                        <div class="box has-hover c-media-card box-overlay dark box-text-bottom">
                                            <div class="box-image">
                                                <img width="250" height="359" src="<?php echo esc_url($cardImage); ?>" alt="<?php echo esc_attr($cardTitle); ?>" loading="lazy" />
                                                <div class="overlay"></div>
                                            </div>
                                            <div class="box-text text-left">
                                                <div class="box-text-inner">
                                                    <div class="text c-media-card__title">
                                                        <h4><?php echo esc_html($cardTitle); ?></h4>
                                                    </div>
                                                    <div class="text c-media-card__desc">
                                                        <?php echo esc_html($cardExcerpt); ?>
                                                    </div>
                                                    <?php if ($postCount !== null): ?>
                                                        <div class="text c-media-card__meta">
                                                            <?php echo esc_html($postCount); ?> + Bài viết
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?php echo esc_url($cardLink); ?>" class="button primary expand category-grid__button" style="border-radius:10px;">
                                                        <span><?php echo esc_html($cardButtonText); ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <style>
                        .doanh-nghiep-page #section_doanh_nghiep {
                            padding-top: 30px;
                            padding-bottom: 30px;
                        }
                    </style>
                </section>
            <?php else : ?>
                <div class="tcp-business-page__empty"><?php echo esc_html($emptyState); ?></div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
