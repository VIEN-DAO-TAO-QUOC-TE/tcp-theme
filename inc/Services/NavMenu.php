<?php
namespace TCP\Theme\Services;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class NavMenu
{
  use Singleton;

  public function register(): void
  {
    add_filter('nav_menu_link_attributes', [$this, 'link_attrs'], 10, 4);
    add_filter('nav_menu_css_class', [$this, 'li_class'], 10, 4);
  }

  public function li_class($classes, $item, $args, $depth)
  {
    if (($args->theme_location ?? '') === 'primary') {
      $classes[] = 'tcp-nav-item';
    }
    return $classes;
  }

  public function link_attrs($atts, $item, $args, $depth)
  {
    if (($args->theme_location ?? '') === 'primary') {
      $base = 'text-sm font-medium text-foreground/80 hover:text-foreground transition-colors';
      $base .= ' px-2 py-1 rounded-md hover:bg-foreground/5';
      if (in_array('current-menu-item', $item->classes ?? [], true)) {
        $base .= ' text-foreground';
      }
      $atts['class'] = trim(($atts['class'] ?? '') . ' ' . $base);
    }
    return $atts;
  }
}
