<?php

namespace TCP\Theme\Services\Forms;

use TCP\Theme\Core\Singleton;

defined('ABSPATH') || exit;

final class ContactCdpForm
{
    use Singleton;

    private const FORM_ID = 193;
    private const COURSES_CACHE_KEY = 'tcp_cdp_form_courses_v3';
    private const COURSES_CACHE_TTL = 5 * MINUTE_IN_SECONDS;
    private const HED_ACCOUNT_NAME = 'Talent Connect Plus';
    private const GROUP_ACCOUNTS  = 'TCP';

    protected function init(): void
    {
        add_action('acf/init', [$this, 'register_acf_fields']);
        add_filter('wpcf7_form_elements', [$this, 'inject_fields']);
        add_filter('wpcf7_posted_data', [$this, 'merge_posted_data']);
        add_action('wpcf7_before_send_mail', [$this, 'before_send_mail'], 10, 3);
        add_action('wpcf7_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_head', [$this, 'output_utm_capture'], 1);

        add_action('save_post_product', [$this, 'flush_courses_cache']);
        add_action('deleted_post', [$this, 'flush_courses_cache']);
    }

    public function output_utm_capture(): void
    {
        echo "<script>(function(){try{var p=new URLSearchParams(location.search),k=['utm_source','utm_medium','utm_campaign','utm_term','utm_content'],d={},f=false;k.forEach(function(x){var v=p.get(x);if(v){d[x]=v;f=true;}});if(!f)return;document.cookie='_tcp_utm='+encodeURIComponent(JSON.stringify(d))+'; path=/; max-age=2592000; SameSite=Lax';}catch(e){}})();</script>\n";
    }

    public function flush_courses_cache(): void
    {
        delete_transient(self::COURSES_CACHE_KEY);
    }

