<?php
/**
 * Custom search form used by get_search_form().
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;
?>
<form role="search" method="get" class="tcp-search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="tcp-search-input"><?php echo esc_html__('Tìm kiếm:', 'tcp-theme'); ?></label>
    <div class="tcp-search-form__field">
        <input
            type="search"
            id="tcp-search-input"
            class="tcp-search-form__input"
            name="s"
            value="<?php echo esc_attr(get_search_query()); ?>"
            placeholder="<?php echo esc_attr__('Tìm kiếm khóa học, bài viết...', 'tcp-theme'); ?>"
            autocomplete="off"
        />
        <button type="submit" class="tcp-search-form__submit" aria-label="<?php echo esc_attr__('Tìm kiếm', 'tcp-theme'); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 4a7 7 0 1 1 0 14 7 7 0 0 1 0-14Zm5.5 12.5L21 21"/>
            </svg>
        </button>
    </div>
</form>
