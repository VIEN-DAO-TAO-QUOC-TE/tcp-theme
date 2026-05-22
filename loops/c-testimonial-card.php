<?php
/**
 * Loop: Testimonial Card
 * Path: /wp-content/themes/tcp-theme/loops/c-testimonial-card.php
 *
 * Expected $args:
 * - quote (string)
 * - name (string)
 * - role (string) 
 * - avatar (string url)
 */

defined('ABSPATH') || exit;

$quote  = trim((string) ($args['quote'] ?? ''));
$name   = trim((string) ($args['name'] ?? ''));
$role   = trim((string) ($args['role'] ?? ''));
$avatar = trim((string) ($args['avatar'] ?? ''));

if ($quote === '' && $name === '' && $role === '') {
    return;
}

// Icon quote (đúng theme path)
$quote_icon = get_stylesheet_directory_uri() . '/assets/images/icons/quote.svg';
?>

<figure class="c-testimonial-card">
    <div class="c-testimonial-card__quote" aria-hidden="true">
        <img
            class="c-testimonial-card__quoteIcon"
            src="<?php echo esc_url($quote_icon); ?>"
            alt=""
            loading="lazy"
            decoding="async" />

        <?php if ($quote !== ''): ?>
            <blockquote class="c-testimonial-card__content">
                <?php
                // quote đã được normalize ở UI (wp_kses_post). Ở đây render an toàn:
                echo wp_kses_post(nl2br($quote));
                ?>
            </blockquote>
        <?php endif; ?>
    </div>

    <div class="divider-line"></div>

    <figcaption class="c-testimonial-card__footer">
        <span class="c-testimonial-card__avatar">
            <?php if ($avatar !== ''): ?>
                <img
                    src="<?php echo esc_url($avatar); ?>"
                    alt="<?php echo esc_attr('Ảnh đại diện ' . $name); ?>"
                    loading="lazy"
                    decoding="async" />
            <?php else: ?>
                <span class="c-testimonial-card__avatarPlaceholder" aria-hidden="true"></span>
            <?php endif; ?>
        </span>

        <div class="c-testimonial-card__author">
            <?php if ($name !== ''): ?>
                <p class="c-testimonial-card__name"><?php echo esc_html($name); ?></p>
            <?php endif; ?>

            <?php if ($role !== ''): ?>
                <p class="c-testimonial-card__meta"><?php echo esc_html($role); ?></p>
            <?php endif; ?>
        </div>
    </figcaption>
</figure>
