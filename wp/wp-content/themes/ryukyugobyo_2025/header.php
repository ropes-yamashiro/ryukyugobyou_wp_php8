<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Noto+Serif+JP:wght@200..900&family=Zen+Kaku+Gothic+New&display=swap"
    rel="stylesheet" />
  <title><?php if (is_home() || is_front_page()) : ?>
      <?php
            bloginfo('name');
            echo ' | ';
            bloginfo('description');
      ?>
    <?php elseif (!is_home() && !is_front_page()) : ?>
      <?php wp_title('', true, 'left');
            echo ' | ';
            bloginfo('description');
            echo ' | ';
            bloginfo('name');
      ?>
    <?php endif; ?></title>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-XLNZCGPJTY"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-XLNZCGPJTY');
  </script>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" />
  <?php if (is_home() || is_front_page()) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/top.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/notice.css" />
  <?php elseif (is_page('yuika')) : ?>
    <!-- Preconnect for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700;900&family=Noto+Serif+JP:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Referrer Policy: Imgurなど外部画像ホスティングのホットリンクブロック対策 -->
    <meta name="referrer" content="no-referrer">

    <!-- Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/yuika_lp/css/style.css?v=20260312b">
  <?php elseif (is_page('beauty')) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/beauty.css" />
  <?php elseif (is_page(array('feedback-list', 'letter-to-heaven', 'memorial-service'))) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/testimonials.css" />
  <?php elseif (is_page(array('news-list', 'news-closed', 'news-others', 'news-commercial'))) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/news.css" />
  <?php elseif (is_page(array('events-list', 'events-campaign', 'events-event'))) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/events.css" />
  <?php elseif (is_singular(array('news', 'event', 'feedback'))) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/post.css" />
  <?php elseif (is_404()) : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/top.css" />
  <?php else : ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/styles/pages/<?php echo $post->post_name ?>.css">
  <?php endif; ?>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/tagmanager.js"></script>
  <?php wp_head(); ?>
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KML53ZC" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <header class="header">
    <div id="logo-switcher"></div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const logoArea = document.getElementById('logo-switcher');

        if (urlParams.has('seimeinooka-jyumokusou')) {
          // パラメータがある場合
          logoArea.innerHTML = `
          <a href="/seimeinooka/yuika/" class="header__logo--form yashiro-logo">
          <img src="/wp/wp-content/themes/ryukyugobyo_2025/yuika_lp/images/yashiro-logo.png" alt="琉球御廟ロゴ">
        </a>
            `;
        } else {
          // パラメータがない場合（バッククォートに変更し、スペースを修正しました）
          logoArea.innerHTML = `
        <a href="/" class="header__logo--form">
          <img src="/wp/wp-content/themes/ryukyugobyo_2025/images/common/logo.svg" alt="琉球御廟ロゴ">
        </a>
      `;
        }
      });
    </script>
    <?php /*
    <a href="/" class="header__logo">
      <img src="<?php echo get_template_directory_uri(); ?>/images/common/logo.svg" alt="琉球御廟ロゴ" />
    </a>
    */ ?>
    <nav class="header__nav" id="header-nav-switcher">
      <ul class="header__nav-list">
        <li class="header__nav-item dropdown">
          <div class="header__nav-link dropdown-toggle">初めての方</div>
          <div class="dropdown-menu">
            <a href="/beauty/" class="dropdown-item">琉球御廟の美</a>
            <a href="/facilities/" class="dropdown-item">琉球御廟の設備</a>
            <a href="/guide/" class="dropdown-item">ご納骨と参拝方法について</a>
            <!-- <a href="/numbers/" class="dropdown-item">数字で見る琉球御廟</a> -->
          </div>
        </li>
        <li class="header__nav-item">
          <a href="/facilities/#access" class="header__nav-link">アクセス</a>
        </li>
        <li class="header__nav-item">
          <a href="/comparison/" class="header__nav-link">費用・個人墓地との比較</a>
        </li>
        <li class="header__nav-item dropdown">
          <div class="header__nav-link dropdown-toggle">ご利用者様の想い</div>
          <div class="dropdown-menu">
            <a href="/feedback-list/" class="dropdown-item">お客様からの声</a>
            <!-- <a href="/letter-to-heaven/" class="dropdown-item">天国への手紙</a>
            <a href="/memorial-service/" class="dropdown-item">法要の様子</a> -->
          </div>
        </li>
        <li class="header__nav-item">
          <a href="/komyoji/" class="header__nav-link">光明寺 琉球識名院について</a>
        </li>
        <li class="header__nav-item">
          <a href="/faq/" class="header__nav-link">よくある質問</a>
        </li>
      </ul>
    </nav>
    <script>
      (function() {
        document.addEventListener('DOMContentLoaded', function() {
          const urlParams = new URLSearchParams(window.location.search); // パラメータを取得
          const headerNav = document.getElementById('header-nav-switcher');

          // ロゴと同じパラメータの有無で判定する
          if (headerNav && urlParams.has('seimeinooka-jyumokusou')) {
            headerNav.remove();
          }
        });
      })();
    </script>
  </header>
  <div class="mobile-menu__container">
    <div class="mobile-menu">
      <?php if (!is_page('yuika')) : ?>
        <a href="/form.php?pid=3" class="mobile-menu__item">
          <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-wheelchair.svg" alt="" />
          <span>見学予約</span>
        </a>
      <?php endif; ?>
      <a href="/form.php" class="mobile-menu__item"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-document.svg" alt="" /><span>資料請求</span>
      </a>
      <!-- <a href="/contact/" class="mobile-menu__item"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-mail.svg" <?php if (is_page('yuika')) : ?> style="width:40px;" <?php endif; ?> alt="" /><span>問合せ</span></a> -->
      <a href="tel:0120-846-940" class="mobile-menu__item"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-phone.svg" alt="" /><span>お電話</span></a>
      <button
        class="mobile-menu__item mobile-menu__item--hamburger"
        id="btn_spmenu">
        <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-menu.svg" alt="" />
        <span>メニュー</span>
      </button>
    </div>
  </div>

  <div class="popup">
    <div class="popup__close">
      <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-close.svg" alt="閉じる" />
      <span>閉じる</span>
    </div>

    <nav class="menu">
      <div class="menu__section">
        <div class="menu__heading">・初めての方</div>
        <ul class="menu__list">
          <li class="menu__item">
            <a href="/beauty/" class="menu__link">琉球御廟の美</a>
          </li>
          <li>
            <a href="/facilities/" class="menu__link">琉球御廟の設備</a>
          </li>
          <li>
            <a href="/guide/" class="menu__link">ご納骨と参拝方法について</a>
          </li>
          <li>
            <!-- <a href="/numbers/" class="menu__link">数字で見る琉球御廟</a> -->
          </li>
        </ul>
      </div>

      <a href="/facilities/#access" class="menu__link">・アクセス</a>

      <a href="/comparison/" class="menu__link">・費用・個人墓地との比較</a>

      <div class="menu__section menu__section--feedback">
        <div class="menu__heading">・ご利用者様の想い</div>
        <ul class="menu__list">
          <li class="menu__item">
            <a href="/feedback-list/" class="menu__link">お客様からの声</a>
          </li>
          <!-- <li>
            <a href="/letter-to-heaven/" class="menu__link">天国への手紙</a>
          </li>
          <li>
            <a href="/memorial-service/" class="menu__link">法要の様子</a>
          </li> -->
        </ul>
      </div>

      <a href="/komyoji/" class="menu__link">・光明寺 琉球識名院について</a>

      <a href="/faq/" class="menu__link">・よくある質問</a>

      <a href="/about/" class="menu__link">・株式会社 霊園・墓石のヤシロについて</a>
    </nav>
  </div>
  <nav class="bottom-menu">
    <ul class="bottom-menu-list">
      <?php if (!is_page('yuika')) : ?>

        <li class="bottom-menu-item">
          <a href="/form.php?pid=3" class="bottom-menu-link">見学予約はこちら</a>
        </li>
      <?php endif; ?>
      <li class="bottom-menu-item">
        <a href="/form.php" class="bottom-menu-link">資料請求はこちら</a>
      </li>
      <!-- <li class="bottom-menu-item">
        <a href="/contact/" class="bottom-menu-link">お問い合わせ</a>
        <a href="/seminar-contact/" class="bottom-menu-link">セミナー予約</a>
      </li> -->
    </ul>
  </nav>