<?php
/* Template:
 * Template Name: About Us Custom Dynamic
 * Template Post Type: page
 */
get_header();

// --- FETCH DATA & FALLBACKS ---
$hero_bg    = get_field('hero_bg') ?: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b';
$hero_title = get_field('hero_title') ?: 'Nơi tôi luyện lãnh đạo và <span style="color:#ed6c36">tiếp nối những di sản</span>';
$hero_desc  = get_field('hero_desc') ?: 'Chúng tôi tin vào sức mạnh của giáo dục thực tiễn, giúp học viên phát triển sự nghiệp và cuộc sống.';

$give_items = get_field('give_list') ?: [
    [
        'char'  => 'G',
        'label' => 'Growth',
        'text'  => 'Chúng tôi tin vào khả năng phát triển và trưởng thành liên tục của con người, nơi mỗi thách thức là một cơ hội để học hỏi, để cải thiện, và vươn lên vững vàng hơn phiên bản ngày hôm qua của chính mình.'
    ],
    [
        'char'  => 'I',
        'label' => 'Integrity',
        'text'  => 'Chúng tôi theo đuổi sự chính trực như nền tảng của mọi kết nối và dẫn dắt, nơi lời nói, hành động và giá trị sống luôn thống nhất, kể cả trong những lựa chọn khó khăn nhất.'
    ],
    [
        'char'  => 'V',
        'label' => 'Value',
        'text'  => 'Chúng tôi không chỉ hướng tới kết quả, mà còn hướng tới tạo giá trị bền vững, cho người học, cho tổ chức, và cho xã hội. Mỗi hành động đều phải tạo ra giá trị thực, giá trị của năng lực, tư duy, và sự chuyển hóa.'
    ],
    [
        'char'  => 'E',
        'label' => 'Educator\'s Heart',
        'text'  => 'Chúng tôi làm mọi việc bằng trái tim của người làm giáo dục, đủ lắng nghe để thấu hiểu, đủ kỳ vọng để truyền động lực, và đủ kiên nhẫn để đồng hành cùng người học trên hành trình khám phá năng lực, định hình tư duy và vững bước thành người dẫn đầu.'
    ],
];

$team_members = get_field('team_list') ?: array_fill(0, 4, [
    'name' => 'Tên Thành Viên',
    'pos'  => 'Chức vụ tại công ty',
    'img'  => 'https://via.placeholder.com/400x500'
]);
?>

<div id="custom-about-page" class="about-wrapper">

    <section class="section-hero" style="background-image: url('<?php echo esc_url($hero_bg); ?>');">
        <div class="container">
            <div class="hero-card shadow-3">
                <h6 class="accent-color">VỀ CHÚNG TÔI</h6>
                <h1 class="hero-h1"><?php echo $hero_title; ?></h1>
                <div class="hero-p"><?php echo $hero_desc; ?></div>
            </div>
        </div>
    </section>

    <section class="section-give hide-for-small">
        <div class="container text-center">
            <div class="give-row" data-give-row>
                <?php foreach ($give_items as $index => $item): ?>
                    <div class="give-item"
                         data-give-key="<?php echo esc_attr($item['char']); ?>"
                         data-give-label="<?php echo esc_attr($item['label']); ?>"
                         data-give-text="<?php echo esc_attr($item['text']); ?>">
                        <span class="give-char"><?php echo esc_html($item['char']); ?></span>
                    </div>
                    <?php if ($index < count($give_items) - 1): ?>
                        <span class="give-dot">•</span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="give-detail" data-give-detail aria-live="polite">
                <h3 class="give-detail__label" data-give-detail-label></h3>
                <p class="give-detail__text" data-give-detail-text></p>
            </div>
        </div>
    </section>

    <section class="section-give-mobile show-for-small">
        <div class="container">
            <?php foreach ($give_items as $index => $item): ?>
                <div class="give-m-row">
                    <h2 class="give-char-m"><?php echo esc_html($item['char']); ?></h2>
                    <div class="give-m-info">
                        <strong><?php echo esc_html($item['label']); ?></strong>
                        <p><?php echo esc_html($item['text']); ?></p>
                    </div>
                </div>
                <?php if ($index < count($give_items) - 1): ?>
                    <div class="give-m-divider" aria-hidden="true"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-team">
        <div class="container">
            <div class="section-title text-center">
                <h6 class="blue-color">C-SHERPA</h6>
                <h2>Gặp gỡ các C-Sherpa</h2>
            </div>
            <div class="row row-small team-grid">
                <?php foreach ($team_members as $member): ?>
                    <div class="col medium-3 small-12">
                        <div class="sherpa-card">
                            <div class="card-image">
                                <img src="<?php echo esc_url($member['img']); ?>" alt="<?php echo esc_attr($member['name']); ?>">
                            </div>
                            <div class="card-body text-left">
                                <h4 class="member-name"><?php echo esc_html($member['name']); ?></h4>
                                <span class="member-pos"><?php echo esc_html($member['pos']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section-contact container">
        <div class="contact-box">
            <div class="row row-collapse align-middle">
                <div class="col medium-6 hide-for-small">
                    <img src="https://via.placeholder.com/600x450" class="contact-img" alt="Workplace">
                </div>
                <div class="col medium-6 small-12 text-center contact-form-padding">
                    <h6 class="gray-color">LIÊN HỆ</h6>
                    <h3>Kết nối cùng Talent Connect Plus</h3>
                    <div class="custom-form-wrapper">
                        <?php echo do_shortcode('[contact-form-7 id="c12345" title="About Contact"]'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>
