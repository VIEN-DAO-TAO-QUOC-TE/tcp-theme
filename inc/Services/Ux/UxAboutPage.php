<?php

namespace TCP\Theme\Services\Ux;

use TCP\Theme\Core\Singleton;
use TCP\Theme\Services\Ux\Sections\About\AboutBreadcrumbSection;
use TCP\Theme\Services\Ux\Sections\About\AboutHeroSection;
use TCP\Theme\Services\Ux\Sections\About\AboutStylesSection;
use TCP\Theme\Services\Ux\Sections\About\AboutTeamSection;
use TCP\Theme\Services\Ux\Sections\About\AboutValuesSection;
use TCP\Theme\Services\Ux\Sections\ContactCtaSection;

defined('ABSPATH') || exit;

final class UxAboutPage
{
    use Singleton;
    use AcfFieldsTrait;

    protected function init(): void
    {
        add_action('ux_builder_setup', [$this, 'registerUx'], 999);
        add_shortcode('tcp_about_page', [$this, 'renderShortcode']);
    }

    public function registerUx(): void
    {
        if (!function_exists('add_ux_builder_shortcode')) {
            return;
        }

        add_ux_builder_shortcode('tcp_about_page', [
            'name' => 'TCP About Page',
            'category' => 'TCP',
            'info' => 'ACF-driven about page sections',
            'wrap' => false,
            'options' => [
                'page_id' => [
                    'type' => 'textfield',
                    'heading' => 'Page ID (optional)',
                    'description' => 'Leave empty to use current page.',
                    'default' => '',
                ],
                'team_trainer_ids' => [
                    'type' => 'select',
                    'heading' => 'Team trainers (optional)',
                    'param_name' => 'team_trainer_ids',
                    'config' => [
                        'multiple' => true,
                        'placeholder' => 'Select trainers..',
                        'postSelect' => [
                            'post_type' => 'tcp_trainer',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function renderShortcode($atts, $content = null): string
    {
        $atts = shortcode_atts([
            'page_id' => '',
            'team_trainer_ids' => '',
        ], (array) $atts, 'tcp_about_page');

        $pageId = absint((string) $atts['page_id']);
        if ($pageId <= 0) {
            $pageId = get_queried_object_id();
        }
        if ($pageId <= 0) {
            $pageId = absint((string) get_the_ID());
        }
        if ($pageId <= 0) {
            // About ACF group in this project is currently attached to page ID 653.
            $pageId = 653;
        }

        $heroBgUrl = $this->field($pageId, 'hero_bg', '');
        $heroTitle = $this->field($pageId, 'hero_title', 'Noi toi luyen lanh dao va tiep noi nhung di san');
        $heroTitleHighlight = $this->field($pageId, 'hero_title_highlight', '');
        $heroDesc = $this->field($pageId, 'hero_desc', 'Chung toi tin vao suc manh cua giao duc thuc tien, giup hoc vien phat trien su nghiep va cuoc song.');
        $values = $this->valuesData($pageId);

        $giveItems = $this->giveItems($pageId);
        $teamTrainersIds = $atts['team_trainer_ids'];
        if ($teamTrainersIds === '' || $teamTrainersIds === null) {
            $teamTrainersIds = $this->fieldValue($pageId, 'team_trainer_ids');
        }

        // Section: Contact CTA (shared helper, still dynamic by page fields)
        $contactCta = $this->contactCtaData($pageId, [], [
            'image' => ['tcp_about_contact_image', 'tcp_home_contact_image'],
            'eyebrow' => ['tcp_about_contact_eyebrow', 'tcp_home_contact_eyebrow'],
            'title' => ['tcp_about_contact_title', 'tcp_home_contact_title'],
            'form' => ['tcp_about_contact_form_shortcode', 'tcp_home_contact_form_shortcode'],
        ]);

        $out = '<div id="custom-about-page" class="about-wrapper">';
        $out .= '<style>' . AboutStylesSection::render() . '</style>';
        $out .= AboutBreadcrumbSection::render();
        $out .= AboutHeroSection::render([
            'heroBgUrl' => $heroBgUrl,
            'heroTitle' => $heroTitle,
            'heroTitleHighlight' => $heroTitleHighlight,
            'heroDesc' => $heroDesc,
        ]);
        $out .= AboutValuesSection::render([
            'values' => $values,
            'giveItems' => $giveItems,
        ]);
        $out .= AboutTeamSection::render([
            'trainersIds' => $teamTrainersIds,
        ]);

        $out .= ContactCtaSection::render([
            'contactImageId' => $contactCta['contactImageId'],
            'contactEyebrow' => $contactCta['contactEyebrow'],
            'contactTitle' => $contactCta['contactTitle'],
            'contactFormShortcode' => $contactCta['contactFormShortcode'],
        ]);
        $out .= '</div>';

        return do_shortcode($out);
    }
    
    private function valuesData(int $postId): array
    {
        return [
            'label' => $this->field($postId, 'values_label', 'GIÁ TRỊ'),
            'intro' => $this->field($postId, 'values_intro', 'Chúng tôi tin vào sức mạnh của'),
            'highlight' => $this->field($postId, 'values_highlight', 'giáo dục thực tiễn'),
            'outro' => $this->field($postId, 'values_outro', ', giup hoc vien phat trien su nghiep va cuoc song.'),
            'left_title' => $this->field($postId, 'values_left_title', 'Tam nhin'),
            'left_icon' => $this->field($postId, 'values_left_icon', '<span>◎</span>'),
            'left_text' => $this->field($postId, 'values_left_text', 'Xay dung mot he sinh thai giao duc ung dung, noi ma tri thuc thuc tien va kinh nghiem lanh dao khong chi la di san duoc trao truyen ma con la nen tang vung chac de phat trien the he tai nang va doanh nghiep tai Viet Nam.'),
            'right_title' => $this->field($postId, 'values_right_title', 'Su menh'),
            'right_icon' => $this->field($postId, 'values_right_icon', '<span>⚑</span>'),
            'right_text' => $this->field($postId, 'values_right_text', 'Kien tao nen tang giao duc ung dung ket hop dao tao, co van va tu van, nham giup nguoi hoc va doanh nghiep xac dinh ro nang luc, hanh dong co chien luoc va chinh phuc hanh trinh vuon minh ra the gioi.'),
        ];
    }


    private function giveItems(int $postId): array
    {
        foreach ($this->candidatePostIds($postId) as $candidateId) {
            $rows = $this->fieldValue($candidateId, 'give_list');
            $items = $this->parseGiveRows($rows);
            if (!empty($items)) {
                return $items;
            }

            // Fallback: when ACF cannot resolve repeater schema, get_field() may return row count as string.
            if (is_string($rows) && ctype_digit(trim($rows))) {
                $items = $this->loadGiveItemsFromPostMeta($candidateId, (int) trim($rows));
                if (!empty($items)) {
                    return $items;
                }
            }

            if (function_exists('have_rows') && have_rows('give_list', $candidateId)) {
                $items = [];
                while (have_rows('give_list', $candidateId)) {
                    the_row();
                    $char = trim((string) get_sub_field('char'));
                    $label = trim((string) get_sub_field('label'));
                    $text = trim((string) get_sub_field('text'));

                    if ($char === '' && $label === '' && $text === '') {
                        continue;
                    }

                    $items[] = [
                        'char' => $char,
                        'label' => $label,
                        'text' => $text,
                    ];
                }

                if (!empty($items)) {
                    return $items;
                }
            }
        }

        return [
            [
                'char' => 'G',
                'label' => 'Growth',
                'text' => 'Su tang truong ben vung.',
            ],
            [
                'char' => 'I',
                'label' => 'Integrity',
                'text' => 'Chinh truc trong moi hanh dong.',
            ],
            [
                'char' => 'V',
                'label' => 'Value',
                'text' => 'Tao ra gia tri thuc cho cong dong.',
            ],
            [
                'char' => 'E',
                'label' => 'Empowerment',
                'text' => 'Trao quyen de but pha.',
            ],
        ];
    }

    private function candidatePostIds(int $postId): array
    {
        $ids = [];

        if ($postId > 0) {
            $ids[] = $postId;
        }

        $queriedId = absint((string) get_queried_object_id());
        if ($queriedId > 0) {
            $ids[] = $queriedId;
        }

        $currentId = absint((string) get_the_ID());
        if ($currentId > 0) {
            $ids[] = $currentId;
        }

        global $post;
        if (isset($post->ID)) {
            $globalId = absint((string) $post->ID);
            if ($globalId > 0) {
                $ids[] = $globalId;
            }
        }

        $ids[] = 653;

        return array_values(array_unique(array_filter($ids)));
    }

    private function parseGiveRows($rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            if (is_object($row)) {
                $row = (array) $row;
            }
            if (!is_array($row)) {
                continue;
            }

            $char = trim((string) ($row['char'] ?? ''));
            $label = trim((string) ($row['label'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));

            if ($char === '' && $label === '' && $text === '') {
                continue;
            }

            $items[] = [
                'char' => $char,
                'label' => $label,
                'text' => $text,
            ];
        }

        return $items;
    }

    private function loadGiveItemsFromPostMeta(int $postId, int $rowCount): array
    {
        if ($postId <= 0 || $rowCount <= 0) {
            return [];
        }

        $items = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $char = trim((string) get_post_meta($postId, 'give_list_' . $i . '_char', true));
            $label = trim((string) get_post_meta($postId, 'give_list_' . $i . '_label', true));
            $text = trim((string) get_post_meta($postId, 'give_list_' . $i . '_text', true));

            if ($char === '' && $label === '' && $text === '') {
                continue;
            }

            $items[] = [
                'char' => $char,
                'label' => $label,
                'text' => $text,
            ];
        }

        return $items;
    }
}
