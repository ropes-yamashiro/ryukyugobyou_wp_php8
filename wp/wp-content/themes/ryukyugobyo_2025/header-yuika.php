<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="沖縄初のサークル型樹木葬墓地「結花（ゆいか）」- 糸満 沖縄清明の丘墓地公園。50年管理費不要、永代供養で安心。">
  <title>樹木葬墓地 結花（ゆいか）| 沖縄 糸満 清明の丘墓地公園</title>

  <!-- Preconnect for faster loading -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://i.imgur.com">
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
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/tagmanager.js"></script>
  <?php wp_head(); ?>
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KML53ZC" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- Header / Navigation -->
  <header class="header">
    <nav class="nav container">
      <div class="nav-logo">
        <img src="<?php echo get_template_directory_uri(); ?>/yuika_lp/images/teikinsaku.png" alt="テイキンザクラ" class="nav-logo-icon">
        <span>樹木葬墓地 結花</span>
      </div>
      <ul class="nav-menu" id="navMenu">
        <li><a href="#home" class="nav-link">ホーム</a></li>
        <li><a href="#features" class="nav-link">結花の特徴</a></li>
        <li><a href="#about" class="nav-link">サークル型樹木葬</a></li>
        <li><a href="#yuika-detail" class="nav-link">詳細情報</a></li>
        <li><a href="#pricing" class="nav-link">料金プラン</a></li>
        <li><a href="#flow" class="nav-link">ご契約の流れ</a></li>
        <li><a href="#access" class="nav-link">アクセス</a></li>
        <li><a href="/contact/?seimeinooka-jyumokusou" class="nav-link nav-cta">お問い合わせ</a></li>
      </ul>
      <!-- <button class="nav-toggle" id="navToggle">
        <i class="fas fa-bars"></i>
      </button> -->
    </nav>
  </header>
  <div class="mobile-menu__container">
    <div class="mobile-menu">
      <?php /*
      <a href="/form.php?pid=3" class="mobile-menu__item">
        <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-wheelchair.svg" alt="" />
        <span>見学予約</span>
      </a>
      */ ?>
      <a href="/form.php?seimeinooka-jyumokusou" class="mobile-menu__item"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-document.svg" alt="" /><span>資料請求</span>
      </a>
      <a href="/contact/?seimeinooka-jyumokusou" class="mobile-menu__item"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-mail.svg" <?php if (is_page('yuika')) : ?> style="width:40px;" <?php endif; ?> alt="" /><span>問合せ</span></a>
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
        <ul class="menu__list">
          <li class="menu__section"><a href="#home" class="menu__link menu-item">ホーム</a></li>
          <li class="menu__section"><a href="#features" class="menu__link menu-item">結花の特徴</a></li>
          <li class="menu__section"><a href="#about" class="menu__link menu-item">サークル型樹木葬</a></li>
          <li class="menu__section"><a href="#yuika-detail" class="menu__link menu-item">詳細情報</a></li>
          <li class="menu__section"><a href="#pricing" class="menu__link menu-item">料金プラン</a></li>
          <li class="menu__section"><a href="#flow" class="menu__link menu-item">ご契約の流れ</a></li>
          <li class="menu__section"><a href="#access" class="menu__link menu-item">アクセス</a></li>
        </ul>
      </div>
    </nav>
  </div>

  <button class="scroll-top visible" id="scrollTop" aria-label="ページトップへ戻る">
    <i class="fas fa-arrow-up"></i>
  </button>

  <nav class="bottom-menu">
    <ul class="bottom-menu-list">
      <?php if (!is_page('yuika')) : ?>
        <li class="bottom-menu-item">
          <a href="/form.php?pid=3" class="bottom-menu-link">見学予約はこちら</a>
        </li>
      <?php endif; ?>
      <li class="bottom-menu-item">
        <a href="/form.php?seimeinooka-jyumokusou" class="bottom-menu-link">資料請求はこちら</a>
      </li>
      <li class="bottom-menu-item">
        <a href="/contact/?seimeinooka-jyumokusou" class="bottom-menu-link">お問い合わせ</a>
        <a href="/seminar-contact/" class="bottom-menu-link">セミナー予約</a>
      </li>
    </ul>
  </nav>