    public function register_acf_fields(): void
    {
        if (!function_exists('acf_add_local_field_group')) return;

        acf_add_local_field_group([
            'key'    => 'group_tcp_course_cdp',
            'title'  => 'Thông tin CDP & Lịch khai giảng',
            'fields' => [
                [
                    'key'          => 'field_tcp_course_cdp_id',
                    'name'         => 'tcp_course_cdp_id',
                    'label'        => 'CDP Course ID',
                    'type'         => 'text',
                    'instructions' => 'Mã khóa học gửi về CDP (vd: S006). Để trống nếu không muốn khóa này xuất hiện trong form liên hệ.',
                ],
                [
                    'key'          => 'field_tcp_course_cdp_lead_event',
                    'name'         => 'tcp_course_cdp_lead_event',
                    'label'        => 'CDP Lead Event',
                    'type'         => 'text',
                    'instructions' => 'Tên event gửi về CDP (vd: CEO Masterclass).',
                ],
                [
                    'key'          => 'field_tcp_course_cdp_event_name',
                    'name'         => 'tcp_course_cdp_event_name',
                    'label'        => 'Event name (dataLayer)',
                    'type'         => 'text',
                    'instructions' => 'Tên event push vào dataLayer khi submit form cho khóa này. Vd: submit_form_lb, submit_form_ceo_2026. Để trống thì mặc định là submit_form.',
                ],
                [
                    'key'          => 'field_tcp_course_cdp_reference',
                    'name'         => 'tcp_course_cdp_reference',
                    'label'        => 'CDP Reference URL',
                    'type'         => 'url',
                    'instructions' => 'URL landing page kèm UTM (nếu để trống sẽ dùng permalink của khóa học).',
                ],
                [
                    'key'          => 'field_tcp_course_schedules',
                    'name'         => 'tcp_course_schedules',
                    'label'        => 'Lịch khai giảng',
                    'type'         => 'repeater',
                    'instructions' => 'Mỗi dòng là một thời gian khai giảng. Người dùng có thể chọn nhiều dòng khi đăng ký.',
                    'layout'       => 'table',
                    'button_label' => 'Thêm lịch khai giảng',
                    'sub_fields'   => [
                        [
                            'key'         => 'field_tcp_schedule_label',
                            'name'        => 'tcp_schedule_label',
                            'label'       => 'Thời gian',
                            'type'        => 'text',
                            'required'    => 1,
                            'instructions' => 'Vd: 18/05/2026 hoặc "Khóa K23 - 18/05/2026". Nội dung này sẽ hiển thị trực tiếp trên form.',
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'product',
                    ],
                ],
            ],
            'menu_order' => 20,
            'position'   => 'normal',
            'style'      => 'default',
        ]);
    }

    public function inject_fields(string $html): string
    {
        if (!function_exists('wpcf7_get_current_contact_form')) return $html;

        $form = wpcf7_get_current_contact_form();
        if (!$form || (int) $form->id() !== self::FORM_ID) return $html;

        $courses = $this->get_courses();
        if (empty($courses)) return $html;

        $marker = '<div class="c-form__actions">';
        if (strpos($html, $marker) === false) return $html;

        $extras = $this->render_extras($courses);

        return str_replace($marker, $extras . $marker, $html);
    }

    private function render_extras(array $courses): string
    {
        ob_start();
        ?>
        <input type="hidden" name="page_url" class="tcp-cdp-page-url" value="">

        <div class="c-form__field c-form__field--course">
            <label class="c-form__label" for="tcp-cdp-course">Khóa học<span class="c-form__required">*</span></label>
            <div class="c-form__control">
                <select id="tcp-cdp-course" class="c-form__select tcp-cdp-course" name="course" required aria-required="true">
                    <option value="">Chọn khóa học</option>
                    <?php foreach ($courses as $c): ?>
                        <option
                            value="<?php echo esc_attr((string) $c['id']); ?>"
                            data-course-id="<?php echo esc_attr($c['course_id']); ?>"
                            data-lead-event="<?php echo esc_attr($c['lead_event']); ?>"
                            data-event-name="<?php echo esc_attr($c['event_name']); ?>"
                            data-reference="<?php echo esc_attr($c['reference']); ?>"
                        ><?php echo esc_html($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="c-form__field c-form__field--schedules tcp-cdp-schedules" data-empty="true">
            <label class="c-form__label">Chọn thời gian<span class="c-form__required">*</span></label>
            <p class="c-form__hint tcp-cdp-schedules__hint">Vui lòng chọn khóa học để xem các thời gian.</p>

            <div class="c-form__checks tcp-cdp-schedules__groups">
                <?php foreach ($courses as $c): ?>
                    <?php if (empty($c['schedules'])) continue; ?>
                    <div class="tcp-cdp-schedules__group" data-course-pid="<?php echo esc_attr((string) $c['id']); ?>" hidden>
                        <?php foreach ($c['schedules'] as $s): ?>
                            <label class="c-form__check">
                                <input
                                    type="radio"
                                    class="tcp-cdp-schedule"
                                    name="schedule"
                                    value="<?php echo esc_attr($s['key']); ?>"
                                    data-schedule-label="<?php echo esc_attr($s['label']); ?>"
                                    disabled
                                >
                                <span class="c-form__check-mark" aria-hidden="true"></span>
                                <span class="c-form__check-text"><?php echo esc_html($s['label']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function merge_posted_data(array $data): array
    {
        if (function_exists('wpcf7_get_current_contact_form')) {
            $form = wpcf7_get_current_contact_form();
            if (!$form || (int) $form->id() !== self::FORM_ID) return $data;
        }

        if (!isset($data['course']) && isset($_POST['course'])) {
            $data['course'] = sanitize_text_field((string) wp_unslash($_POST['course']));
        }
        if (!isset($data['schedule']) && isset($_POST['schedule'])) {
            $data['schedule'] = sanitize_text_field((string) wp_unslash($_POST['schedule']));
        }
        if (!isset($data['page_url']) && isset($_POST['page_url'])) {
            $data['page_url'] = esc_url_raw((string) wp_unslash($_POST['page_url']));
        }
        return $data;
    }

    public function before_send_mail($contact_form, &$abort, $submission): void
    {
        if (!$contact_form || (int) $contact_form->id() !== self::FORM_ID) return;
        if (!$submission) return;

        // Nếu chưa có khóa học nào khai báo CDP thì giữ form như cũ, không bắt buộc.
        if (empty($this->get_courses())) return;

        $posted = (array) $submission->get_posted_data();

        $course_pid   = isset($posted['course']) ? (int) $posted['course'] : 0;
        $schedule_key = isset($posted['schedule']) ? sanitize_text_field((string) $posted['schedule']) : '';

        if ($course_pid <= 0 || $schedule_key === '') {
            $abort = true;
            $submission->set_response('Vui lòng chọn khóa học và thời gian.');
            return;
        }

        $course = $this->get_course_by_id($course_pid);
        if (!$course) {
            $abort = true;
            $submission->set_response('Khóa học không hợp lệ. Vui lòng tải lại trang và thử lại.');
            return;
        }

        $schedule = $this->find_schedule($course['schedules'], $schedule_key);
        if (!$schedule) {
            $abort = true;
            $submission->set_response('Thời gian được chọn không hợp lệ.');
            return;
        }

        $payload = $this->build_payload($course, $schedule, $posted);
        $this->send_webhook($payload);
    }

    private function build_payload(array $course, array $schedule, array $posted): array
    {
        $reference = $this->resolve_reference_with_utm($course, $posted);

        return [
            'timestamp'            => (string) (int) round(microtime(true) * 1000),
            'cookie_id'            => $this->resolve_cookie_id(),
            'type'                 => 'lead',
            'course_name'          => $course['name'],
            'course_id'            => $course['course_id'],
            'lead_event'           => $course['lead_event'],
            'reference'            => $reference,
            'hed_account_name'     => self::HED_ACCOUNT_NAME,
            'group_accounts'       => self::GROUP_ACCOUNTS,
            'full_name'            => $this->sanitize_text($posted['fullname'] ?? ''),
            'email'                => sanitize_email((string) ($posted['email'] ?? '')),
            'phone_mobile'         => $this->sanitize_text($posted['phone'] ?? ''),
            'work_experience'      => $this->sanitize_text($posted['experience'] ?? ''),
            'current_career_stage' => $this->sanitize_text($posted['field'] ?? ''),
            'current_career_note'  => '',
            'company_name'         => '',
            'schedule'             => $schedule['label'],
        ];
    }

    private function send_webhook(array $payload): void
    {
        $url = defined('TCP_CDP_WEBHOOK_URL') ? (string) constant('TCP_CDP_WEBHOOK_URL') : '';
        if ($url === '') {
            error_log('[TCP CDP Webhook] TCP_CDP_WEBHOOK_URL chưa được khai báo — payload: ' . wp_json_encode($payload));
            return;
        }

        $response = wp_remote_post($url, [
            'headers'  => ['Content-Type' => 'application/json'],
            'body'     => (string) wp_json_encode($payload),
            'timeout'  => 5,
            'blocking' => true,
        ]);

        if (is_wp_error($response)) {
            error_log('[TCP CDP Webhook] ' . $response->get_error_message());
            return;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            error_log('[TCP CDP Webhook] HTTP ' . $code . ' — ' . wp_remote_retrieve_body($response));
        }
    }

    public function enqueue_assets(): void
    {
        $config_js = 'window.TCP_CDP_DATALAYER = ' . wp_json_encode($this->get_datalayer_config()) . ';';

        // Hook wpcf7_enqueue_scripts đã đảm bảo CF7 có trên trang hiện tại.
        if (wp_script_is('contact-form-7', 'enqueued') || wp_script_is('contact-form-7', 'registered')) {
            wp_add_inline_script('contact-form-7', $config_js, 'before');
            wp_add_inline_script('contact-form-7', $this->get_inline_js());
            return;
        }

        wp_register_script('tcp-contact-cdp-form', '', ['jquery'], null, true);
        wp_enqueue_script('tcp-contact-cdp-form');
        wp_add_inline_script('tcp-contact-cdp-form', $config_js, 'before');
        wp_add_inline_script('tcp-contact-cdp-form', $this->get_inline_js());
    }

    private function get_datalayer_config(): array
    {
        $defaults = [
            self::FORM_ID => 'submit_form',
        ];
        $event_names = apply_filters('tcp_cdp_form_event_names', $defaults);
        $normalized = [];
        if (is_array($event_names)) {
            foreach ($event_names as $form_id => $name) {
                $normalized[(string) (int) $form_id] = (string) $name;
            }
        }

        $page_context = null;
        if (function_exists('is_product') && is_product()) {
            $pid = (int) get_queried_object_id();
            if ($pid > 0) {
                $event_name = trim((string) get_post_meta($pid, 'tcp_course_cdp_event_name', true));
                if ($event_name !== '') {
                    $page_context = ['eventName' => $event_name];
                }
            }
        }

        return [
            'eventNames'       => $normalized,
            'defaultEventName' => 'submit_form',
            'emailField'       => 'email',
            'phoneField'       => 'phone',
            'pageContext'      => $page_context,
            'formId'           => self::FORM_ID,
            'redirectUrl'      => home_url('/xac-nhan-dang-ky/'),
        ];
    }

    private function get_inline_js(): string
    {
        return <<<'JS'
(function () {
    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); return; }
        document.addEventListener('DOMContentLoaded', fn);
    }

    function init(root) {
        var select = root.querySelector('.tcp-cdp-course');
        var schedulesWrap = root.querySelector('.tcp-cdp-schedules');
        if (!select || !schedulesWrap) return;

        var groups = schedulesWrap.querySelectorAll('.tcp-cdp-schedules__group');
        var hint = schedulesWrap.querySelector('.tcp-cdp-schedules__hint');

        function setActive(pid) {
            var hasActive = false;
            groups.forEach(function (g) {
                var match = g.getAttribute('data-course-pid') === pid && pid !== '';
                g.hidden = !match;
                g.querySelectorAll('input.tcp-cdp-schedule').forEach(function (input) {
                    input.disabled = !match;
                    if (!match) input.checked = false;
                });
                if (match) hasActive = true;
            });
            schedulesWrap.setAttribute('data-empty', hasActive ? 'false' : 'true');
            if (hint) hint.style.display = hasActive ? 'none' : '';
        }

        select.addEventListener('change', function () {
            setActive(select.value);
        });

        setActive(select.value);
    }

    function generateEventId() {
        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
            return window.crypto.randomUUID();
        }
        return 'evt_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);
    }

    function getInputValue(inputs, name) {
        if (!inputs || !name) return '';
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i] && inputs[i].name === name) {
                return inputs[i].value == null ? '' : String(inputs[i].value);
            }
        }
        return '';
    }

