<?php

namespace TCP\Theme\Services\Ux\Sections;

defined('ABSPATH') || exit;

final class AboutStylesSection
{
    public static function render(): string
    {
        return '
        :root {
            --tc-blue: #3f4ab2;
            --tc-orange: #ed6c36;
            --tc-gray-bg: #f4f6f8;
        }

        #custom-about-page.about-wrapper {
            background-color: #fff;
            color: #333;
            line-height: 1.6;
            padding-top: 26px;
        }

        #custom-about-page .about-breadcrumb {
            margin: 0 0 20px;
            font-size: 12px;
            color: #8c8f9f;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #custom-about-page .about-breadcrumb a {
            color: #8c8f9f;
            text-decoration: none;
        }

        #custom-about-page .about-breadcrumb span:last-child {
            color: #222c3a;
            font-weight: 600;
        }

        #custom-about-page .blue-color { color: var(--Color-Gray-gray-900); }
        #custom-about-page .accent-color { color: var(--tc-orange); }

        #custom-about-page .section-hero {
            background-size: cover;
            background-position: center;
            padding: 120px 0;
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            width: min(1320px, calc(100% - 48px));
            margin: 0 auto;
        }

        #custom-about-page .hero-card {
            background: #fff;
            padding: 50px;
            width: 50%;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            margin: 24px 0;
            
        }
        #custom-about-page .sub-title {
            font-size: 14px;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin: 0 0 8px;
        }
        #custom-about-page .hero-h1 {
            font-size: 48px;
            color: var(--Color-Gray-gray-900);
            font-weight: 800;
            line-height: 1.05;
            margin: 15px 0;
        }

        #custom-about-page .hero-p {
            font-size: 15px;
            color: var(--Color-Gray-gray-900);
        }

        #custom-about-page .hero-mobile-image {
            margin-top: 18px;
        }

        #custom-about-page .hero-mobile-image img {
            width: 100%;
            border-radius: 16px;
            display: block;
        }

        #custom-about-page .section-values {
            padding: 58px 0 36px;
        }

        #custom-about-page .values-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1fr) minmax(0, 1fr);
            column-gap: 34px;
            align-items: start;
        }

        #custom-about-page .values-label {
            margin: 0 0 10px;
            color: #8a90a3;
            letter-spacing: 0.08em;
            font-size: 11px;
            font-weight: 700;
        }

        #custom-about-page .values-main h2 {
            margin: 0;
            font-size: 52px;
            line-height: 1.08;
            color: #1d2535;
            letter-spacing: -0.02em;
        }

        #custom-about-page .values-main h2 span {
            color: var(--tc-orange);
        }

        #custom-about-page .values-side h3 {
            margin: 6px 0 10px;
            font-size: 24px;
            color: #232a3a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #custom-about-page .values-icon {
            font-size: 18px;
            color: #3e4459;
        }

        #custom-about-page .values-side p {
            margin: 0;
            font-size: 16px;
            color: #4f5768;
            line-height: 1.6;
        }

        #custom-about-page .section-give {
            padding: 54px 0 68px;
            background: #eceef8;
        }

        #custom-about-page .give-flex {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #custom-about-page .give-char {
            font-size: 110px;
            font-weight: 900;
            color: var(--tc-blue);
            line-height: 1;
            letter-spacing: 0.04em;
        }

        #custom-about-page .give-dot {
            font-size: 40px;
            color: var(--tc-orange);
            margin: 0 25px;
        }

        #custom-about-page .section-give-mobile { padding: 32px 0 12px; }

        #custom-about-page .give-m-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        #custom-about-page .give-char-m {
            font-size: 60px;
            font-weight: 900;
            color: var(--tc-blue);
            margin-right: 20px;
            line-height: 1;
        }

        #custom-about-page .give-m-info strong {
            color: var(--tc-blue);
            font-size: 18px;
        }

        #custom-about-page .section-team {
            background-color: var(--tc-gray-bg);
            padding: 80px 0;
        }

        #custom-about-page .section-title {
            margin-bottom: 24px;
            text-align: left;
        }

        #custom-about-page .section-title h6 {
            font-size: 10px;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        #custom-about-page .section-title h2 {
            font-size: 46px;
            line-height: 1.08;
            color: #222a3a;
            margin: 0;
            letter-spacing: -0.015em;
        }

        #custom-about-page .team-grid {
            row-gap: 18px;
        }

        #custom-about-page .sherpa-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        #custom-about-page .card-image img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        #custom-about-page .card-body {
            padding: 14px 14px 16px;
            background-color: #eceef3;
        }

        #custom-about-page .member-name {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1f2737;
            line-height: 1.18;
        }

        #custom-about-page .member-pos {
            font-size: 12px;
            color: #888;
            line-height: 1.4;
            text-transform: none;
        }

        #custom-about-page .section-contact {
            padding: 56px 0 64px;
            background: #f5f5f7;
        }

        #custom-about-page .contact-box {
            background: #e9eaee;
            border-radius: 28px;
            padding: 18px;
        }

        #custom-about-page .contact-img {
            width: 100%;
            height: 100%;
            min-height: 460px;
            object-fit: cover;
            border-radius: 20px;
        }

        #custom-about-page .contact-form-padding {
            padding: 28px 22px;
        }

        #custom-about-page .gray-color {
            color: #7f8697;
            margin-bottom: 8px;
            font-size: 10px;
            letter-spacing: 0.08em;
        }

        #custom-about-page .contact-form-padding h3 {
            font-size: 48px;
            line-height: 1.05;
            margin: 0 0 20px;
            color: #222a3a;
        }

        #custom-about-page .custom-form-wrapper input,
        #custom-about-page .custom-form-wrapper select,
        #custom-about-page .custom-form-wrapper textarea {
            border-radius: 8px;
            border: 1px solid #d6d8de;
            min-height: 42px;
            font-size: 14px;
        }

        #custom-about-page .custom-form-wrapper input[type="submit"],
        #custom-about-page .custom-form-wrapper .wpcf7-submit {
            border: 0;
            border-radius: 8px;
            min-height: 44px;
            width: 100%;
            background: #4f46e5;
            color: #fff;
            font-weight: 700;
        }

        @media (max-width: 1024px) {
            #custom-about-page .section-hero {
                padding: 84px 0;
                width: min(960px, calc(100% - 32px));
            }
            #custom-about-page .hero-card {
                width: 72%;
                padding: 42px;
            }
            #custom-about-page .hero-h1 { font-size: 46px; }
            #custom-about-page .values-grid {
                grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) minmax(0, 1fr);
                column-gap: 22px;
            }
            #custom-about-page .values-main h2 { font-size: 42px; }
            #custom-about-page .values-side h3 { font-size: 20px; }
            #custom-about-page .values-side p { font-size: 14px; }
            #custom-about-page .give-char { font-size: 88px; }
            #custom-about-page .give-dot { margin: 0 18px; }
            #custom-about-page .section-title h2 { font-size: 38px; }
            #custom-about-page .member-name { font-size: 20px; }
            #custom-about-page .contact-form-padding h3 { font-size: 34px; }
        }

        @media (max-width: 849px) {
            #custom-about-page.about-wrapper { padding-top: 16px; }
            #custom-about-page .about-breadcrumb { margin: 0 0 14px; }

            #custom-about-page .section-hero {
                width: calc(100% - 24px);
                border-radius: 0;
                padding: 0;
                background-image: none !important;
            }

            #custom-about-page .hero-card {
                width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                border-top: 0;
                background: transparent;
            }

            #custom-about-page .hero-h1 { font-size: 42px; }
            #custom-about-page .hero-p { font-size: 14px; }

            #custom-about-page .section-values { padding: 28px 0 12px; }
            #custom-about-page .values-grid {
                grid-template-columns: 1fr;
                row-gap: 14px;
            }
            #custom-about-page .values-main h2 { font-size: 18px; line-height: 1.3; }
            #custom-about-page .values-side { margin-top: 0; }
            #custom-about-page .values-side h3 { font-size: 20px; }
            #custom-about-page .values-side p { font-size: 14px; }

            #custom-about-page .section-give {
                display: none;
            }

            #custom-about-page .section-give-mobile {
                display: block;
                background: #eceef8;
                padding: 30px 0 4px;
            }

            #custom-about-page .section-title h2 {
                font-size: 40px;
                text-align: left;
            }

            #custom-about-page .team-grid-mobile {
                display: flex;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                gap: 12px;
                margin: 0 -12px;
                padding: 0 12px 6px;
            }

            #custom-about-page .team-grid-mobile__item {
                min-width: 82%;
                flex: 0 0 auto;
                scroll-snap-align: start;
                padding-bottom: 4px;
            }

            #custom-about-page .card-image img { height: 340px; }
            #custom-about-page .section-team { padding: 56px 0; }
            #custom-about-page .member-name { font-size: 30px; }

            #custom-about-page .section-contact { padding: 24px 0 40px; }
            #custom-about-page .contact-box {
                border-radius: 20px;
                padding: 10px;
            }
            #custom-about-page .contact-img {
                min-height: 220px;
                border-radius: 14px;
            }
            #custom-about-page .contact-form-padding {
                text-align: center;
                padding: 14px 8px 8px;
            }
            #custom-about-page .contact-form-padding h3 {
                font-size: 32px;
            }
        }

        @media (max-width: 375px) {
            #custom-about-page .hero-h1 { font-size: 36px; }
            #custom-about-page .values-main h2 { font-size: 17px; }
            #custom-about-page .member-name { font-size: 24px; }
            #custom-about-page .give-char-m { font-size: 54px; }
            #custom-about-page .contact-form-padding h3 { font-size: 30px; }
        }
        ';
    }
}
