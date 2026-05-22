<?php

namespace TCP\Theme\Services\Ux;

defined('ABSPATH') || exit;

/**
 * Shared ACF field helpers for UX pages
 */
trait AcfFieldsTrait
{
    protected function field(int $postId, string $key, string $default = ''): string
    {
        if (!function_exists('get_field')) {
            return $default;
        }

        $value = $postId > 0 ? get_field($key, $postId) : get_field($key);
        if (!is_string($value)) {
            return $default;
        }

        $value = trim($value);
        return $value === '' ? $default : $value;
    }

    protected function fieldValue(int $postId, string $key)
    {
        if (!function_exists('get_field')) {
            return null;
        }

        return $postId > 0 ? get_field($key, $postId) : get_field($key);
    }

    protected function imageId($value): int
    {
        if (is_numeric($value)) {
            return absint($value);
        }

        if (is_array($value) && !empty($value['ID'])) {
            return absint($value['ID']);
        }

        return 0;
    }

    protected function galleryIds(int $postId, string $key): array
    {
        $raw = $this->fieldValue($postId, $key);
        if (!is_array($raw)) {
            return [];
        }

        $ids = array_map('absint', $raw);
        $ids = array_values(array_filter($ids));

        return $ids;
    }

    protected function firstField(int $postId, array $keys, string $default = ''): string
    {
        if (!function_exists('get_field')) {
            return $default;
        }

        foreach ($keys as $key) {
            $value = $postId > 0 ? get_field((string) $key, $postId) : get_field((string) $key);
            if (!is_string($value)) {
                continue;
            }

            $value = trim($value);
            if ($value !== '') {
                return $value;
            }
        }

        return $default;
    }

    protected function firstFieldValue(int $postId, array $keys)
    {
        if (!function_exists('get_field')) {
            return null;
        }

        foreach ($keys as $key) {
            $value = $postId > 0 ? get_field((string) $key, $postId) : get_field((string) $key);
            if (is_string($value)) {
                $value = trim($value);
                if ($value !== '') {
                    return $value;
                }
                continue;
            }

            if (is_array($value) && !empty($value['ID'])) {
                return $value;
            }

            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    protected function contactCtaData(int $postId, array $defaults = [], array $fieldKeys = []): array
    {
        $defaults = array_merge([
            'contactImageId' => 0,
            'contactEyebrow' => 'LIEN HE',
            'contactTitle' => "Ket noi cung\nTalent Connect Plus",
            'contactFormShortcode' => '[contact-form-7 id="193"]',
        ], $defaults);

        $fieldKeys = array_merge([
            'image' => ['tcp_home_contact_image'],
            'eyebrow' => ['tcp_home_contact_eyebrow'],
            'title' => ['tcp_home_contact_title'],
            'form' => ['tcp_home_contact_form_shortcode'],
        ], $fieldKeys);

        $imageValue = $this->firstFieldValue($postId, (array) ($fieldKeys['image'] ?? []));

        return [
            'contactImageId' => $imageValue !== null ? $this->imageId($imageValue) : (int) $defaults['contactImageId'],
            'contactEyebrow' => $this->firstField($postId, (array) ($fieldKeys['eyebrow'] ?? []), (string) $defaults['contactEyebrow']),
            'contactTitle' => $this->firstField($postId, (array) ($fieldKeys['title'] ?? []), (string) $defaults['contactTitle']),
            'contactFormShortcode' => $this->firstField($postId, (array) ($fieldKeys['form'] ?? []), (string) $defaults['contactFormShortcode']),
        ];
    }
}
