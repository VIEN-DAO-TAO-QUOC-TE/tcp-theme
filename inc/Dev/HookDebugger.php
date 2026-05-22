<?php

namespace TCP\Theme\Dev;

defined('ABSPATH') || exit;

/**
 * HookDebugger
 *
 * Dùng để debug các callbacks đang "join" vào 1 hook bất kỳ.
 *
 * - Render overlay ở frontend (wp_footer) cho admin
 * - Hỗ trợ filter theo page (is_cart, is_checkout, v.v.)
 * - Hỗ trợ debug nhiều hook cùng lúc
 * - Có Reflection để show file:line (nếu lấy được)
 *
 * Usage ví dụ:
 *
 * HookDebugger::boot([
 *   'enabled'   => true,
 *   'hooks'     => ['woocommerce_cart_collaterals', 'woocommerce_after_cart_table'],
 *   'only_admin'=> true,
 *   'when'      => function () { return is_cart(); },
 *   'position'  => 'br', // br|bl|tr|tl
 * ]);
 */
final class HookDebugger
{
    private static bool $booted = false;

    /** @var array<string, mixed> */
    private static array $cfg = [
        'enabled'      => false,
        'hooks'        => [],
        'only_admin'   => true,
        'capability'   => 'manage_options',
        'when'         => null, // callable|null
        'position'     => 'br', // br|bl|tr|tl
        'max_width'    => 560,
        'max_height'   => '60vh',
        'z_index'      => 999999,
        'priority'     => 9999,
        'show_file'    => true,
        'compact'      => false, // true: gọn hơn
        'search'       => '',    // lọc label chứa chuỗi
        'highlight'    => '',    // highlight label chứa chuỗi
    ];

    /**
     * Boot debugger
     *
     * @param array<string, mixed> $config
     */
    public static function boot(array $config = []): void
    {
        if (self::$booted) return;
        self::$booted = true;

        self::$cfg = array_merge(self::$cfg, $config);

        if (!self::$cfg['enabled']) return;

        add_action('wp_footer', [__CLASS__, 'render'], (int) self::$cfg['priority']);
    }

    public static function render(): void
    {
        if (!self::$cfg['enabled']) return;

        if (!empty(self::$cfg['only_admin'])) {
            $cap = (string) (self::$cfg['capability'] ?? 'manage_options');
            if (!current_user_can($cap)) return;
        }

        if (is_callable(self::$cfg['when'])) {
            try {
                if (!call_user_func(self::$cfg['when'])) return;
            } catch (\Throwable $e) {
                // nếu when() lỗi thì bỏ render để tránh phá trang
                return;
            }
        }

        $hooks = (array) (self::$cfg['hooks'] ?? []);
        $hooks = array_values(array_filter(array_map('strval', $hooks)));
        if (!$hooks) return;

        $posCss = self::positionCss((string) self::$cfg['position']);

        echo '<div style="' . esc_attr(
            'position:fixed;'
                . $posCss
                . 'z-index:' . (int) self::$cfg['z_index'] . ';'
                . 'background:#0b0b0b;color:#b5ffb5;'
                . 'padding:12px;'
                . 'max-width:' . (int) self::$cfg['max_width'] . 'px;'
                . 'max-height:' . esc_attr((string) self::$cfg['max_height']) . ';'
                . 'overflow:auto;'
                . 'font:12px/1.4 ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;'
                . 'border-radius:10px;'
                . 'border:1px solid rgba(255,255,255,0.08);'
                . 'box-shadow:0 10px 30px rgba(0,0,0,0.45);'
        ) . '">';

        echo '<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">';
        echo '<div style="color:#fff;font-weight:700;">HOOK DEBUGGER</div>';
        echo '<div style="opacity:.8;">' . esc_html(home_url()) . '</div>';
        echo '</div>';

        foreach ($hooks as $hook) {
            self::renderHookBlock($hook);
        }

        echo '</div>';
    }

