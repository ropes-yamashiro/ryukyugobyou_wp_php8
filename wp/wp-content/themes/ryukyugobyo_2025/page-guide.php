<?php get_header(); ?>

<main>
  <div class="selector fadeIn">
    <h1 class="beauty__title page-title">
      ご納骨と<br class="sp" />参拝方法について
    </h1>
    <nav class="navigation">
      <div class="mobile-dropdown">
        <div class="dropdown-header">
          <span class="dropdown-title">ご納骨と参拝方法について</span>
        </div>
        <ul class="dropdown-list">
          <li>
            <a href="/beauty/" class="nav-item">琉球御廟の美</a>
          </li>
          <li>
            <a href="/facilities/" class="nav-item">琉球御廟の設備</a>
          </li>
          <li>
            <a href="/comparison/" class="nav-item">費用・個人墓地との比較</a>
          </li>
          <li>
            <span class="nav-item active">ご納骨と参拝方法について</span>
          </li>
          <li>
            <!-- <a href="/numbers/" class="nav-item">数字で見る琉球御廟</a> -->
          </li>
          <li>
            <a href="/komyoji/" class="nav-item">光明寺 琉球識名院について</a>
          </li>
          <li><a href="/faq/" class="nav-item">よくある質問</a></li>
        </ul>
      </div>
    </nav>
  </div>

  <section class="about fadeIn">
    <div class="about__wrapper">
      <div class="about__item">
        <video
          src="<?php echo get_template_directory_uri(); ?>/images/guide/05_syuzouhouhou.mp4"
          controls
          class="about__item-video"
          poster="<?php echo get_template_directory_uri(); ?>/images/guide/video-1-screenshot.webp"></video>
        <div class="about__item-content">
          <h2 class="section-title about__item-title">納骨について</h2>
          <p class="body-copy about__item-text">
            ご遺骨は専用の納骨袋に移し替え、お厨子と呼ばれる金属製の納骨箱にお納めいたします。
          </p>
        </div>
      </div>
      <div class="about__item fadeIn">
        <video
          src="<?php echo get_template_directory_uri(); ?>/images/guide/04_sanpaihouhou.mp4"
          controls
          class="about__item-video"
          poster="<?php echo get_template_directory_uri(); ?>/images/guide/video-2-screenshot.webp"></video>
        <div class="about__item-content">
          <h2 class="section-title about__item-title">参拝方法について</h2>
          <p class="body-copy about__item-text">
            琉球御廟では、カード式参拝システムを採用しています。これは格式を大切にしながらスマートさを兼ねそなえた、
            新しいお参りのカタチです。
            従来のお墓でも、納骨堂でもない、安心の室内墓所です。
          </p>
        </div>
      </div>
    </div>
  </section>

  <div class="tejun fadeIn">
    <h2 class="section-title tejun__title">ご利用手順</h2>
    <div class="swiper tejun__swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-1_02.png"
            alt=""
            class="tejun__slide-image" />
          <h3 class="tejun__slide-title">①カードリーダーで受付</h3>
          <p class="tejun__slide-desc">
            1階受付カウンターでカードリーダーに専用カードをかざし、参拝室に向かいます。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-2.webp"
            alt=""
            class="tejun__slide-image" />
          <h3 class="tejun__slide-title">②12の参拝室から自由に選択</h3>
          <p class="tejun__slide-desc">
            参拝室は１階と２階の２フロア。12の参拝室から自由に選んでいただけます。カードをお持ちでない方は、参拝申請（受付）にて、参拝の受付が可能です
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-3.webp"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title">
            ③その間に、<br />
            　厨子と銘板が自動搬送
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-4.webp"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ④手ぶらでお越しになっても大丈夫
          </h3>
          <p class="tejun__slide-desc">
            供花やお焼香などの準備、また日々のお墓のお手入れはスタッフが行います。お天気に左右されませんので、いつでもお墓参りにお越しいただけます。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-5_02.png"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ⑤気兼ねなく、ゆっくり参拝
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>
        <div class="swiper-slide tejun__slide" style="opacity: 0;">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-5_02.png"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ⑤気兼ねなく、ゆっくり参拝
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>


        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-1_02.png"
            alt=""
            class="tejun__slide-image" />
          <h3 class="tejun__slide-title">①カードリーダーで受付</h3>
          <p class="tejun__slide-desc">
            1階受付カウンターでカードリーダーに専用カードをかざし、参拝室に向かいます。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-2.webp"
            alt=""
            class="tejun__slide-image" />
          <h3 class="tejun__slide-title">②12の参拝室から自由に選択</h3>
          <p class="tejun__slide-desc">
            参拝室は１階と２階の２フロア。12の参拝室から自由に選んでいただけます。カードをお持ちでない方は、参拝申請（受付）にて、参拝の受付が可能です
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-3.webp"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title">
            ③その間に、<br />
            　厨子と銘板が自動搬送
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-4.webp"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ④手ぶらでお越しになっても大丈夫
          </h3>
          <p class="tejun__slide-desc">
            供花やお焼香などの準備、また日々のお墓のお手入れはスタッフが行います。お天気に左右されませんので、いつでもお墓参りにお越しいただけます。
          </p>
        </div>
        <div class="swiper-slide tejun__slide">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-5_02.png"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ⑤気兼ねなく、ゆっくり参拝
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>
        <div class="swiper-slide tejun__slide" style="opacity: 0;">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/guide/tejun-5_02.png"
            alt=""
            class="tejun__slide-image tejun__slide-image--small" />
          <h3 class="tejun__slide-title tejun__slide-title--narrow">
            ⑤気兼ねなく、ゆっくり参拝
          </h3>
          <p class="tejun__slide-desc">
            参拝室へ向かう間に、故人のお骨を納めた「厨子」と家名等を刻んだ「銘板」が自動で搬送されます。墓前では故人の在りし日の姿をデジタルフォトフレームに映し出すことも。
          </p>
        </div>
      </div>
    </div>
    <div class="tejun__buttons">
      <div class="tejun__button tejun__button--prev"></div>
      <div class="tejun__button tejun__button--next"></div>
    </div>
  </div>

  <section class="contact-section fadeIn">
    <h2 class="section-title">お問い合わせ</h2>
    <p class="body-copy body-copy--narrow contact-section__text">
      問い合わせフォームまたはお電話にてお気軽にお問合せください。
    </p>
    <div class="contact-section__buttons">
      <!-- <a href="/contact-before/" class="contact-section__button link-button"><span>お問合わせフォーム</span></a> -->
      <div class="contact-section__button-wrapper">
        <a
          href="tel:0120-846-940"
          class="contact-section__button link-button"><span>0120-846-940</span></a>
        <span class="contact-section__button-desc">9:15~17:00（水曜定休）</span>
      </div>
    </div>
  </section>

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item">ご納骨と参拝方法について</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>