<?php get_header(); ?>

<?php
$args = array(
  'post_type' => 'top_notices',/* カスタム投稿名 */
  'posts_per_page' => 1,/* 表示する数 */
  'paged' => $paged,
  'paged' => get_query_var('paged'),
  'orderby'        => 'date',   // ★ 追加：投稿日でソート
  'order'          => 'DESC'
);
?>
<?php
$my_query = new WP_Query($args); ?>
<?php if ($my_query->have_posts()) : // 投稿がある場合 
?>

<div id="c-modal_bg"></div>
<div id="c-modal">
    <div class="notice">
    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
		<h2>重要なお知らせ<br><span class="notice_date"><?php echo get_the_date('Y.m.d'); ?></span></h2>
		<p class="notice_txt"><?php the_title(); ?></p>
	</div>
    <div id="c-modal_close">閉じる</div>
</div>
<script>
	// モーダルの処理
	var bg = document.getElementById('c-modal_bg');
	bg.addEventListener('click', modal_close, false);

	var close = document.getElementById('c-modal_close');
	close.addEventListener('click', modal_close, false);

	function modal_close() {
		var modal = document.getElementById('c-modal');
		modal.style.display = 'none';
		bg.style.display = 'none';
	}
</script>
<?php endwhile; endif; wp_reset_postdata(); ?>

