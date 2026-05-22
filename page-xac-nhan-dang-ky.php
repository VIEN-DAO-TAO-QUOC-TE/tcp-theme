<?php
/**
 * Template Name: TCP - Xac Nhan Dang Ky
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

$fieldImageUrl = static function (string $key, string $default = ''): string {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($key);
    if (is_string($value)) {
        $value = trim($value);
        return $value === '' ? $default : $value;
    }

    if (is_array($value) && isset($value['url']) && is_string($value['url'])) {
        $url = trim($value['url']);
        return $url === '' ? $default : $url;
    }

    return $default;
};

$fieldLines = static function (string $key, string $default = ''): array {
    $source = $default;
    if (function_exists('get_field')) {
        $value = get_field($key);
        if (is_string($value) && trim($value) !== '') {
            $source = trim($value);
        }
    }

    $source = html_entity_decode((string) $source, ENT_QUOTES, 'UTF-8');
    $source = preg_replace('/<br\s*\/?>/i', "\n", $source) ?? $source;
    $source = wp_strip_all_tags($source);
    $parts = preg_split('/\r\n|\r|\n/', $source) ?: [];

    return array_values(array_filter(array_map(static fn(string $line): string => trim($line), $parts)));
};

$eyebrow = $fieldText('tcp_confirm_eyebrow', 'DANG KY THANH CONG');
$titleDefault = "Talent Connect Plus\nda nhan thong tin cua ban";
$titleLines = $fieldLines('tcp_confirm_title', $titleDefault);
$description = $fieldText('tcp_confirm_description', 'Chung toi se lien he voi ban trong thoi gian som nhat.');
$buttonText = $fieldText('tcp_confirm_button_text', 'Trở về trang chủ');
$buttonLink = $fieldText('tcp_confirm_button_link', home_url('/'));
$visualBg = $fieldText('tcp_confirm_visual_bg', '#E5E7EB');
$visualImage = $fieldImageUrl('tcp_confirm_visual_image', TCP_THEME_URI . 'assets/images/icons/success.svg');
$visualAlt = $fieldText('tcp_confirm_visual_alt', 'Thong bao dang ky thanh cong');
?>

<main id="main" class="tcp-confirm-page" role="main">
    <div class="tcp-confirm-page__inner container">
        <section class="tcp-confirm-page__content" aria-label="Confirmation content">
            <?php if ($eyebrow !== '') : ?>
                <p class="tcp-confirm-page__eyebrow"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>

            <?php if (!empty($titleLines)) : ?>
                <h1 class="tcp-confirm-page__title">
                    <?php foreach ($titleLines as $line) : ?>
                        <span><?php echo esc_html($line); ?></span>
                    <?php endforeach; ?>
                </h1>
            <?php endif; ?>

            <?php if ($description !== '') : ?>
                <p class="tcp-confirm-page__desc"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if ($buttonText !== '') : ?>
                <a class="tcp-confirm-page__button" href="<?php echo esc_url($buttonLink); ?>"><?php echo esc_html($buttonText); ?></a>
            <?php endif; ?>
        </section>

        <section class="tcp-confirm-page__visual" aria-label="Confirmation illustration">
            <div class="tcp-confirm-page__visual-card" style="--tcp-confirm-visual-bg: <?php echo esc_attr($visualBg); ?>;">
                <?php if ($visualImage !== '') : ?>
                    <img src="<?php echo esc_url($visualImage); ?>" alt="<?php echo esc_attr($visualAlt); ?>" loading="lazy" />
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
