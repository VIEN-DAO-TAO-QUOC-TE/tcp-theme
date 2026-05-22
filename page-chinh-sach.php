<?php
/**
 * Custom page template by slug: chinh-sach
 * Template Name: TCP - Chinh Sach
 *
 * @package TCP Theme
 */

defined('ABSPATH') || exit;

get_header();

$fieldText = static function (string $key, string $default = ''): string {
  if (!function_exists('get_field')) {
    return $default;
  }

  $value = get_field($key);
  if (!is_string($value)) {
    return $default;
  }

  $value = trim($value);
  return $value === '' ? $default : $value;
};

$fieldHtml = static function (string $key, string $default = ''): string {
  if (!function_exists('get_field')) {
    return $default;
  }

  $value = get_field($key);
  if (!is_string($value)) {
    return $default;
  }

  $value = trim($value);
  return $value === '' ? $default : $value;
};

$defaultTermsIntro = '<p>Talent Connect Plus Terms of Use last updated September 11, 2025. Please review these Terms carefully as they form a binding contract between us and contain crucial information about your legal rights, remedies, and obligations.</p>';
$defaultTermsIntro .= '<p>We empower anyone, anywhere, to create and share educational services and to access those services for learning. We believe our marketplace model is the best way to provide valuable educational services to users.</p>';
$defaultTermsIntro .= '<p>By using our Services, you also agree to the Instructor Terms where applicable, and our Privacy Policy. These policies are incorporated by reference into these Terms.</p>';

$defaultTermsBottom = '<p>Under our Instructor Terms, when instructors publish content, they grant us a license to offer that content to students. This means we have the right to sublicense content to enrolled students and to support platform operations.</p>';
$defaultTermsBottom .= '<p>Unless indicated otherwise in relevant terms, content is licensed and not sold, and access rights are limited, non-exclusive, non-transferable, and revocable in specific cases such as policy violations or legal requirements.</p>';

$defaultPrivacyContent = '<p>Your privacy matters. This section explains what personal data we collect, why we collect it, and how we use and protect it when you use Talent Connect Plus websites, applications, and services.</p>';
$defaultPrivacyContent .= '<p>We process data to provide our services, maintain account security, personalize learning experiences, and communicate updates related to your account and purchases.</p>';
$defaultPrivacyContent .= '<p>If you have requests related to your personal data, such as access, correction, or deletion, you can contact our support team using the channels published on the website.</p>';

$termsToc = [
  'Accounts',
  'Content Enrollment and Lifetime Access',
  'Payments, Credits, and Refunds',
  'Content and Behavior Guidelines',
  'Talent Connect Plus\'s Rights to Content You Post',
  'Using Talent Connect Plus at Your Own Risk',
  'Talent Connect Plus\'s Rights',
  'Coaching Services',
  'Miscellaneous Legal Terms',
  'Dispute Resolution',
];

if (function_exists('get_field')) {
  $tocRows = get_field('tcp_policy_terms_toc');
  if (is_array($tocRows) && !empty($tocRows)) {
    $acfToc = [];
    foreach ($tocRows as $row) {
      $item = isset($row['item']) && is_string($row['item']) ? trim($row['item']) : '';
      if ($item !== '') {
        $acfToc[] = $item;
      }
    }
    if (!empty($acfToc)) {
      $termsToc = $acfToc;
    }
  }
}

$tabs = [
  [
    'id' => 'terms-of-use',
    'label' => $fieldText('tcp_policy_terms_nav_label', 'Terms of Use'),
    'title' => $fieldText('tcp_policy_terms_title', 'Terms of Use'),
  ],
  [
    'id' => 'privacy-policy',
    'label' => $fieldText('tcp_policy_privacy_nav_label', 'Privacy Policy'),
    'title' => $fieldText('tcp_policy_privacy_title', 'Privacy Policy'),
  ],
];

$tocTitle = $fieldText('tcp_policy_toc_title', 'Table of Contents');
$termsIntroHtml = $fieldHtml('tcp_policy_terms_intro', $defaultTermsIntro);
$termsBottomHtml = $fieldHtml('tcp_policy_terms_bottom', $defaultTermsBottom);
$privacyContentHtml = $fieldHtml('tcp_policy_privacy_content', $defaultPrivacyContent);

?>

