<?php

namespace TCP\Theme\Services\Ux\Sections\About;

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

        #custom-about-page .blue-color {

        }
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
            font-size: 12px;
            letter-spacing: 0.08em;
            color: var(--gray-500);
            margin: 0 0 4px;
        }
        #custom-about-page .hero-h1 {
            font-size: 48px;
            color: var(--Color-Gray-gray-900);
            font-weight: 800;
            line-height: 1.05;
            margin: 15px 0;
        }

        #custom-about-page .hero-p {
            font-family: var(--font-family-body);
            font-weight: 400;
            font-style: normal;
            font-size: var(--paragraph-small-font-size);
            line-height: var(--paragraph-small-line-height);
            letter-spacing: 0.5%;
            vertical-align: middle;
            color: rgba(31, 41, 55, 1);
        }

        #custom-about-page .hero-mobile-image {
            margin-top: 18px;
        }

        #custom-about-page .hero-mobile-image img {
            width: 100%;
            border-radius: 16px;
            display: block;
        }

        #custom-about-page .section-values-give {
            background: linear-gradient(180deg, #FFFFFF 0%, #EEF2FF 100%);
            height: 672px;
            padding: 58px 0 68px;
            overflow: hidden;
        }

        #custom-about-page .section-values-give__container {
            display: flex;
            flex-direction: column;
            gap: 36px;
        }

        #custom-about-page .values-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 0.5fr) minmax(0, 0.5fr);
            column-gap: 34px;
            align-items: start;
        }

        #custom-about-page .values-label {
            margin: 0 0 4px;
            color: var(--gray-500);
            font-size: 12px;
            font-weight: 700;
        }

        #custom-about-page .values-main h2 {
            margin: 0;
            font-size: var(--heading-2-font-size, 30px);
            line-height: 1.08;
            color: #1d2535;
            letter-spacing: -0.02em;
        }

        #custom-about-page .values-main h2 span {
            color: var(--tc-orange);
        }

        #custom-about-page .values-side h3 {
            margin: 6px 0 10px;
            font-size: var(--heading-3-font-size, 24px);
            color: #232a3a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #custom-about-page .values-icon {
            font-size: 18px;
            color: #3e4459;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            line-height: 1;
        }

        #custom-about-page .values-icon img {
            width: 24px;
            height: 24px;
        }

        #custom-about-page .values-side p {
            font-family: var(--font-definitions-font-family-body, Inter);
            font-weight: 400;
            font-style: normal;
            font-size: var(--paragraph-small-font-size, 14px);
            line-height: var(--paragraph-small-line-height, 22px);
            letter-spacing: 0.005em;
            vertical-align: middle;
        }

        #custom-about-page .give-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        #custom-about-page .give-item {
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            min-height: 225px;
            cursor: pointer;
            padding: 0 8px;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #custom-about-page .give-char {
            display: inline-block;
            font-family: Inter, sans-serif;
            font-weight: 700;
            font-style: normal;
            font-size: 200px;
            line-height: 224.95px;
            letter-spacing: 0.04em;
            text-align: center;
            vertical-align: middle;
            background: linear-gradient(180deg, #312E81 0%, #6366F1 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        #custom-about-page .give-content {
            display: none;
            position: absolute;
            inset: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 8px;
        }

        #custom-about-page .give-item:hover .give-char,
        #custom-about-page .give-item:focus-within .give-char {
            visibility: hidden;
        }

        #custom-about-page .give-item:hover .give-content,
        #custom-about-page .give-item:focus-within .give-content {
            display: flex;
        }

        #custom-about-page .give-content__label {
            display: block;
            color: var(--tc-blue, #312E81);
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
            line-height: 1.3;
        }

        #custom-about-page .give-content__text {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
        }

        #custom-about-page .give-dot {
            font-size: 40px;
            color: var(--tc-orange);
            margin: 0 25px;
        }

        #custom-about-page .section-values-give__give-mobile { padding: 32px 0 12px; }

        #custom-about-page .give-m-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        #custom-about-page .give-char-m {
            font-family: Inter, sans-serif;
            font-weight: 700;
            font-style: normal;
            font-size: 60px;
            line-height: 64px;
            letter-spacing: 0;
            text-align: center;
            vertical-align: middle;
            background: linear-gradient(180deg, #312E81 0%, #6366F1 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            margin-right: 20px;
        }

        #custom-about-page .give-m-info strong {
            color: var(--tc-blue);
            font-size: 18px;
        }

        #custom-about-page .section-team {
            background-color: var(--white, #fff);
            padding: 80px 0;
        }

        #custom-about-page .section-team .about-team-mobile-nav {
            display: none;
        }

        #custom-about-page .section-title {
            margin-bottom: 24px;
            text-align: left;
        }

        #custom-about-page .section-title h6 {
            color: #4F46E5;
            font-family: var(--font-definitions-font-family-body, Inter);
            font-size: 12px;
            font-weight: 700;
            line-height: var(--paragraph-mini-line-height, 133.333%);
            letter-spacing: 0.18px;
            margin-bottom: 0;
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
                grid-template-columns: minmax(0, 1fr) minmax(0, 0.5fr) minmax(0, 0.5fr);
                column-gap: 22px;
            }
            #custom-about-page .values-main h2 { font-size: 42px; }
            #custom-about-page .values-side h3 { font-size: 20px; }
            #custom-about-page .values-side p { font-size: 14px; }
            #custom-about-page .give-char {
                font-size: 200px;
                line-height: 224.95px;
            }
            #custom-about-page .give-dot { margin: 0 18px; }
            #custom-about-page .section-title h2 { font-size: 38px; }
            #custom-about-page .member-name { font-size: 20px; }
            #custom-about-page .contact-form-padding h3 { font-size: 34px; }
        }

        @media (max-width: 849px) {
            #custom-about-page.about-wrapper { padding-top: 16px; }
            #custom-about-page .sub-title {
                line-height: 16px;
                padding-top: 10px;
            }
            #custom-about-page .about-breadcrumb { margin: 0 0 14px; }
            .about-breadcrumb p {margin-bottom:0!important;}

            #custom-about-page .section-hero {
                width: 100%;
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

            #custom-about-page .section-values-give {
                height: auto;
                padding: 28px 0 12px;
            }

            #custom-about-page .section-values-give__container {
                gap: 18px;
            }
            #custom-about-page .values-grid {
                grid-template-columns: 1fr;
                row-gap: 14px;
            }
            #custom-about-page .values-main h2 { font-size: 18px; line-height: 1.3; }
            #custom-about-page .values-side { margin-top: 0; }
            #custom-about-page .values-side h3 { font-size: 16px; }
            #custom-about-page .values-side p { font-size: 14px; }

            #custom-about-page .section-values-give__give {
                display: none;
            }

            #custom-about-page .section-values-give__give-mobile {
                display: block;
                padding: 30px 0 4px;
            }

            #custom-about-page .give-m-item {
                display: grid;
                grid-template-columns: 84px minmax(0, 1fr);
                column-gap: 18px;
                align-items: center;
                margin-bottom: 0;
            }

            #custom-about-page .give-m-letter {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #custom-about-page .give-m-divider {
                display: flex;
                justify-content: flex-start;
                padding-left: 32px;
                margin: 12px 0 12px;
            }

            #custom-about-page .give-m-divider .give-dot {
                font-size: 34px;
                line-height: 1;
                margin: 0;
            }

            #custom-about-page .give-m-info strong {
                display: block;
                font-family: var(--font-family-body, Inter, sans-serif);
                font-weight: 700;
                font-style: normal;
                font-size: var(--paragraph-regular-font-size, 16px);
                line-height: var(--paragraph-regular-line-height, 24px);
                letter-spacing: var(--paragraph-regular-letter-spacing, 0);
                vertical-align: middle;
                margin-bottom: 8px;
            }

            #custom-about-page .give-m-info p {
                margin: 0;
                font-family: var(--font-family-body, Inter, sans-serif);
                font-weight: 500;
                font-style: normal;
                font-size: var(--paragraph-small-font-size, 14px);
                line-height: var(--paragraph-small-line-height, 20px);
                letter-spacing: 0.005em;
                vertical-align: middle;
                color: var(--general-secondary-foreground, #312E81);
            }

            #custom-about-page .give-char-m {
                margin-right: 0;
            }

            #custom-about-page .section-title h2 {
                font-size: 30px;
                line-height: 36px;
                text-align: left;
                color: #1F2937;
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
            #custom-about-page .section-team { padding: 24px 0; }
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

        @media (min-width: 744px) and (max-width: 1024px) {
            /* About team only: force iPad layout to 3 columns. */
            #custom-about-page .section-team .about-team-trainers > .col {
                flex: 0 0 33.3333% !important;
                max-width: 33.3333% !important;
            }
        }

        @media (max-width: 375px) {
            #custom-about-page .hero-h1 { font-size: 24px; }
            #custom-about-page .values-main h2 { font-size: 17px; }
            #custom-about-page .member-name { font-size: 24px; }
            #custom-about-page .give-char-m {
                font-size: 60px;
                line-height: 64px;
            }
            #custom-about-page .give-m-item {
                grid-template-columns: 72px minmax(0, 1fr);
                column-gap: 14px;
            }
            #custom-about-page .give-m-divider {
                padding-left: 26px;
            }
            #custom-about-page .give-m-info strong {
                font-size: var(--paragraph-regular-font-size, 16px);
            }
            #custom-about-page .give-m-info p {
                font-size: var(--paragraph-small-font-size, 14px);
            }
            #custom-about-page .contact-form-padding h3 { font-size: 30px; }
        }

        @media (max-width: 549px) {
            /* About team only: mobile horizontal slider with 1.5 cards visible. */
            #custom-about-page .section-team .about-team-trainers {
                flex-wrap: nowrap;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                scrollbar-width: none;
            }

            #custom-about-page .section-team .about-team-trainers::-webkit-scrollbar {
                display: none;
            }

            #custom-about-page .section-team .about-team-trainers > .col {
                flex: 0 0 66.6667% !important;
                max-width: 66.6667% !important;
                scroll-snap-align: start;
            }

            #custom-about-page .section-team .about-team-mobile-nav {
                display: grid;
                grid-template-columns: 32px auto 32px;
                align-items: center;
                justify-content: center;
                gap: var(--semantic-xs, 8px);
                margin-top: 18px;
            }

            #custom-about-page .section-team .about-team-mobile-nav__btn {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                min-height: 32px !important;
                max-width: 32px;
                max-height: 32px;
                border-radius: 50%!important;
                border: 0;
                background: #e7e9ef;
                color: #312e81;
                font-size: 20px;
                line-height: 1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 !important;
                flex: 0 0 32px;
                margin-right: 0;
                margin-bottom:0;
                aspect-ratio: 1 / 1;
            }

            #custom-about-page .section-team .about-team-mobile-nav__btn[disabled] {
                opacity: 0.45;
            }

            #custom-about-page .section-team .about-team-mobile-nav__count {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: var(--semantic-xs, 8px);
                width: auto;
                min-width: max-content;
                height: 38px;
                min-height: 36px;
                padding: 8px var(--semantic-md, 16px);
                border-radius: var(--semantic-rounded-lg, 8px);
                color: #6b7280;
                font-size:var(--paragraph-small-font-size, 14px);
                font-weight: 500;
                line-height: 1;
                opacity: 1;
                white-space: nowrap;
            }

            #custom-about-page .section-team .about-team-mobile-nav__dash {
                transform: translateY(-1px);
            }
        }
        ';
    }
}
