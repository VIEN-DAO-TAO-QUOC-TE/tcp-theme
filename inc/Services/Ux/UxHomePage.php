<?php

namespace TCP\Theme\Services\Ux;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Services\Ux\Sections\BooksSection;
use TCP\Theme\Services\Ux\Sections\CardInfoSection;
use TCP\Theme\Services\Ux\Sections\ContactCtaSection;
use TCP\Theme\Services\Ux\Sections\CorporateTrainingSection;
use TCP\Theme\Services\Ux\Sections\CoursesSection;
use TCP\Theme\Services\Ux\Sections\HeroSection;
use TCP\Theme\Services\Ux\Sections\LogosSection;
use TCP\Theme\Services\Ux\Sections\MainCtaSection;
use TCP\Theme\Services\Ux\Sections\MiniCtaSection;
use TCP\Theme\Services\Ux\Sections\TestimonialsSection;
use TCP\Theme\Services\Ux\Sections\TrainersSection;

defined('ABSPATH') || exit;

final class UxHomePage
{
    use Singleton;
    use AcfFieldsTrait;

    protected function init(): void
    {
        add_shortcode('tcp_home_sections', [$this, 'renderShortcode']);
        add_action('ux_builder_setup', [$this, 'registerUx'], 999);
        add_action('acf/init', [$this, 'registerFieldGroup']);
    }

    public function registerUx(): void
    {
        if (!function_exists('add_ux_builder_shortcode')) {
            return;
        }

        add_ux_builder_shortcode('tcp_home_sections', [
            'name'     => 'TCP Home Sections',
            'category' => 'TCP',
            'info'     => 'ACF-driven homepage sections',
            'wrap'     => false,
            'options'  => [
                'page_id' => [
                    'type'        => 'textfield',
                    'heading'     => 'Page ID (optional)',
                    'description' => 'Leave empty to use current page/front page.',
                    'default'     => '',
                ],
            ],
        ]);
    }