<main>
  <div class="fv__loader" id="videoLoader">
    <img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/logo.svg" alt="" class="fv__loader-image" />
  </div>

  <section class="fv fadeIn">
    <div class="fv__wrapper">
      <video
        src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/fv_02.mp4"
        class="fv__video"
        autoplay
        muted
        loop
        playsinline
        preload="auto"
        id="mainVideo"></video>
    </div>
    <span class="fv__caption"> ※琉球御廟は琉球識名院内にあります </span>

    <h1 class="fv__hero">
      伝統の美と<br class="sp" />革新の設備を融合させた<br />
      全く新しい永代供養・室内墓所
    </h1>
  </section>
  <section class="intro fadeIn">
    <div class="intro__gallery swiper" id="introGallery">
      <div class="intro__gallery-list swiper-wrapper">
        <div
          class="intro__gallery-item intro__gallery-item--purple swiper-slide">
          <a href="/wp/wp-content/themes/ryukyugobyo_2025/images/top/260602_ryukyugobyou_flyer.pdf" target="_blank" class="intro__gallery-link">
            <!-- <div class="intro__gallery-tag">キャンペーン</div> -->
            <div class="intro__gallery-title">
              最新の<br />
              チラシ
            </div>
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/kyuubon-small.png"
              alt="供養の窓口"
              class="intro__gallery-image" />
            <!-- <div class="intro__gallery-features">
              <div class="intro__gallery-feature">
                先着
                <strong>30組</strong>
                限定
              </div>
              <div class="intro__gallery-feature">特典は<br />今だけ</div>
              <div class="intro__gallery-feature">
                最大
                <strong>5万円</strong>
                クーポン
              </div>
            </div> -->
            詳細はコチラ
          </a>
        </div>

        <div
          class="intro__gallery-item intro__gallery-item--blue swiper-slide">
          <a href="/events-event/" class="intro__gallery-link">
            <!-- <div class="intro__gallery-tag">キャンペーン</div> -->
            <div class="intro__gallery-title">
              琉球識名院の<br>イベント
            </div>
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/temple-small.png"
              alt="建物画像"
              class="intro__gallery-image" />
            詳細はコチラ
          </a>
        </div>

        <div
          class="intro__gallery-item intro__gallery-item--green swiper-slide">
          <a href="/seminar-contact/" class="intro__gallery-link">
            <!-- <div class="intro__gallery-tag">ご案内</div> -->
            <div class="intro__gallery-title">
              琉球識名院お寺体験会<br>(参加無料)
            </div>
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/seminar-small.png"
              alt="建物画像"
              class="intro__gallery-image" />
            <!-- <div class="intro__gallery-features">
            <div class="intro__gallery-feature">
              <strong>5/15(木)</strong>
              10:00～
            </div>
            <div class="intro__gallery-feature">
              <strong>5/24(土)</strong>
              10:00～
            </div>
          </div> -->
            詳細はコチラ
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="news fadeIn">
    <div class="news__wrapper">
      <h2 class="top-title news__title">新着情報</h2>

      <ul class="news__list">
                                              <!-- ▽ ループ開始 ▽ -->
            <li class="news__item">
                                <a href="https://ryukyugobyou.okinawa/news/2026%e5%b9%b46%e6%9c%8826%e6%97%a5%ef%bc%88%e9%87%91%ef%bc%89%e3%81%af%e5%8f%b0%e9%a2%a8%e3%81%ae%e7%82%ba%e3%80%81%e4%bc%91%e9%a4%a8%e3%81%a8%e3%81%95%e3%81%9b%e3%81%a6%e9%a0%82%e3%81%8d%e3%81%be/" class="news__content">
                                    <time class="news__date">2026.06.25</time>
                  <p class="news__text body-copy">
                    2026年6月26日（金）は台風の為、休館とさせて頂きます。                  </p>
                  </a>
            </li>
            <!-- △ ループ終了 △ -->
                      <!-- ▽ ループ開始 ▽ -->
            <li class="news__item">
                                <a href="https://ryukyugobyou.okinawa/news/32%e4%b8%87%e3%81%ae%e3%81%8a%e8%a9%b1%ef%bc%81%ef%bc%81/" class="news__content">
                                    <time class="news__date">2026.06.08</time>
                  <p class="news__text body-copy">
                    32万のお話！！                  </p>
                  </a>
            </li>
            <!-- △ ループ終了 △ -->
                      <!-- ▽ ループ開始 ▽ -->
            <li class="news__item">
                                <a href="https://ryukyugobyou.okinawa/news/6%e6%9c%88%ef%bc%91%e6%97%a5%ef%bc%88%e6%9c%88%ef%bc%89%e5%8f%b0%e9%a2%a8%e3%81%ab%e3%82%88%e3%82%8a%e4%bc%91%e9%a4%a8%e3%81%a8%e3%81%95%e3%81%9b%e3%81%a6%e9%a0%82%e3%81%8d%e3%81%be%e3%81%99%e3%80%82/" class="news__content">
                                    <time class="news__date">2026.05.31</time>
                  <p class="news__text body-copy">
                    2026年6月１日（月）台風により休館とさせて頂きます。                  </p>
                  </a>
            </li>
            <!-- △ ループ終了 △ -->
                        </ul>
    </div>

    <a href="/news-list/" class="news__button link-button"><span>一覧を見る</span></a>
  </section>

  <section class="about fadeIn">
    <p class="about__text">
      沖縄最大級の木造建築寺院<br class="sp" />琉球識名院<br />
      その敷地内に設立された<br class="sp" />琉球御廟が提供する<br />
      伝統の美と最新の技術を融合させた<br class="sp" />新しい和らぎの場
    </p>
    <div class="about__buttons">
      <a href="/beauty/" class="about__button link-button"><span>琉球御廟の美へ</span></a>
      <a href="/facilities/" class="about__button link-button"><span>琉球御廟の設備へ</span></a>
    </div>
  </section>

  <section class="service fadeIn">
    <h2 class="top-title top-title--spaced">
      時代に合わせた<br class="sp" />供養の新しいカタチ<br />
      琉球御廟とは
    </h2>
    <!-- <div class="youtube__container">
      <iframe width="100%" height="100%" src="https://www.youtube.com/embed/JDouokaMfyA?si=tZrxg8LssAqUEG44" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <video
      src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/01_opning.mp4"
      controls
      class="service__video fadeIn"
      playsinline
      poster="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/opening-screenshot.webp"></video>
    <span class="service__video-caption">※琉球御廟は琉球識名院内にあります</span>
    <div class="youtube__container">
      <iframe width="100%" height="100%" src="https://www.youtube.com/embed/sPCFmJ_LmE4?si=fzA5-723P_5yU8iZ&rel=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div> -->
	  
	<div class="video__wrap">
		<ul>
			<li>
				<iframe width="100%" height="100%" src="https://www.youtube.com/embed/JDouokaMfyA?si=tZrxg8LssAqUEG44" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
			</li>
			<li>
			  <video
				  src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/01_opning.mp4"
				  controls
				  class="fadeIn"
				  playsinline
				  poster="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/opening-screenshot.webp">
			  </video>
			</li>
			<li>
				<iframe width="100%" height="100%" src="https://www.youtube.com/embed/sPCFmJ_LmE4?si=fzA5-723P_5yU8iZ&rel=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" class="fadeIn" allowfullscreen></iframe>
			</li>
		</ul>
		<p class="video-caption">※琉球御廟は琉球識名院内にあります</p>
	</div>

    <div class="reasons fadeIn">
      <h2 class="top-title top-title--spaced">琉球御廟が選ばれる理由</h2>
      <div class="reasons__wrapper">
        <div class="reason">
          <div class="reason__image-wrapper">
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/reason-1.webp"
              alt=""
              class="reason__image" />
          </div>

          <div class="reason__content">
            <h3 class="reason__title top-subtitle">
              伝統的の美で造られた施設は<br />
              常に清潔にされた心休まる<br />
              憩いの空間
            </h3>
            <p class="reason__text body-copy">
              琉球様式と和の伝統技術が調和された施設は、木の香りと柔らかな光に包まれた心安らぐ静寂のひとときをお過ごしいただけます。
            </p>
            <a href="/beauty/" class="reason__button link-button"><span>琉球御廟の美へ</span></a>
          </div>
        </div>
        <div class="reason fadeIn">
          <div class="reason__image-wrapper">
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/reason-2_02.png"
              alt=""
              class="reason__image" />
          </div>

          <div class="reason__content">
            <h3 class="reason__title top-subtitle">
              新しい供養のカタチである<br />
              自動搬送式納骨堂を導入した<br />
              県内初の「室内墓所」
            </h3>
            <p class="reason__text body-copy">
              毎日の清掃やお花のお供えなど、心のこもった管理を徹底しており、
              いつ訪れても清潔な空間でお参りすることが出来ます。
            </p>
            <a href="/facilities/" class="reason__button link-button"><span>自動搬送式納骨堂とは</span></a>
          </div>
        </div>
        <div class="reason fadeIn">
          <div class="reason__image-wrapper">
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/reason-3.webp"
              alt=""
              class="reason__image" />
          </div>

          <div class="reason__content">
            <h3 class="reason__title top-subtitle">
              アクセスしやすい<br />
              世界遺産 識名園の正面に位置する<br />
              琉球識名院・琉球御廟
            </h3>
            <p class="reason__text body-copy">
              那覇市中心から車で15分、那覇インターから車で5分と、多方面からアクセスしやすく、施設内には駐車場もございます。
            </p>
            <a
              href="/facilities/#access"
              class="reason__button link-button"><span>琉球御廟へのアクセス</span></a>
          </div>
        </div>
        <div class="reason fadeIn">
          <div class="reason__image-wrapper">
            <img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/reason-4_02.webp"
              alt=""
              class="reason__image" />
          </div>

          <div class="reason__content">
            <h3 class="reason__title top-subtitle">
              子ども・孫世代にも安心な<br />
              料金設定とサービス
            </h3>
            <p class="reason__text body-copy">
              納骨数に応じた初期費用に掛かる費用と維持管理費だけの、シンプルな費用設計です。琉球御廟と個人墓地の特徴を分かりやすく比較した表もご案内しています。
            </p>
            <a href="/comparison/" class="reason__button link-button"><span>費用について</span></a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="difference fadeIn">
    <div class="difference__images">
      <div class="difference__image">
        <img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/difference-1.webp" alt="" />
      </div>
      <div class="difference__image">
        <img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/difference-2.webp" alt="" />
      </div>
    </div>
    <div class="difference__content">
      <h2 class="top-title">通常のお墓と琉球御廟の違い</h2>
      <p class="difference__text body-copy">
        お墓を購入する動機やお悩みは様々です。<br />
        お墓をご検討されている方が<br />
        最も気になるポイントをピックアップし、<br />
        琉球御廟と個人墓地の特徴を<br
          class="sp" />わかりやすく比較しました。
      </p>
      <a
        href="/comparison/#comparison"
        class="difference__button link-button"><span>個人墓地との比較</span></a>
    </div>
  </section>

  <section class="ritual fadeIn">
    <div class="ritual__wrapper">
      <img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/interment_02.png" alt="" class="ritual__image" />
      <div class="ritual__content">
        <h2 class="ritual__title top-title">ご納骨と参拝方法について</h2>
        <p class="ritual__text body-copy">
          ご遺骨は専用袋に納め、金属製のお厨子へ安置いたします。カード式参拝システムは、手ぶらで気軽にお越しいただける安心の室内墓所です。
        </p>
        <a href="/guide/" class="ritual__button link-button"><span>ご納骨と参拝方法について</span></a>
      </div>
    </div>
  </section>

  <section class="feedback fadeIn">
    <h2 class="top-title">ご利用者様の想い</h2>
    <p class="feedback__text body-copy body-copy--spaced">
      お客様によっても供養に関する<br
        class="sp" />悩み・課題・想いは様々です。<br />
      琉球御廟を実際にご利用いただいている<br class="sp" />
      お客様の声や様子をご覧いただくことで、<br class="sp" />
      ご安心いただけるかもしれません。<br />ぜひ悩み解消のヒントにしてください。
    </p>

    <div class="feedback__wrapper">
      <div class="feedback__item">
        <img
          src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/top/feedback-1.webp"
          alt=""
          class="feedback__image" />
        <a href="/feedback-list/" class="feedback__button link-button"><span>お客様からの声を見る</span></a>
      </div>
          </div>
  </section>
  <!-- <section class="numbers fadeIn">
    <h2 class="numbers__title top-title">数字で見る琉球御廟</h2>
    <p class="numbers__text body-copy body-copy--spaced">
      お墓選びの参考になるよう、<br />
      現在ご利用いただいているお客様の詳細を数値化してまとめております。
    </p>
    <div class="numbers__wrapper">
      <div class="numbers__links pc">
        <a href="/faq/" class="numbers__link">琉球御廟を選んだ理由は？</a>
        <a href="/faq/" class="numbers__link">どのくらいの人が利用している？</a>
        <a href="/faq/" class="numbers__link">スタッフの対応は？</a>
      </div>
      <a href="/numbers/" class="numbers__button link-button"><span>数字で見るご利用者様</span></a>
      <div class="numbers__links">
        <a href="/faq/" class="numbers__link">琉球御廟を選んだ理由は？</a>
        <a href="/faq/" class="numbers__link">どのくらいの人が利用している？</a>
        <a href="/faq/" class="numbers__link">スタッフの対応は？</a>
        <a href="/faq/" class="numbers__link">利用者の満足度は？</a>
        <a href="/faq/" class="numbers__link">どんな検討をしている？</a>
      </div>
    </div>
  </section> -->
  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
    </ul>
  </div>