    function resolveEventName(form, detail) {
        var cfg = window.TCP_CDP_DATALAYER || {};
        var defaultName = cfg.defaultEventName || 'submit_form';
        var inputs = (detail && detail.inputs) || [];

        // 1) Map cố định theo formId (form-level config thắng course-level).
        var formId = detail && detail.contactFormId ? String(detail.contactFormId) : '';
        if (formId && cfg.eventNames && cfg.eventNames[formId]) {
            return cfg.eventNames[formId];
        }

        // 2) Form có select khóa học → đọc data-event-name của option đang chọn.
        if (form && form.querySelector) {
            var selectedCourse = getInputValue(inputs, 'course');
            if (selectedCourse) {
                var option = form.querySelector('.tcp-cdp-course option[value="' + selectedCourse + '"]');
                if (option) {
                    var name = (option.getAttribute('data-event-name') || '').trim();
                    if (name) return name;
                }
            }
        }

        // 3) Page có context khóa học (single product page).
        if (cfg.pageContext && cfg.pageContext.eventName) {
            var ctxName = String(cfg.pageContext.eventName).trim();
            if (ctxName) return ctxName;
        }

        return defaultName;
    }

    function pushDataLayer(e) {
        var form = e.target;
        var detail = e.detail || {};
        var cfg = window.TCP_CDP_DATALAYER || {};
        var inputs = detail.inputs || [];

        var eventName = resolveEventName(form, detail);
        var email = getInputValue(inputs, cfg.emailField || 'email');
        var phone = getInputValue(inputs, cfg.phoneField || 'phone');

        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            event: eventName,
            event_id: generateEventId(),
            email: email,
            phone: phone
        });
    }

    function setPageUrl(form) {
        var input = form.querySelector('input.tcp-cdp-page-url');
        if (input) input.value = window.location.href;
    }

    ready(function () {
        document.querySelectorAll('form.wpcf7-form').forEach(function (form) {
            if (form.querySelector('.tcp-cdp-course')) {
                init(form);
                setPageUrl(form);
            }
        });

        document.addEventListener('wpcf7mailsent', function (e) {
            pushDataLayer(e);

            var cfg = window.TCP_CDP_DATALAYER || {};
            var detail = e.detail || {};
            var form = e.target;

            if (cfg.formId && String(detail.contactFormId) === String(cfg.formId) && cfg.redirectUrl) {
                if (form && form.querySelector) {
                    var resp = form.querySelector('.wpcf7-response-output');
                    if (resp) resp.style.display = 'none';
                }
                window.location.href = cfg.redirectUrl;
                return;
            }

            if (!form || !form.querySelector) return;
            var select = form.querySelector('.tcp-cdp-course');
            if (!select) return;
            select.value = '';
            select.dispatchEvent(new Event('change'));
        });
    });
})();
JS;
    }

    private function get_courses(): array
    {
        $cached = get_transient(self::COURSES_CACHE_KEY);
        if (is_array($cached)) return $cached;

        $q = new \WP_Query([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'tcp_course_cdp_id',
                    'compare' => 'EXISTS',
                ],
                [
                    'key'     => 'tcp_course_cdp_id',
                    'value'   => '',
                    'compare' => '!=',
                ],
            ],
        ]);

        $courses = [];
        foreach ($q->posts as $pid) {
            $pid = (int) $pid;
            $course_id  = trim((string) get_post_meta($pid, 'tcp_course_cdp_id', true));
            if ($course_id === '') continue;

            $schedules = $this->read_schedules_meta($pid);

            $courses[] = [
                'id'         => $pid,
                'name'       => (string) get_the_title($pid),
                'course_id'  => $course_id,
                'lead_event' => trim((string) get_post_meta($pid, 'tcp_course_cdp_lead_event', true)),
                'event_name' => trim((string) get_post_meta($pid, 'tcp_course_cdp_event_name', true)),
                'reference'  => $this->resolve_reference($pid),
                'schedules'  => $schedules,
            ];
        }

        set_transient(self::COURSES_CACHE_KEY, $courses, self::COURSES_CACHE_TTL);
        return $courses;
    }

    private function get_course_by_id(int $product_id): ?array
    {
        foreach ($this->get_courses() as $c) {
            if ((int) $c['id'] === $product_id) return $c;
        }
        return null;
    }

    private function read_schedules_meta(int $pid): array
    {
        $rows = function_exists('get_field') ? get_field('tcp_course_schedules', $pid) : null;
        if (!is_array($rows) || empty($rows)) return [];

        $schedules = [];
        foreach ($rows as $idx => $row) {
            $label = isset($row['tcp_schedule_label']) ? trim((string) $row['tcp_schedule_label']) : '';
            if ($label === '') continue;

            $schedules[] = [
                'label' => $label,
                'key'   => $pid . ':row-' . ($idx + 1),
            ];
        }
        return $schedules;
    }

    private function resolve_reference(int $pid): string
    {
        $url = trim((string) get_post_meta($pid, 'tcp_course_cdp_reference', true));
        if ($url !== '') return $url;
        return (string) get_permalink($pid);
    }

    private function resolve_reference_with_utm(array $course, array $posted): string
    {
        $utm = $this->collect_utm($posted);
        $fallback = $course['reference'] !== '' ? $course['reference'] : (string) get_permalink($course['id']);

        if (empty($utm)) return $fallback;

        $base = $this->extract_base_url(isset($posted['page_url']) ? (string) $posted['page_url'] : '');
        if ($base === '') $base = $fallback;

        return add_query_arg($utm, $base);
    }

    private function collect_utm(array $posted): array
    {
        $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        $utm  = [];

        if (!empty($_COOKIE['_tcp_utm'])) {
            $raw  = wp_unslash((string) $_COOKIE['_tcp_utm']);
            $data = json_decode($raw, true);
            if (is_array($data)) {
                foreach ($keys as $k) {
                    if (!empty($data[$k])) {
                        $utm[$k] = sanitize_text_field((string) $data[$k]);
                    }
                }
            }
        }

        if (!empty($utm)) return $utm;

        $page_url = isset($posted['page_url']) ? (string) $posted['page_url'] : '';
        if ($page_url === '') return [];

        $parsed = wp_parse_url($page_url);
        if (!is_array($parsed) || empty($parsed['query'])) return [];

        $site_host = (string) wp_parse_url(home_url(), PHP_URL_HOST);
        if (empty($parsed['host']) || strcasecmp($parsed['host'], $site_host) !== 0) return [];

        parse_str((string) $parsed['query'], $query);
        foreach ($keys as $k) {
            if (!empty($query[$k])) {
                $utm[$k] = sanitize_text_field((string) $query[$k]);
            }
        }
        return $utm;
    }

    private function extract_base_url(string $url): string
    {
        if ($url === '') return '';

        $parsed = wp_parse_url($url);
        if (!is_array($parsed) || empty($parsed['scheme']) || empty($parsed['host'])) return '';

        $site_host = (string) wp_parse_url(home_url(), PHP_URL_HOST);
        if (strcasecmp($parsed['host'], $site_host) !== 0) return '';

        return $parsed['scheme'] . '://' . $parsed['host'] . ($parsed['path'] ?? '/');
    }

    private function find_schedule(array $available, string $key): ?array
    {
        foreach ($available as $s) {
            if ($s['key'] === $key) return $s;
        }
        return null;
    }

    private function sanitize_text($value): string
    {
        if (is_array($value)) {
            $parts = array_map(static fn($v) => sanitize_text_field((string) $v), $value);
            $parts = array_filter($parts, static fn($v) => $v !== '');
            return implode(', ', $parts);
        }
        return sanitize_text_field((string) $value);
    }

    private function resolve_cookie_id(): string
    {
        if (!empty($_COOKIE['_tcp_cdp_uid'])) {
            return sanitize_text_field((string) wp_unslash($_COOKIE['_tcp_cdp_uid']));
        }
        if (!empty($_COOKIE['_ga'])) {
            $ga = sanitize_text_field((string) wp_unslash($_COOKIE['_ga']));
            if (preg_match('/GA\d\.\d\.(\d+\.\d+)/', $ga, $m)) {
                return $m[1];
            }
            return $ga;
        }
        return function_exists('wp_generate_uuid4') ? wp_generate_uuid4() : (string) round(microtime(true) * 1000);
    }
}