    public function registerFieldGroup(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key'      => 'group_tcp_home_sections',
            'title'    => 'TCP Home Sections',
            'fields'   => [
                [
                    'key'   => 'field_tcp_home_tab_hero',
                    'label' => 'Hero',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_hero_title',
                    'label'         => 'Hero title',
                    'name'          => 'tcp_home_hero_title',
                    'type'          => 'text',
                    'default_value' => 'Nen tang hoc tap ung dung tich hop',
                ],
                [
                    'key'           => 'field_tcp_home_hero_highlight',
                    'label'         => 'Hero highlight',
                    'name'          => 'tcp_home_hero_highlight',
                    'type'          => 'text',
                    'default_value' => 'co van, dao tao va tu van',
                ],
                [
                    'key'          => 'field_tcp_home_hero_cards',
                    'label'        => 'Hero cards (desktop/tablet)',
                    'name'         => 'tcp_home_hero_cards',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'button_label' => 'Add hero card',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_tcp_home_hero_card_image',
                            'label'         => 'Image',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'           => 'field_tcp_home_hero_card_background_image',
                            'label'         => 'Background image',
                            'name'          => 'background_image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'   => 'field_tcp_home_hero_card_title',
                            'label' => 'Title',
                            'name'  => 'title',
                            'type'  => 'text',
                        ],
                        [
                            'key'           => 'field_tcp_home_hero_card_cta_text',
                            'label'         => 'CTA text',
                            'name'          => 'cta_text',
                            'type'          => 'text',
                            'default_value' => 'Tim hieu ngay',
                        ],
                        [
                            'key'   => 'field_tcp_home_hero_card_cta_url',
                            'label' => 'CTA URL',
                            'name'  => 'cta_url',
                            'type'  => 'url',
                        ],
                    ],
                ],
                [
                    'key'          => 'field_tcp_home_hero_cards_mobile',
                    'label'        => 'Hero cards (mobile)',
                    'name'         => 'tcp_home_hero_cards_mobile',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'button_label' => 'Add hero card mobile',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_tcp_home_hero_card_mobile_image',
                            'label'         => 'Image',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'           => 'field_tcp_home_hero_card_mobile_background_image',
                            'label'         => 'Background image',
                            'name'          => 'background_image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'   => 'field_tcp_home_hero_card_mobile_title',
                            'label' => 'Title',
                            'name'  => 'title',
                            'type'  => 'text',
                        ],
                        [
                            'key'           => 'field_tcp_home_hero_card_mobile_cta_text',
                            'label'         => 'CTA text',
                            'name'          => 'cta_text',
                            'type'          => 'text',
                            'default_value' => 'Tim hieu ngay',
                        ],
                        [
                            'key'   => 'field_tcp_home_hero_card_mobile_cta_url',
                            'label' => 'CTA URL',
                            'name'  => 'cta_url',
                            'type'  => 'url',
                        ],
                    ],
                ],
                [
                    'key'          => 'field_tcp_home_hero_desktop_slides',
                    'label'        => 'Hero desktop slides',
                    'name'         => 'tcp_home_hero_desktop_slides',
                    'type'         => 'gallery',
                    'preview_size' => 'medium',
                    'return_format'=> 'id',
                ],
                [
                    'key'          => 'field_tcp_home_hero_mobile_slides',
                    'label'        => 'Hero mobile slides',
                    'name'         => 'tcp_home_hero_mobile_slides',
                    'type'         => 'gallery',
                    'preview_size' => 'medium',
                    'return_format'=> 'id',
                ],

                [
                    'key'   => 'field_tcp_home_tab_courses',
                    'label' => 'Courses',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_courses_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_courses_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'KHOA HOC',
                ],
                [
                    'key'           => 'field_tcp_home_courses_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_courses_title',
                    'type'          => 'text',
                    'default_value' => 'Nhung ky nang',
                ],
                [
                    'key'           => 'field_tcp_home_courses_highlight',
                    'label'         => 'Highlight',
                    'name'          => 'tcp_home_courses_highlight',
                    'type'          => 'text',
                    'default_value' => 'dinh hinh tu duy va kha nang lanh dao',
                ],
                [
                    'key'           => 'field_tcp_home_courses_product_ids',
                    'label'         => 'Product IDs (comma separated)',
                    'name'          => 'tcp_home_courses_product_ids',
                    'type'          => 'text',
                    'default_value' => '479,472,517,487',
                ],
                [
                    'key'           => 'field_tcp_home_courses_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_courses_button_text',
                    'type'          => 'text',
                    'default_value' => 'Xem toan bo khoa hoc',
                ],
                [
                    'key'           => 'field_tcp_home_courses_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_courses_button_url',
                    'type'          => 'url',
                    'default_value' => '/cac-khoa-hoc/',
                ],

                [
                    'key'   => 'field_tcp_home_tab_main_cta',
                    'label' => 'Main CTA',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_title',
                    'label'         => 'Main CTA title',
                    'name'          => 'tcp_home_main_cta_title',
                    'type'          => 'text',
                    'default_value' => 'Bat dau hanh trinh tro thanh nha lanh dao',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_desc',
                    'label'         => 'Main CTA description',
                    'name'          => 'tcp_home_main_cta_desc',
                    'type'          => 'textarea',
                    'rows'          => 3,
                    'default_value' => 'Dang ky de nhan uu dai va cac khoa hoc moi nhat tu Talent Connect Plus',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_btn_1_text',
                    'label'         => 'Button 1 text',
                    'name'          => 'tcp_home_main_cta_btn_1_text',
                    'type'          => 'text',
                    'default_value' => 'Tim hieu them',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_btn_1_url',
                    'label'         => 'Button 1 URL',
                    'name'          => 'tcp_home_main_cta_btn_1_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_btn_2_text',
                    'label'         => 'Button 2 text',
                    'name'          => 'tcp_home_main_cta_btn_2_text',
                    'type'          => 'text',
                    'default_value' => 'Dang ky ngay',
                ],
                [
                    'key'           => 'field_tcp_home_main_cta_btn_2_url',
                    'label'         => 'Button 2 URL',
                    'name'          => 'tcp_home_main_cta_btn_2_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],

                [
                    'key'   => 'field_tcp_home_tab_trainers',
                    'label' => 'Trainers',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_trainers_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_trainers_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'C-SHERPA TRAINER',
                ],
                [
                    'key'           => 'field_tcp_home_trainers_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_trainers_title',
                    'type'          => 'text',
                    'default_value' => 'Duoc thiet ke va giang day',
                ],
                [
                    'key'           => 'field_tcp_home_trainers_highlight',
                    'label'         => 'Highlight',
                    'name'          => 'tcp_home_trainers_highlight',
                    'type'          => 'text',
                    'default_value' => 'boi cac chuyen gia hang dau trong linh vuc',
                ],
                [
                    'key'               => 'field_tcp_home_trainers_stats',
                    'label'             => 'Stats',
                    'name'              => 'tcp_home_trainers_stats',
                    'type'              => 'repeater',
                    'layout'            => 'table',
                    'button_label'      => 'Add stat',
                    'min'               => 0,
                    'max'               => 6,
                    'sub_fields'        => [
                        [
                            'key'   => 'field_tcp_home_trainers_stats_value',
                            'label' => 'Value',
                            'name'  => 'value',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_tcp_home_trainers_stats_label',
                            'label' => 'Label',
                            'name'  => 'label',
                            'type'  => 'text',
                        ],
                    ],
                ],
                [
                    'key'           => 'field_tcp_home_trainers_ids',
                    'label'         => 'Trainer IDs (comma separated)',
                    'name'          => 'tcp_home_trainers_ids',
                    'type'          => 'text',
                    'default_value' => '429,369,567,568',
                ],
                [
                    'key'           => 'field_tcp_home_trainers_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_trainers_button_text',
                    'type'          => 'text',
                    'default_value' => 'Ve chung toi',
                ],
                [
                    'key'           => 'field_tcp_home_trainers_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_trainers_button_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],

                [
                    'key'   => 'field_tcp_home_tab_mini_cta',
                    'label' => 'Mini CTA Banner',
                    'type'  => 'tab',
                ],
                [
                    'key'   => 'field_tcp_home_tab_books',
                    'label' => 'Books (Above Mini CTA)',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_books_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_books_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'SACH KY NANG',
                ],
                [
                    'key'           => 'field_tcp_home_books_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_books_title',
                    'type'          => 'text',
                    'default_value' => 'Sach hay ve ky nang quan ly va lanh dao',
                ],
                [
                    'key'           => 'field_tcp_home_books_highlight',
                    'label'         => 'Highlight',
                    'name'          => 'tcp_home_books_highlight',
                    'type'          => 'text',
                    'default_value' => '',
                ],
                [
                    'key'           => 'field_tcp_home_books_product_ids',
                    'label'         => 'Product IDs (comma separated)',
                    'name'          => 'tcp_home_books_product_ids',
                    'type'          => 'text',
                    'default_value' => '',
                ],
                [
                    'key'           => 'field_tcp_home_books_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_books_button_text',
                    'type'          => 'text',
                    'default_value' => 'Xem toan bo',
                ],
                [
                    'key'           => 'field_tcp_home_books_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_books_button_url',
                    'type'          => 'url',
                    'default_value' => '/shop/',
                ],
                [
                    'key'           => 'field_tcp_home_mini_cta_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_mini_cta_title',
                    'type'          => 'text',
                    'default_value' => 'Ban da san sang danh thuc nha lanh dao trong ban?',
                ],
                [
                    'key'           => 'field_tcp_home_mini_cta_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_mini_cta_button_text',
                    'type'          => 'text',
                    'default_value' => 'Dang ky ngay',
                ],
                [
                    'key'           => 'field_tcp_home_mini_cta_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_mini_cta_button_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],
                [
                    'key'           => 'field_tcp_home_mini_cta_image',
                    'label'         => 'Banner image',
                    'name'          => 'tcp_home_mini_cta_image',
                    'type'          => 'image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                    'library'       => 'all',
                ],

                [
                    'key'   => 'field_tcp_home_tab_testimonials',
                    'label' => 'Testimonials',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_testimonials_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_testimonials_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'CAM NHAN HOC VIEN',
                ],
                [
                    'key'           => 'field_tcp_home_testimonials_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_testimonials_title',
                    'type'          => 'text',
                    'default_value' => 'Hoc vien Talent Connect Plus',
                ],
                [
                    'key'           => 'field_tcp_home_testimonials_highlight',
                    'label'         => 'Highlight',
                    'name'          => 'tcp_home_testimonials_highlight',
                    'type'          => 'text',
                    'default_value' => 'noi gi?',
                ],
                [
                    'key'           => 'field_tcp_home_testimonials_ids',
                    'label'         => 'Review IDs (comma separated)',
                    'name'          => 'tcp_home_testimonials_ids',
                    'type'          => 'text',
                    'default_value' => '',
                ],
                [
                    'key'           => 'field_tcp_home_testimonials_perpage',
                    'label'         => 'Reviews per page',
                    'name'          => 'tcp_home_testimonials_perpage',
                    'type'          => 'number',
                    'default_value' => 6,
                    'min'           => 1,
                    'max'           => 20,
                ],

                [
                    'key'   => 'field_tcp_home_tab_card_info',
                    'label' => 'Card Info',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_card_info_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_card_info_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'Ket noi tri thuc',
                ],
                [
                    'key'           => 'field_tcp_home_card_info_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_card_info_title',
                    'type'          => 'text',
                    'default_value' => 'Kinh nghiem lanh dao tu cac CEOs ky cuu',
                ],
                [
                    'key'           => 'field_tcp_home_card_info_desc',
                    'label'         => 'Description',
                    'name'          => 'tcp_home_card_info_desc',
                    'type'          => 'textarea',
                    'rows'          => 3,
                    'default_value' => 'Cap nhat chuong trinh va noi dung moi nhat tu Talent Connect Plus.',
                ],
                [
                    'key'           => 'field_tcp_home_card_info_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_card_info_button_text',
                    'type'          => 'text',
                    'default_value' => 'Kham pha ngay',
                ],
                [
                    'key'           => 'field_tcp_home_card_info_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_card_info_button_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],
                [
                    'key'          => 'field_tcp_home_card_info_cards',
                    'label'        => 'Cards',
                    'name'         => 'tcp_home_card_info_cards',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'button_label' => 'Add card',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_tcp_home_card_info_card_image',
                            'label'         => 'Image',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'   => 'field_tcp_home_card_info_card_title',
                            'label' => 'Title',
                            'name'  => 'title',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_tcp_home_card_info_card_desc',
                            'label' => 'Description',
                            'name'  => 'desc',
                            'type'  => 'textarea',
                            'rows'  => 3,
                        ],
                        [
                            'key'   => 'field_tcp_home_card_info_card_meta',
                            'label' => 'Meta',
                            'name'  => 'meta',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_tcp_home_card_info_card_url',
                            'label' => 'URL',
                            'name'  => 'url',
                            'type'  => 'url',
                        ],
                    ],
                ],

                [
                    'key'   => 'field_tcp_home_tab_logos',
                    'label' => 'Logos',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_logos_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_logos_title',
                    'type'          => 'text',
                    'default_value' => 'Duoc tin tuong va lua chon tu cac doanh nghiep hang dau',
                ],
                [
                    'key'          => 'field_tcp_home_logos_items',
                    'label'        => 'Logos',
                    'name'         => 'tcp_home_logos_items',
                    'type'         => 'repeater',
                    'layout'       => 'table',
                    'button_label' => 'Add logo',
                    'sub_fields'   => [
                        [
                            'key'           => 'field_tcp_home_logo_image',
                            'label'         => 'Logo image',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'id',
                            'preview_size'  => 'medium',
                        ],
                    ],
                ],

                [
                    'key'   => 'field_tcp_home_tab_corporate',
                    'label' => 'Corporate Training',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_corporate_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'DOANH NGHIEP',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_corporate_title',
                    'type'          => 'text',
                    'default_value' => 'Chuong trinh dao tao doanh nghiep',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_desc',
                    'label'         => 'Description',
                    'name'          => 'tcp_home_corporate_desc',
                    'type'          => 'textarea',
                    'rows'          => 4,
                    'default_value' => 'Talent Connect Plus phat trien cac chuong trinh dao tao phu hop voi tung doanh nghiep.',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_button_text',
                    'label'         => 'Button text',
                    'name'          => 'tcp_home_corporate_button_text',
                    'type'          => 'text',
                    'default_value' => 'Tim hieu them',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_button_url',
                    'label'         => 'Button URL',
                    'name'          => 'tcp_home_corporate_button_url',
                    'type'          => 'url',
                    'default_value' => '#',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_left_gallery',
                    'label'         => 'Left gallery (3 images)',
                    'name'          => 'tcp_home_corporate_left_gallery',
                    'type'          => 'gallery',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_center_gallery',
                    'label'         => 'Center gallery (2 images)',
                    'name'          => 'tcp_home_corporate_center_gallery',
                    'type'          => 'gallery',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ],
                [
                    'key'           => 'field_tcp_home_corporate_right_gallery',
                    'label'         => 'Right gallery (3 images)',
                    'name'          => 'tcp_home_corporate_right_gallery',
                    'type'          => 'gallery',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ],

                [
                    'key'   => 'field_tcp_home_tab_contact',
                    'label' => 'Contact CTA',
                    'type'  => 'tab',
                ],
                [
                    'key'           => 'field_tcp_home_contact_image',
                    'label'         => 'Contact image',
                    'name'          => 'tcp_home_contact_image',
                    'type'          => 'image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ],
                [
                    'key'           => 'field_tcp_home_contact_eyebrow',
                    'label'         => 'Eyebrow',
                    'name'          => 'tcp_home_contact_eyebrow',
                    'type'          => 'text',
                    'default_value' => 'LIEN HE',
                ],
                [
                    'key'           => 'field_tcp_home_contact_title',
                    'label'         => 'Title',
                    'name'          => 'tcp_home_contact_title',
                    'type'          => 'text',
                    'default_value' => "Ket noi cung\nTalent Connect Plus",
                ],
                [
                    'key'           => 'field_tcp_home_contact_form_shortcode',
                    'label'         => 'Form shortcode',
                    'name'          => 'tcp_home_contact_form_shortcode',
                    'type'          => 'text',
                    'default_value' => '[contact-form-7 id="193"]',
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'page',
                        'operator' => '==',
                        'value'    => '584',
                    ],
                ],
            ],
            'position' => 'acf_after_title',
            'style'    => 'seamless',
            'active'   => true,
        ]);
    }

    public function renderShortcode($atts, $content = null): string
    {
        $atts = shortcode_atts([
            'page_id' => '',
        ], (array) $atts, 'tcp_home_sections');

        $pageId = absint((string) $atts['page_id']);
        if ($pageId <= 0) {
            $pageId = get_queried_object_id();
        }
        if ($pageId <= 0) {
            $pageId = (int) get_option('page_on_front');
        }

        // Section: Hero (desktop/tablet + mobile)
        $heroTitle = $this->field($pageId, 'tcp_home_hero_title', 'Nen tang hoc tap ung dung tich hop');
        $heroHighlight = $this->field($pageId, 'tcp_home_hero_highlight', 'co van, dao tao va tu van');
        $heroCardsTabletDesktop = $this->heroCards($pageId, 'tcp_home_hero_cards');
        $heroCardsMobile = $this->heroCards($pageId, 'tcp_home_hero_cards_mobile');

        if (empty($heroCardsTabletDesktop)) {
            $heroCardsTabletDesktop = $this->defaultHeroCards('desktop');
        }
        if (empty($heroCardsMobile)) {
            $heroCardsMobile = $this->defaultHeroCards('mobile');
        }
        if (empty($heroCardsMobile)) {
            $heroCardsMobile = $heroCardsTabletDesktop;
        }
        $heroSlidesTabletDesktop = $this->galleryIds($pageId, 'tcp_home_hero_desktop_slides');
        $heroSlidesMobile = $this->galleryIds($pageId, 'tcp_home_hero_mobile_slides');
        if (empty($heroSlidesTabletDesktop)) {
            $heroSlidesTabletDesktop = [72, 71, 67, 66];
        }
        if (empty($heroSlidesMobile)) {
            $heroSlidesMobile = [304, 305, 306];
        }

        // Section: Courses (desktop/tablet/mobile share same content source)
        $coursesEyebrow = $this->field($pageId, 'tcp_home_courses_eyebrow', 'KHOA HOC');
        $coursesTitle = $this->field($pageId, 'tcp_home_courses_title', 'Nhung ky nang');
        $coursesHighlight = $this->field($pageId, 'tcp_home_courses_highlight', 'dinh hinh tu duy va kha nang lanh dao');
        $courseProductIds = $this->field($pageId, 'tcp_home_courses_product_ids', '479,472,517,487');
        $coursesBtnText = $this->field($pageId, 'tcp_home_courses_button_text', 'Xem toan bo khoa hoc');
        $coursesBtnUrl = $this->field($pageId, 'tcp_home_courses_button_url', '/cac-khoa-hoc/');

        // Section: Main CTA (desktop/tablet/mobile share same content source)
        $mainCtaTitle = $this->field($pageId, 'tcp_home_main_cta_title', 'Bat dau hanh trinh tro thanh nha lanh dao');
        $mainCtaDesc = $this->field($pageId, 'tcp_home_main_cta_desc', 'Dang ky de nhan uu dai va cac khoa hoc moi nhat tu Talent Connect Plus');
        $mainCtaBtn1Text = $this->field($pageId, 'tcp_home_main_cta_btn_1_text', 'Tim hieu them');
        $mainCtaBtn1Url = $this->field($pageId, 'tcp_home_main_cta_btn_1_url', '#');
        $mainCtaBtn2Text = $this->field($pageId, 'tcp_home_main_cta_btn_2_text', 'Dang ky ngay');
        $mainCtaBtn2Url = $this->field($pageId, 'tcp_home_main_cta_btn_2_url', '#');

        // Section: Trainers (desktop/tablet/mobile share same content source)
        $trainersEyebrow = $this->field($pageId, 'tcp_home_trainers_eyebrow', 'C-SHERPA TRAINER');
        $trainersTitle = $this->field($pageId, 'tcp_home_trainers_title', 'Duoc thiet ke va giang day');
        $trainersHighlight = $this->field($pageId, 'tcp_home_trainers_highlight', 'boi cac chuyen gia hang dau trong linh vuc');
        $trainersIds = $this->field($pageId, 'tcp_home_trainers_ids', '429,369,567,568');
        $trainersBtnText = $this->field($pageId, 'tcp_home_trainers_button_text', 'Ve chung toi');
        $trainersBtnUrl = $this->field($pageId, 'tcp_home_trainers_button_url', '#');

        // Section: Books + Mini CTA (desktop/tablet/mobile share same content source)
        $booksEyebrow = $this->field($pageId, 'tcp_home_books_eyebrow', 'SACH KY NANG');
        $booksTitle = $this->field($pageId, 'tcp_home_books_title', 'Sach hay ve ky nang quan ly va lanh dao');
        $booksHighlight = $this->field($pageId, 'tcp_home_books_highlight', '');
        $bookProductIds = $this->field($pageId, 'tcp_home_books_product_ids', '');
        $booksBtnText = $this->field($pageId, 'tcp_home_books_button_text', 'Xem toan bo');
        $booksBtnUrl = $this->field($pageId, 'tcp_home_books_button_url', '/shop/');

        $miniCtaTitle = $this->field($pageId, 'tcp_home_mini_cta_title', 'Ban da san sang danh thuc nha lanh dao trong ban?');
        $miniCtaBtnText = $this->field($pageId, 'tcp_home_mini_cta_button_text', 'Dang ky ngay');
        $miniCtaBtnUrl = $this->field($pageId, 'tcp_home_mini_cta_button_url', '#');
        $miniCtaImageId = $this->imageId($this->fieldValue($pageId, 'tcp_home_mini_cta_image'));

        // Section: Testimonials (desktop/tablet/mobile share same content source)
        $testimonialsEyebrow = $this->field($pageId, 'tcp_home_testimonials_eyebrow', 'CAM NHAN HOC VIEN');
        $testimonialsTitle = $this->field($pageId, 'tcp_home_testimonials_title', 'Hoc vien Talent Connect Plus');
        $testimonialsHighlight = $this->field($pageId, 'tcp_home_testimonials_highlight', 'noi gi?');
        $testimonialsIds = $this->field($pageId, 'tcp_home_testimonials_ids', '');
        $testimonialsPerPage = absint((string) $this->field($pageId, 'tcp_home_testimonials_perpage', '6'));
        if ($testimonialsPerPage <= 0) {
            $testimonialsPerPage = 6;
        }

        // Section: Card Info (desktop/tablet/mobile share same content source)
        $cardInfoEyebrow = $this->field($pageId, 'tcp_home_card_info_eyebrow', 'Ket noi tri thuc');
        $cardInfoTitle = $this->field($pageId, 'tcp_home_card_info_title', 'Kinh nghiem lanh dao tu cac CEOs ky cuu');
        $cardInfoDesc = $this->field($pageId, 'tcp_home_card_info_desc', 'Cap nhat chuong trinh va noi dung moi nhat tu Talent Connect Plus.');
        $cardInfoBtnText = $this->field($pageId, 'tcp_home_card_info_button_text', 'Kham pha ngay');
        $cardInfoBtnUrl = $this->field($pageId, 'tcp_home_card_info_button_url', '#');

        // Section: Logos (desktop/tablet/mobile share same content source)
        $logosTitle = $this->field($pageId, 'tcp_home_logos_title', 'Duoc tin tuong va lua chon tu cac doanh nghiep hang dau');

        // Section: Corporate Training (desktop/tablet/mobile share same content source)
        $corporateEyebrow = $this->field($pageId, 'tcp_home_corporate_eyebrow', 'DOANH NGHIEP');
        $corporateTitle = $this->field($pageId, 'tcp_home_corporate_title', 'Chuong trinh dao tao doanh nghiep');
        $corporateTitleHighlight = $this->field($pageId, 'tcp_home_corporate_title_highlight', 'dành cho doanh nghiệp');
        $corporateDesc = $this->field($pageId, 'tcp_home_corporate_desc', 'Talent Connect Plus phat trien cac chuong trinh dao tao phu hop voi tung doanh nghiep.');
        $corporateBtnText = $this->field($pageId, 'tcp_home_corporate_button_text', 'Tim hieu them');
        $corporateBtnUrl = $this->field($pageId, 'tcp_home_corporate_button_url', '#');
        $corporateLeft = $this->galleryIds($pageId, 'tcp_home_corporate_left_gallery');
        $corporateCenter = $this->galleryIds($pageId, 'tcp_home_corporate_center_gallery');
        $corporateRight = $this->galleryIds($pageId, 'tcp_home_corporate_right_gallery');

        // Section: Contact CTA (shared helper, still dynamic by page fields)
        $contactCta = $this->contactCtaData($pageId);

        $out = '';?>

        <?php get_template_part("inc/Services/Ux/blocks/content", "getenrollment", [
            'herotitle' => $heroTitle, 
            'heroHighlight' => $heroHighlight, 
            'heroCardsTabletDesktop' => $heroCardsTabletDesktop, 
            'heroCardsMobile' => $heroCardsMobile]); 
        ?>
        <?php
        $out .= CoursesSection::render([
            'coursesEyebrow' => $coursesEyebrow,
            'coursesTitle' => $coursesTitle,
            'coursesHighlight' => $coursesHighlight,
            'courseProductIds' => $courseProductIds,
            'coursesBtnText' => $coursesBtnText,
            'coursesBtnUrl' => $coursesBtnUrl,
        ]);
        $out .= MainCtaSection::render([
            'mainCtaTitle' => $mainCtaTitle,
            'mainCtaDesc' => $mainCtaDesc,
            'mainCtaBtn1Text' => $mainCtaBtn1Text,
            'mainCtaBtn1Url' => $mainCtaBtn1Url,
            'mainCtaBtn2Text' => $mainCtaBtn2Text,
            'mainCtaBtn2Url' => $mainCtaBtn2Url,
        ]);
        $out .= TrainersSection::render([
            'trainersEyebrow' => $trainersEyebrow,
            'trainersTitle' => $trainersTitle,
            'trainersHighlight' => $trainersHighlight,
            'trainersBtnText' => $trainersBtnText,
            'trainersBtnUrl' => $trainersBtnUrl,
            'trainersIds' => $trainersIds,
            'stats' => $this->trainerStats($pageId),
        ]);
        // $out .= BooksSection::render([
        //     'booksEyebrow' => $booksEyebrow,
        //     'booksTitle' => $booksTitle,
        //     'booksHighlight' => $booksHighlight,
        //     'bookProductIds' => $bookProductIds,
        //     'booksBtnText' => $booksBtnText,
        //     'booksBtnUrl' => $booksBtnUrl,
        // ]);
        $out .= MiniCtaSection::render([
            'miniCtaTitle' => $miniCtaTitle,
            'miniCtaBtnText' => $miniCtaBtnText,
            'miniCtaBtnUrl' => $miniCtaBtnUrl,
            'miniCtaImageId' => $miniCtaImageId,
        ]);
        $out .= TestimonialsSection::render([
            'testimonialsEyebrow' => $testimonialsEyebrow,
            'testimonialsTitle' => $testimonialsTitle,
            'testimonialsHighlight' => $testimonialsHighlight,
            'testimonialsIds' => $testimonialsIds,
            'testimonialsPerPage' => $testimonialsPerPage,
        ]);
        $out .= CardInfoSection::render([
            'cardInfoEyebrow' => $cardInfoEyebrow,
            'cardInfoTitle' => $cardInfoTitle,
            'cardInfoDesc' => $cardInfoDesc,
            'cardInfoBtnText' => $cardInfoBtnText,
            'cardInfoBtnUrl' => $cardInfoBtnUrl,
            'cards' => $this->cardInfoCards($pageId),
        ]);
        $out .= LogosSection::render([
            'logosTitle' => $logosTitle,
            'logoIds' => $this->logoIds($pageId),
        ]);
        $out .= CorporateTrainingSection::render([
            'corporateEyebrow' => $corporateEyebrow,
            'corporateTitle' => $corporateTitle,
            'corporateTitleHighlight' => $corporateTitleHighlight,
            'corporateDesc' => $corporateDesc,
            'corporateBtnText' => $corporateBtnText,
            'corporateBtnUrl' => $corporateBtnUrl,
            'corporateLeft' => $corporateLeft,
            'corporateCenter' => $corporateCenter,
            'corporateRight' => $corporateRight,
        ]);
        $out .= ContactCtaSection::render([
            'contactImageId' => $contactCta['contactImageId'],
            'contactEyebrow' => $contactCta['contactEyebrow'],
            'contactTitle' => $contactCta['contactTitle'],
            'contactFormShortcode' => $contactCta['contactFormShortcode'],
        ]);

        return do_shortcode($out);
    }

    private function trainerStats(int $postId): array
    {
        $items = [];

        if (function_exists('have_rows') && $postId > 0 && have_rows('tcp_home_trainers_stats', $postId)) {
            while (have_rows('tcp_home_trainers_stats', $postId)) {
                the_row();
                $value = trim((string) get_sub_field('value'));
                $label = trim((string) get_sub_field('label'));
                if ($value === '' && $label === '') {
                    continue;
                }
                $items[] = [
                    'value' => $value,
                    'label' => $label,
                ];
            }
        }

        if (!empty($items)) {
            return $items;
        }

        return [
            [
                'value' => '100%',
                'label' => 'Chuyên gia là những nhà lãnh đạo kỳ cựu',
            ],
            [
                'value' => '100%',
                'label' => 'Nội dung được thiết kế dựa trên kinh nghiệm thực tiễn',
            ],
            [
                'value' => '100%',
                'label' => 'Kiến thức có thể ứng dụng trực tiếp vào công việc',
            ],
        ];
    }

    private function heroCards(int $postId, string $fieldName): array
    {
        $items = [];

        if (function_exists('have_rows') && $postId > 0 && have_rows($fieldName, $postId)) {
            while (have_rows($fieldName, $postId)) {
                the_row();
                $image = $this->imageId(get_sub_field('image'));
                $bgImage = get_sub_field('background_image');
                $title = trim((string) get_sub_field('title'));
                $ctaText = trim((string) get_sub_field('cta_text'));
                $ctaUrl = trim((string) get_sub_field('cta_url'));
                $bgImageUrl = '';

                $bgImageId = $this->imageId($bgImage);
                if ($bgImageId > 0) {
                    $bgImageUrl = (string) wp_get_attachment_image_url($bgImageId, 'original');
                }

                if ($image <= 0 && $title === '') {
                    continue;
                }

                if ($ctaText === '') {
                    $ctaText = 'Tim hieu ngay';
                }

                if ($ctaUrl === '') {
                    $ctaUrl = '#';
                }

                $items[] = [
                    'image'    => $image,
                    'title'    => $title,
                    'cta_text' => $ctaText,
                    'cta_url'  => $ctaUrl,
                    'bg_image_url' => $bgImageUrl,
                ];
            }
        }

        return $items;
    }

    private function defaultHeroCards(string $device = 'desktop'): array
    {
        $desktop = [
            [
                'image'    => 72,
                'title'    => 'HR Analytics Cho Lanh dao nhan su',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
            [
                'image'    => 71,
                'title'    => 'Phong Cach Lam Viec Tinh Thuc',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
            [
                'image'    => 67,
                'title'    => 'Lanh dao bang du lieu',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
        ];

        $mobile = [
            [
                'image'    => 304,
                'title'    => 'HR Analytics Cho Lanh dao nhan su',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
            [
                'image'    => 305,
                'title'    => 'Phong Cach Lam Viec Tinh Thuc',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
            [
                'image'    => 306,
                'title'    => 'Lanh dao bang du lieu',
                'cta_text' => 'Tim hieu ngay',
                'cta_url'  => '#',
                'bg_image_url' => '',
            ],
        ];

        return $device === 'mobile' ? $mobile : $desktop;
    }

    private function cardInfoCards(int $postId): array
    {
        $items = [];

        if (function_exists('have_rows') && $postId > 0 && have_rows('tcp_home_card_info_cards', $postId)) {
            while (have_rows('tcp_home_card_info_cards', $postId)) {
                the_row();
                $image = $this->imageId(get_sub_field('image'));
                $title = trim((string) get_sub_field('title'));
                $desc = trim((string) get_sub_field('desc'));
                $meta = trim((string) get_sub_field('meta'));
                $url = trim((string) get_sub_field('url'));

                if ($image <= 0 && $title === '' && $desc === '' && $meta === '') {
                    continue;
                }

                $items[] = [
                    'image' => $image,
                    'title' => $title,
                    'desc'  => $desc,
                    'meta'  => $meta,
                    'url'   => $url,
                ];
            }
        }

        return $items;
    }

    private function logoIds(int $postId): array
    {
        $items = [];

        if (function_exists('have_rows') && $postId > 0 && have_rows('tcp_home_logos_items', $postId)) {
            while (have_rows('tcp_home_logos_items', $postId)) {
                the_row();
                $id = $this->imageId(get_sub_field('image'));
                if ($id > 0) {
                    $items[] = $id;
                }
            }
        }

        return $items;
    }
}