</main>
<footer class="footer">
  <div class="footer__banner">
    <a href="/seimeinooka/yuika/"><img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/yuika-bnr-sp.jpg" alt="" class="switch" /></a>
  </div>
  <div class="footer__content">
    <div class="footer__left">
      <img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/logo.svg" alt="" class="footer__logo" />

      <div class="footer__contact">
        〒902-0072<br />
        沖縄県那覇市真地2−1(琉球識名院に併設)<br />
        開館時間　9:15 ～ 17:00(水曜定休)
      </div>

      <img
        src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/logo-2.svg"
        class="footer__logo-yashiro"
        alt="" />

      <div class="footer__contact">
        〒901-1117<br />
        沖縄県島尻郡南風原町字津嘉山1593 SKビル202<br />
        開館時間　9:30～17:30(水曜定休)
      </div>

      <ul class="footer__social">
        <li>
          <a href="https://www.youtube.com/channel/UCun6NWS4JQEBe-1H-tb9Guw" class="footer__social-link" target="_blank"><img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/icon-youtube.svg"
              alt="ユーチューブアイコン" /></a>
        </li>
        <li>
          <a href="https://page.line.me/523gnuoa?openQrModal=true" class="footer__social-link" target="_blank"><img src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/icon-line.svg" alt="ラインアイコン" /></a>
        </li>
        <li>
          <a href="https://www.facebook.com/people/%E5%AE%A4%E5%86%85%E5%A2%93%E6%89%80%E7%90%89%E7%90%83%E5%BE%A1%E5%BB%9F/pfbid0BHDAVHJeJkQwkxPKw1vjtDgRAyy7CaZdGfAJxtNTmmM6SP4YK5FShQqc2cMCa1nGl/" class="footer__social-link" target="_blank"><img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/icon-facebook.svg"
              alt="フェイスブックアイコン" /></a>
        </li>
        <li>
          <a href="https://www.instagram.com/accounts/login/?next=%2Fryukyu_gobyou%2F" class="footer__social-link" target="_blank"><img
              src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/images/common/icon-instagram.svg"
              alt="インスタグラムアイコン" /></a>
        </li>
      </ul>
    </div>

    <div class="footer__right">
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
              <a href="/comparison/" class="menu__link">費用・個人墓地との比較</a>
            </li>
            <li>
              <a href="/guide/" class="menu__link">ご納骨と参拝方法について</a>
            </li>
            <li>
              <!-- <a href="/numbers/" class="menu__link">数字で見る琉球御廟</a> -->
            </li>
          </ul>
        </div>
        <div class="menu__section-wrapper">
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
              </li>
              -->
            </ul>
          </div>
          <a href="/komyoji/" class="menu__link">・光明寺 琉球識名院について</a>
          <a href="/faq/" class="menu__link">・よくある質問</a>
        </div>

        <div class="menu__section-wrapper">
          <a href="/news-list/" class="menu__link">・新着情報</a>

          <a href="/events-list/" class="menu__link">・キャンペーン・イベント</a>

          <a href="/about/" class="menu__link menu__link--about"><span>・</span><span>株式会社 霊園・墓石のヤシロについて</span></a>
          <a href="/privacy-policy/" class="menu__link">・プライバシーポリシー</a>
        </div>
      </nav>
    </div>
  </div>

  <div class="footer__copyright">
    <p>©2025 RYUKYUGOBYO</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/js/common.js"></script>
  <script src="https://ryukyugobyou.okinawa/wp/wp-content/themes/ryukyugobyo_2025/js/top.js"></script>
<script type='text/javascript' src='https://ryukyugobyou.okinawa/wp/wp-includes/js/wp-embed.min.js?ver=5.5.18' id='wp-embed-js'></script>
</body>

</html>