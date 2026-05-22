<?php
/**
 * Language Toggle (no dropdown): Globe + TARGET language code
 * - If current is VI => show EN (link to EN)
 * - If current is EN => show VI (link to VI)
 */

$current_code = 'VI';
$current_url  = home_url('/');
$target_code  = 'EN';
$target_url   = home_url('/');

if (function_exists('pll_the_languages')) {
  $languages = pll_the_languages(['raw' => 1]);

  $vi = null; $en = null;

  foreach ($languages as $lang) {
    $slug = strtoupper($lang['slug']); // vi/en -> VI/EN
    if ($slug === 'VI') $vi = $lang;
    if ($slug === 'EN') $en = $lang;

    if (!empty($lang['current_lang'])) {
      $current_code = $slug ?: 'VI';
      $current_url  = $lang['url'];
    }
  }

  // target label + url
  if ($current_code === 'VI') {
    $target_code = 'EN';
    $target_url  = !empty($en['url']) ? $en['url'] : $current_url;
  } else {
    $target_code = 'VI';
    $target_url  = !empty($vi['url']) ? $vi['url'] : $current_url;
  }

} elseif (function_exists('wpml_get_active_languages_filter') || has_filter('wpml_active_languages')) {

  $languages = apply_filters('wpml_active_languages', null, ['skip_missing' => 0]);
  $vi = null; $en = null;

  if (is_array($languages)) {
    foreach ($languages as $lang) {
      $code = strtoupper($lang['language_code']); // vi/en -> VI/EN
      if ($code === 'VI') $vi = $lang;
      if ($code === 'EN') $en = $lang;

      if (!empty($lang['active'])) {
        $current_code = $code ?: 'VI';
        $current_url  = $lang['url'];
      }
    }

    if ($current_code === 'VI') {
      $target_code = 'EN';
      $target_url  = !empty($en['url']) ? $en['url'] : $current_url;
    } else {
      $target_code = 'VI';
      $target_url  = !empty($vi['url']) ? $vi['url'] : $current_url;
    }
  }
}
?>

<li class="header-language-toggle">
  <a class="nav-top-link header-language-toggle__link"
     href="<?php echo esc_url($target_url ?: $current_url); ?>"
     aria-label="<?php echo esc_attr__('Switch language', 'flatsome'); ?>">
    <span class="header-language-toggle__icon icon-globe" aria-hidden="true"></span>
    <span class="header-language-toggle__text"><?php echo esc_html($target_code); ?></span>
  </a>
</li>