<style>
  .page-template-page-chinh-sach {
    background: #f3f4f6;
  }

  .tcp-policy-page {
    background: transparent;
    min-height: 100vh;
    padding: 56px 0 96px;
  }

  .tcp-policy-page__inner {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 32px;
    display: grid;
    grid-template-columns: 228px minmax(0, 1fr);
    gap: 56px;
    align-items: start;
  }

  .tcp-policy-page__side {
    position: sticky;
    top: 88px;
  }

  .tcp-policy-page__nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .tcp-policy-page__nav-link {
    display: block;
    border-radius: 12px;
    color: #374151;
    font-family: var(--font-definitions-font-family-body, Inter);
    font-size: var(--paragraph-regular-font-size, 16px);
    line-height: var(--paragraph-regular-line-height, 24px);
    text-decoration: none;
    padding: 12px 14px;
    transition: background-color .2s ease, color .2s ease;
  }

  .tcp-policy-page__nav-link.is-active,
  .tcp-policy-page__nav-link:hover {
    background: #e5e7eb;
    color: #111827;
    font-weight: 600;
  }

  .tcp-policy-page__content {
    min-width: 0;
  }

  .tcp-policy-page__section + .tcp-policy-page__section {
    margin-top: 64px;
  }

  .tcp-policy-page__title {
    font-size: clamp(30px, 3.2vw, 42px);
    line-height: 1.15;
    font-weight: 700;
    color: #111827;
    margin: 0 0 20px;
  }

  .tcp-policy-page__paragraph {
    color: #374151;
    font-size: var(--paragraph-regular-font-size, 16px);
    line-height: var(--paragraph-regular-line-height, 24px);
    margin: 0 0 16px;
  }

  .tcp-policy-page__paragraph p {
    color: inherit;
    font-size: inherit;
    line-height: inherit;
    margin: 0 0 16px;
  }

  .tcp-policy-page__paragraph p:last-child {
    margin-bottom: 0;
  }

  .tcp-policy-page__sub-title {
    font-family: var(--font-definitions-font-family-headings, Inter);
    font-size: var(--heading-3-font-size, 24px);
    line-height: 1.2;
    margin: 40px 0 16px;
    font-weight: 700;
    color: #111827;
  }

  .tcp-policy-page__toc {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: 12px;
  }

  .tcp-policy-page__toc-item {
    color: #1f2937;
    font-size: var(--paragraph-regular-font-size, 16px);
    line-height: var(--paragraph-regular-line-height, 24px);
  }

  @media (max-width: 900px) {
    .tcp-policy-page {
      padding: 24px 0 64px;
    }

    .tcp-policy-page__inner {
      grid-template-columns: 1fr;
      gap: 20px;
      padding: 0 16px;
    }

    .tcp-policy-page__side {
      position: static;
      top: auto;
    }

    .tcp-policy-page__nav {
      flex-direction: row;
      align-items: center;
      gap: 10px;
      overflow-x: auto;
      white-space: nowrap;
      padding-bottom: 8px;
    }

    .tcp-policy-page__nav-link {
      flex: 0 0 auto;
      border: 1px solid #d1d5db;
      background: #ffffff;
      font-size: 14px;
      line-height: 20px;
      padding: 10px 12px;
    }

    .tcp-policy-page__sub-title {
      margin-top: 32px;
      font-size: var(--heading-3-font-size, 24px);
    }

    .tcp-policy-page__toc-item {
      font-size: 14px;
    }
  }
</style>

<main id="main" class="site-main tcp-policy-page" role="main">
  <div class="tcp-policy-page__inner">
    <aside class="tcp-policy-page__side" aria-label="<?php esc_attr_e('Policy navigation', 'tcp-theme'); ?>">
      <nav class="tcp-policy-page__nav">
        <?php foreach ($tabs as $index => $tab) : ?>
          <a
            class="tcp-policy-page__nav-link <?php echo $index === 0 ? 'is-active' : ''; ?>"
            href="#<?php echo esc_attr($tab['id']); ?>"
            data-policy-target="<?php echo esc_attr($tab['id']); ?>"
          >
            <?php echo esc_html($tab['label']); ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </aside>

    <div class="tcp-policy-page__content">
      <section id="terms-of-use" class="tcp-policy-page__section">
        <h1 class="tcp-policy-page__title"><?php echo esc_html($tabs[0]['title']); ?></h1>

        <div class="tcp-policy-page__paragraph"><?php echo wp_kses_post($termsIntroHtml); ?></div>

        <h2 class="tcp-policy-page__sub-title"><?php echo esc_html($tocTitle); ?></h2>
        <ul class="tcp-policy-page__toc">
          <?php foreach ($termsToc as $tocItem) : ?>
            <li class="tcp-policy-page__toc-item"><?php echo esc_html($tocItem); ?></li>
          <?php endforeach; ?>
        </ul>

        <div class="tcp-policy-page__paragraph" style="margin-top: 34px;"><?php echo wp_kses_post($termsBottomHtml); ?></div>
      </section>

      <section id="privacy-policy" class="tcp-policy-page__section">
        <h2 class="tcp-policy-page__title"><?php echo esc_html($tabs[1]['title']); ?></h2>
        <div class="tcp-policy-page__paragraph"><?php echo wp_kses_post($privacyContentHtml); ?></div>
      </section>
    </div>
  </div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.tcp-policy-page');
    if (!page) {
      return;
    }

    const links = Array.from(page.querySelectorAll('.tcp-policy-page__nav-link'));
    const sections = links
      .map((link) => document.getElementById(link.getAttribute('data-policy-target')))
      .filter(Boolean);

    const setActive = (id) => {
      links.forEach((link) => {
        const isActive = link.getAttribute('data-policy-target') === id;
        link.classList.toggle('is-active', isActive);
        if (isActive) {
          link.setAttribute('aria-current', 'page');
        } else {
          link.removeAttribute('aria-current');
        }
      });
    };

    links.forEach((link) => {
      link.addEventListener('click', function (event) {
        const targetId = this.getAttribute('data-policy-target');
        const target = document.getElementById(targetId);
        if (!target) {
          return;
        }

        event.preventDefault();
        setActive(targetId);
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (history.replaceState) {
          history.replaceState(null, '', '#' + targetId);
        }
      });
    });

    const initialId = window.location.hash ? window.location.hash.replace('#', '') : 'terms-of-use';
    if (initialId && document.getElementById(initialId)) {
      setActive(initialId);
    }

    if ('IntersectionObserver' in window && sections.length) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            setActive(entry.target.id);
          }
        });
      }, {
        root: null,
        rootMargin: '-35% 0px -55% 0px',
        threshold: 0.1,
      });

      sections.forEach((section) => observer.observe(section));
    }
  });
</script>

<?php
get_footer();
