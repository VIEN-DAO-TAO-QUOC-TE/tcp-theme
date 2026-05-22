<?php

namespace TCP\Theme\Services\Ux;

use TCP\Theme\Core\Singleton;
use WP_Query;

defined('ABSPATH') || exit;

final class UxTrainers
{
    use Singleton;

    protected function init(): void
    {
        add_action('ux_builder_setup', [$this, 'registerUx'], 999);
        add_shortcode('tcp_trainers', [$this, 'renderShortcode']);
    }

    public function registerUx(): void
    {
        if (!function_exists('add_ux_builder_shortcode')) return;

        add_ux_builder_shortcode('tcp_trainers', [
            'name'     => 'TCP Trainers',
            'category' => 'TCP',
            'info'     => '{{ perpage }} trainers',
            'wrap'     => false,

            'options' => [
                'perpage' => [
                    'type' => 'textfield',
                    'heading' => 'Số lượng',
                    'default' => 8,
                ],

                'orderby' => [
                    'type' => 'select',
                    'heading' => 'Orderby',
                    'options' => [
                        'date' => 'Date',
                        'ID' => 'ID',
                        'title' => 'Title',
                        'rand' => 'Random',
                        'menu_order' => 'Menu order',
                    ],
                    'default' => 'menu_order',
                ],

                'order' => [
                    'type' => 'select',
                    'heading' => 'Order',
                    'options' => [
                        'DESC' => 'DESC',
                        'ASC'  => 'ASC',
                    ],
                    'default' => 'ASC',
                ],

                'trainer_ids' => [
                    'type' => 'select',
                    'heading' => 'Chọn trainer (tuỳ chọn)',
                    'param_name' => 'trainer_ids',
                    'config' => [
                        'multiple' => true,
                        'placeholder' => 'Select..',
                        'postSelect' => [
                            'post_type' => 'tcp_trainer',
                        ],
                    ],
                ],

                'span' => [
                    'type' => 'select',
                    'heading' => 'Col span (desktop)',
                    'options' => [
                        '12' => '12 (1 col)',
                        '6'  => '6 (2 col)',
                        '4'  => '4 (3 col)',
                        '3'  => '3 (4 col)',
                        '2'  => '2 (6 col)',
                    ],
                    'default' => '3',
                ],

                'span__sm' => [
                    'type' => 'select',
                    'heading' => 'Col span (mobile)',
                    'options' => [
                        '12' => '12',
                        '6'  => '6',
                        '4'  => '4',
                    ],
                    'default' => '12',
                ],

                'span__md' => [
                    'type' => 'select',
                    'heading' => 'Col span (tablet)',
                    'options' => [
                        '12' => '12',
                        '6'  => '6',
                        '4'  => '4',
                        '3'  => '3',
                    ],
                    'default' => '4',
                ],

                'row_label' => [
                    'type' => 'textfield',
                    'heading' => 'Row label',
                    'default' => 'home-trainers__row home-trainers__row--grid',
                    'full_width' => true,
                ],

                'row_class' => [
                    'type' => 'textfield',
                    'heading' => 'Row class',
                    'default' => 'home-trainers__row home-trainers__row--grid home-trainers__grid js-slider-nav',
                    'full_width' => true,
                ],

                'card_class' => [
                    'type' => 'textfield',
                    'heading' => 'Card class (ux_image_box)',
                    'default' => 'c-profile-card',
                    'full_width' => true,
                ],

                'card_template' => [
                    'type' => 'select',
                    'heading' => 'Card template',
                    'options' => [
                        'default' => 'Default',
                        'about'   => 'About',
                    ],
                    'default' => 'default',
                ],

                'meta_mode' => [
                    'type' => 'select',
                    'heading' => 'Meta line',
                    'options' => [
                        'role_only'     => 'Chỉ Role',
                        'role_company'  => 'Role • Company',
                        'company_only'  => 'Chỉ Company',
                    ],
                    'default' => 'role_only',
                ],

                'link_mode' => [
                    'type' => 'select',
                    'heading' => 'Link click card',
                    'options' => [
                        'none'   => 'Không link',
                        'acf'    => 'Dùng trainer_link (ACF)',
                        'single' => 'Link tới trang chi tiết (permalink)',
                    ],
                    'default' => 'acf',
                ],

                'class' => [
                    'type' => 'textfield',
                    'heading' => 'CSS class bổ sung (row)',
                    'default' => '',
                    'full_width' => true,
                ],
            ],
        ]);
    }