    private static function renderHookBlock(string $hook): void
    {
        global $wp_filter;

        echo '<div style="padding:10px;border-radius:8px;background:rgba(255,255,255,0.03);margin-bottom:10px;">';
        echo '<div style="color:#fff;font-weight:700;margin-bottom:6px;">' . esc_html($hook) . '</div>';

        if (empty($wp_filter[$hook])) {
            echo '<div style="opacity:.8;">No callbacks.</div>';
            echo '</div>';
            return;
        }

        $callbacks = $wp_filter[$hook]->callbacks ?? [];
        if (!$callbacks) {
            echo '<div style="opacity:.8;">No callbacks.</div>';
            echo '</div>';
            return;
        }

        ksort($callbacks);

        $search    = (string) (self::$cfg['search'] ?? '');
        $highlight = (string) (self::$cfg['highlight'] ?? '');

        $count = 0;

        foreach ($callbacks as $priority => $items) {
            foreach ($items as $id => $item) {
                $info = self::describeCallback($item['function'] ?? null);

                if ($search !== '' && stripos($info['label'], $search) === false) {
                    continue;
                }

                $count++;

                $isHL = ($highlight !== '' && stripos($info['label'], $highlight) !== false);

                echo '<div style="padding:8px;border-radius:6px;'
                    . 'background:' . ($isHL ? 'rgba(255,255,0,0.10)' : 'rgba(255,255,255,0.02)') . ';'
                    . 'border:1px solid rgba(255,255,255,0.06);'
                    . 'margin:6px 0;">';

                echo '<div>';
                echo '• <span style="color:#fff;font-weight:700;">' . esc_html((string) $priority) . '</span>';
                echo ' — <span style="color:#d6ffd6;">' . esc_html($info['label']) . '</span>';
                echo '</div>';

                if (!empty(self::$cfg['show_file']) && !empty($info['file'])) {
                    if (!empty(self::$cfg['compact'])) {
                        echo '<div style="opacity:.75;margin-top:4px;">↳ ' . esc_html(basename($info['file']) . ':' . $info['line']) . '</div>';
                    } else {
                        echo '<div style="opacity:.75;margin-top:4px;">↳ ' . esc_html($info['file'] . ':' . $info['line']) . '</div>';
                    }
                }

                echo '</div>';
            }
        }

        echo '<div style="opacity:.8;margin-top:8px;">Total: <span style="color:#fff;font-weight:700;">' . esc_html((string) $count) . '</span></div>';
        echo '</div>';
    }

    /**
     * @param mixed $fn
     * @return array{label:string,file:string,line:int}
     */
    private static function describeCallback($fn): array
    {
        $label = 'Unknown';
        $file  = '';
        $line  = 0;

        // string function
        if (is_string($fn)) {
            $label = $fn;
            try {
                $ref  = new \ReflectionFunction($fn);
                $file = (string) $ref->getFileName();
                $line = (int) $ref->getStartLine();
            } catch (\Throwable $e) {
            }

            return compact('label', 'file', 'line');
        }

        // array [object/class, method]
        if (is_array($fn) && isset($fn[0], $fn[1])) {
            $objOrClass = is_object($fn[0]) ? get_class($fn[0]) : (string) $fn[0];
            $label = $objOrClass . '::' . (string) $fn[1];

            try {
                $ref  = new \ReflectionMethod($fn[0], (string) $fn[1]);
                $file = (string) $ref->getFileName();
                $line = (int) $ref->getStartLine();
            } catch (\Throwable $e) {
            }

            return compact('label', 'file', 'line');
        }

        // closure
        if ($fn instanceof \Closure) {
            $label = 'Closure';
            try {
                $ref  = new \ReflectionFunction($fn);
                $file = (string) $ref->getFileName();
                $line = (int) $ref->getStartLine();
            } catch (\Throwable $e) {
            }

            return compact('label', 'file', 'line');
        }

        return compact('label', 'file', 'line');
    }

    private static function positionCss(string $pos): string
    {
        // br|bl|tr|tl
        return match ($pos) {
            'bl' => 'bottom:12px;left:12px;',
            'tr' => 'top:12px;right:12px;',
            'tl' => 'top:12px;left:12px;',
            default => 'bottom:12px;right:12px;',
        };
    }
}
