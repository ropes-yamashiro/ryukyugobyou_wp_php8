<footer class="footer">
  <div class="footer__banner">
    <a href="/seimeinooka/yuika/"><img src="<?php echo get_template_directory_uri(); ?>/images/common/yuika-bnr-sp.jpg" alt="" class="switch" /></a>
  </div>
  <div class="footer__content">
    <div class="footer__left">
      <img src="<?php echo get_template_directory_uri(); ?>/images/common/logo.svg" alt="" class="footer__logo" />

      <div class="footer__contact">
        〒902-0072<br />
        沖縄県那覇市真地2−1(琉球識名院に併設)<br />
        開館時間　9:15 ～ 17:00(水曜定休)
      </div>

      <img
        src="<?php echo get_template_directory_uri(); ?>/images/common/logo-2.svg"
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
              src="<?php echo get_template_directory_uri(); ?>/images/common/icon-youtube.svg"
              alt="ユーチューブアイコン" /></a>
        </li>
        <li>
          <a href="https://page.line.me/523gnuoa?openQrModal=true" class="footer__social-link" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-line.svg" alt="ラインアイコン" /></a>
        </li>
        <li>
          <a href="https://www.facebook.com/people/%E5%AE%A4%E5%86%85%E5%A2%93%E6%89%80%E7%90%89%E7%90%83%E5%BE%A1%E5%BB%9F/pfbid0BHDAVHJeJkQwkxPKw1vjtDgRAyy7CaZdGfAJxtNTmmM6SP4YK5FShQqc2cMCa1nGl/" class="footer__social-link" target="_blank"><img
              src="<?php echo get_template_directory_uri(); ?>/images/common/icon-facebook.svg"
              alt="フェイスブックアイコン" /></a>
        </li>
        <li>
          <a href="https://www.instagram.com/accounts/login/?next=%2Fryukyu_gobyou%2F" class="footer__social-link" target="_blank"><img
              src="<?php echo get_template_directory_uri(); ?>/images/common/icon-instagram.svg"
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
<script src="<?php echo get_template_directory_uri(); ?>/js/common.js"></script>
<?php if (is_home() || is_front_page()) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/top.js"></script>
<?php elseif (is_page('yuika')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/yuika_lp/js/main.js?v=20260312b"></script>
<?php elseif (is_page('beauty')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/beauty.js"></script>
<?php elseif (is_page('contact')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/contact.js"></script>
<?php elseif (is_page('seminar-contact')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/contact.js"></script>
<?php elseif (is_page('facilities')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/facilities.js"></script>
<?php elseif (is_page('guide')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/guide.js"></script>
<?php elseif (is_page('numbers')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/numbers.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/pie.js"></script>
<?php elseif (is_page('comparison')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/facilities.js"></script>
<?php elseif (is_page(array('feedback-list', 'letter-to-heaven', 'memorial-service'))) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/filter.js"></script>
<?php elseif (is_page('komyoji')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/komyoji.js"></script>
<?php elseif (is_page('faq')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/faq.js"></script>
<?php elseif (is_page('privacy-policy')) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/faq.js"></script>
<?php elseif (is_page(array('news-list', 'news-closed', 'news-others', 'news-commercial'))) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/filter.js"></script>
<?php elseif (is_page(array('events-list', 'events-event', 'events-campaign'))) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/filter.js"></script>
<?php elseif (is_singular(array('news', 'event', 'feedback'))) : ?>
  <script src="<?php echo get_template_directory_uri(); ?>/js/selector.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/js/filter.js"></script>
<?php endif; ?>
<?php wp_footer(); ?>
</body>

</html>