    public function renderShortcode($atts, $content = null): string
    {
        $atts = shortcode_atts([
            'perpage'     => 8,
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'trainer_ids' => '',
            'span'        => '3',
            'span__sm'    => '12',
            'span__md'    => '4',
            'row_label'   => 'home-trainers__row home-trainers__row--grid',
            'row_class'   => 'home-trainers__row home-trainers__row--grid home-trainers__grid js-slider-nav',
            'card_class'  => 'c-profile-card',
            'card_template' => 'default',
            'meta_mode'   => 'role_only',
            'link_mode'   => 'acf',
            'class'       => '',
        ], (array) $atts, 'tcp_trainers');

        $cardTemplate = strtolower(trim((string) $atts['card_template']));

        $perpage = (int) $atts['perpage'];
        if ($perpage === 0) $perpage = 8;

        $ids = $this->normalizeIds($atts['trainer_ids']);

        $args = [
            'post_type'           => 'tcp_trainer',
            'post_status'         => 'publish',
            'posts_per_page'      => $perpage,
            'orderby'             => $atts['orderby'] ?: 'menu_order',
            'order'               => strtoupper($atts['order']) === 'DESC' ? 'DESC' : 'ASC',
            'ignore_sticky_posts' => true,
        ];

        if (!empty($ids)) {
            $args['post__in'] = $ids;
            $args['orderby']  = 'post__in';
        }

        $q = new WP_Query($args);

        $rowLabel = trim((string) $atts['row_label']);
        $rowClass = trim((string) $atts['row_class'] . ' ' . (string) $atts['class']);

        $span     = preg_replace('/[^0-9]/', '', (string) $atts['span']) ?: '3';
        $spanMd   = preg_replace('/[^0-9]/', '', (string) $atts['span__md']) ?: '4';
        $spanSm   = preg_replace('/[^0-9]/', '', (string) $atts['span__sm']) ?: '12';

        $out  = '[row label="' . esc_attr($rowLabel) . '" class="align-equal ' . esc_attr($rowClass) . '"]';

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $pid   = get_the_ID();
                $name  = get_the_title($pid);

                // ACF fields
                $role    = (string) get_field('trainer_role', $pid);
                $company = (string) get_field('trainer_company', $pid);
                $photo   = get_field('trainer_photo_transparent', $pid); // image (ID/array)
                $link    = (string) get_field('trainer_link', $pid);
               
                $imgId = $this->resolveImageId($photo);

                // Meta line theo option
                $meta = $this->buildMeta((string) $atts['meta_mode'], $role, $company);

                // Link mode
                $href = '';
                if ($atts['link_mode'] === 'acf' && $link !== '') {
                    $href = $link;
                } elseif ($atts['link_mode'] === 'single') {
                    $href = get_permalink($pid);
                }

                if ($cardTemplate === 'about' && $href === '') {
                    $href = get_permalink($pid);
                }

                // Inner HTML (giữ đúng mẫu)
                $inner  = '<h4>' . esc_html($name) . '</h4>';
                if ($meta !== '') {
                    $inner .= '<p>' . esc_html($meta) . '</p>';
                }

                if ($cardTemplate === 'about' && $href !== '') {
                    $inner .= '<span class="button primary c-profile-card__cta">' . esc_html__('Xem thông tin', 'tcp-theme') . '</span>';
                }

                // Nếu muốn click toàn card -> wrap inner bằng <a>
                if ($href !== '') {
                    $inner = '<a class="c-profile-card__link" href="' . esc_url($href) . '">' . $inner . '</a>';
                }

                // Build ux_image_box shortcode
                $cardClass = trim((string) $atts['card_class']);
                if ($cardTemplate === 'about') {
                    $cardClass .= ' c-profile-card--about';
                }

                $box  = '[ux_image_box style="overlay"';
                $box .= $imgId ? ' img="' . (int) $imgId . '"' : '';
                $box .= ' text_pos="top" text_align="left"';
                $box .= ' class="' . esc_attr($cardClass) . '"]';
                $box .= $inner;
                $box .= '[/ux_image_box]';

                $out .= '[col span="' . esc_attr($span) . '" span__md="' . esc_attr($spanMd) . '" span__sm="' . esc_attr($spanSm) . '"]';
                $out .= $box;
                $out .= '[/col]';
            }
        }

        $out .= '[/row]';

        wp_reset_postdata();

        // Để Flatsome parse row/col/ux_image_box trước, rồi bỏ class "dark" chỉ trên profile card.
        $html = do_shortcode($out);
        return $this->stripDarkClassFromProfileCards($html);
    }

    private function stripDarkClassFromProfileCards(string $html): string
    {
        return (string) preg_replace_callback(
            '/class="([^"]*\bc-profile-card\b[^"]*)"/i',
            static function (array $m): string {
                $classes = preg_split('/\s+/', trim((string) $m[1])) ?: [];
                $classes = array_values(array_filter($classes, static fn(string $cls): bool => strtolower($cls) !== 'dark'));
                return 'class="' . esc_attr(implode(' ', $classes)) . '"';
            },
            $html
        );
    }

    private function normalizeIds($value): array
    {
        if (is_array($value)) {
            $ids = $value;
        } else {
            $value = (string) $value;
            if ($value === '') return [];
            $ids = explode(',', $value);
        }

        $ids = array_map('absint', $ids);
        return array_values(array_filter($ids));
    }

    private function resolveImageId($acfImage): int
    {
        if (is_numeric($acfImage)) return (int) $acfImage;
        if (is_array($acfImage) && !empty($acfImage['ID'])) return (int) $acfImage['ID'];
        return 0;
    }

    private function buildMeta(string $mode, string $role, string $company): string
    {
        $role = trim($role);
        $company = trim($company);

        if ($mode === 'company_only') return $company;

        if ($mode === 'role_company') {
            $parts = array_values(array_filter([$role, $company]));
            return implode(' • ', $parts);
        }

        // default role_only
        return $role;
    }
}