<?php get_header(); ?>
<main>
  <div class="selector fadeIn">
    <h1 class="beauty__title page-title">琉球御廟の美</h1>
    <nav class="navigation">
      <div class="mobile-dropdown">
        <div class="dropdown-header">
          <span class="dropdown-title">琉球御廟の美</span>
        </div>
        <ul class="dropdown-list">
          <li>
            <span class="nav-item active">琉球御廟の美</span>
          </li>
          <li>
            <a href="/facilities/" class="nav-item">琉球御廟の設備</a>
          </li>
          <li>
            <a href="/comparison/" class="nav-item">費用・個人墓地との比較</a>
          </li>
          <li>
            <a href="/guide/" class="nav-item">ご納骨と参拝方法について</a>
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
  <section class="beauty-intro fadeIn">
    <picture>
      <source
        srcset="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-1920.webp"
        media="(min-width: 1367px)"
        type="image/webp" />
      <source
        srcset="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-1366.webp"
        media="(min-width: 768px)"
        type="image/webp" />
      <img
        src="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-767.webp"
        class="beauty-intro__image" />
    </picture>
    <h2 class="block-title beuaty-intro__title">
      琉球王朝ゆかりの地、<br class="sp" />那覇市識名に建設された <br />
      県内最大級の木造寺院<br /><span class="pc">琉球御廟はその施設内に建設されています。</span>
    </h2>
  </section>
  <section class="exterior fadeIn">
    <div class="exterior-decor-1 faden"></div>
    <div class="exterior-decor-2 fadeIn"></div>
    <div class="exterior-decor-3 fadeIn"></div>
    <div class="exterior__wrapper">
      <div class="exterior-item">
        <div class="exterior-item__image">
          <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/exterior-1.webp" alt="正門" />
        </div>
        <div class="exterior-item__content">
          <h2 class="block-title exterior-item__title">山門</h2>
          <p class="exterior-item__desc body-copy">
            山門は釘を使わず組み上げられている高さ6mの木造建造物で、基本の本柱を4つの脚柱で支える構造の「四脚門（よつあしもん）」です。
            格子扉からは、閉門時にも寺内の様子をうかがうことができます。無垢の木材の表面には瀟洒な彫刻が施され、屋根の琉球赤瓦の軒巴（のきともえ）は光明寺紋型で製作(本堂屋根・鬼瓦にも採用)されています。
          </p>
        </div>
      </div>
      <div class="exterior-item fadeIn">
        <div class="exterior-item__image">
          <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/exterior-3.webp" alt="幻想的な風景" />
        </div>
        <div class="exterior-item__content">
          <h2 class="block-title exterior-item__title">
            水鏡に映し出される<br />
            幻想的な風景
          </h2>
          <p class="exterior-item__desc body-copy">
            山門をくぐると幅6m長さ25mの水盤に、青い空と黄金の鴟尾、赤瓦の大屋根が映りこみます。古来より、水が豊かな地域であったとの伝聞から、敷地に昔からある井戸を調査したところ、豊かな水源であることが判りました。
          </p>
        </div>
      </div>
      <div class="exterior-item fadeIn">
        <div class="exterior-item__image">
          <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/exterior-4.webp" alt="合同参拝所" />
        </div>
        <div class="exterior-item__content">
          <h2 class="block-title exterior-item__title">合同参拝所</h2>
          <p class="exterior-item__desc body-copy">
            お墓が引き継げなくなった場合でも、敷地内で永代供養いたします。
          </p>
        </div>
      </div>
      <div class="exterior-item fadeIn">
        <div class="exterior-item__image">
          <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/beauty__ihai.jpg" alt="位牌供養碑" />
        </div>
        <div class="exterior-item__content">
          <h2 class="block-title exterior-item__title">位牌供養碑</h2>
          <p class="exterior-item__desc body-copy">
            引き継ぐことが出来なくなったお位牌（トートーメー）も琉球識名院で供養させていただきます。
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="harmony fadeIn">
    <picture>
      <source
        srcset="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-2-1920.webp"
        media="(min-width: 1367px)"
        type="image/webp" />
      <source
        srcset="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-2-1366.webp"
        media="(min-width: 684px)"
        type="image/webp" />
      <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/temple-2-767.webp" class="harmony__image" />
    </picture>
    <div class="harmony__wrapper">
      <h2 class="harmony__title page-title">
        琉球様式と<br />
        和の伝統技術の調和
      </h2>
      <p class="harmony__text body-copy">
        本堂には、奈良時代の建築様式を取り入れ、<br
          class="pc" />沖縄の青空に映える赤瓦の大屋根の大棟には、<br
          class="pc" />この寺院を護る鴟尾(しび)が金色に輝いています。
      </p>
    </div>
  </section>

  <section class="roof fadeIn">
    <div class="roof__wrapper">
      <div class="roof__images">
        <img src="<?php echo get_template_directory_uri(); ?>/images/beauty/roof-1.webp" alt="" class="roof__image" />
        <div class="roof__images-wrapper">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/roof-2.webp"
            alt=""
            class="roof__image roof__image--small" />
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/roof-3.webp"
            alt=""
            class="roof__image roof__image--pc" />
        </div>
      </div>
      <div class="roof__content">
        <h3 class="block-title roof__title">良木が織りなす造形美</h3>
        <p class="roof__text body-copy">
          樹齢1,000年を超えるカナダ産檜の大柱で支えられた本堂は、現代の木造建築では類を見ない、梁や内柱のない天井垂木構造を持つ重厚で美しい寺院建築となっています。
        </p>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/beauty/roof-3.webp"
          alt=""
          class="roof__last-image" />
      </div>
    </div>
  </section>

  <section class="gallery fadeIn">
    <h2 class="page-title">その他の琉球御廟の環境</h2>
    <div class="gallery__wrapper swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-1.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">法要室</h3>
          <p class="gallery-item__text body-copy">
            祭壇を完備した法要室は、約30名様まで収容可能です。納骨の際の法要はもとより3回忌、7回忌などの年次法要、さらには少人数での家族葬にもご活用いただけます。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-3.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">ロビーラウンジ</h3>
          <p class="gallery-item__text body-copy">
            木のぬくもりを感じる温かく明るいロビーラウンジや沖縄の木々を配した庭園で故人さまとの語らいなど、ゆったりとした時間をお過ごしいただけます。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-4.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">庭園</h3>
          <p class="gallery-item__text body-copy">
            山門をくぐると、琉球識名院までの道は緑豊かな庭園が広がります。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-5.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">バリアフリー</h3>
          <p class="gallery-item__text body-copy">
            施設内はエレベーター設備など、バリアフリーを完備しております。
            小さなお子さま連れのご家族やお年寄りの方まで、安心してご参拝いただけます。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-1.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">法要室</h3>
          <p class="gallery-item__text body-copy">
            祭壇を完備した法要室は、約30名様まで収容可能です。納骨の際の法要はもとより3回忌、7回忌などの年次法要、さらには少人数での家族葬にもご活用いただけます。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-3.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">ロビーラウンジ</h3>
          <p class="gallery-item__text body-copy">
            木のぬくもりを感じる温かく明るいロビーラウンジや沖縄の木々を配した庭園で故人さまとの語らいなど、ゆったりとした時間をお過ごしいただけます。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-4.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">庭園</h3>
          <p class="gallery-item__text body-copy">
            山門をくぐると、琉球識名院までの道は緑豊かな庭園が広がります。
          </p>
        </div>
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/gallery-5.webp"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">バリアフリー</h3>
          <p class="gallery-item__text body-copy">
            施設内はエレベーター設備など、バリアフリーを完備しております。
            小さなお子さま連れのご家族やお年寄りの方まで、安心してご参拝いただけます。
          </p>
        </div>
        <?php /*
        <div class="swiper-slide gallery-item">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/beauty/beauty__toilet.jpg"
            alt=""
            class="gallery-item__image" />
          <h3 class="gallery-item__title">お手洗い</h3>
          <p class="gallery-item__text body-copy">
            室内には清潔で快適なお手洗いをご用意しております。
            お子さまからご年配の方まで安心してご利用いただけます。
          </p>
        </div>
        */ ?>
      </div>
      <div class="gallery__buttons">
        <div class="gallery__button gallery__button--prev"></div>
        <div class="gallery__button gallery__button--next"></div>
      </div>
    </div>
  </section>

  <section class="contact-section fadeIn">
    <h2 class="page-title">ご見学について</h2>
    <p class="body-copy body-copy--narrow contact-section__text">
      琉球御廟ご見学の際は、お電話・見学予約フォームで事前予約ください。
    </p>
    <div class="contact-section__buttons">
      <a
        href="/form.php?pid=3"
        class="contact-section__button link-button"><span>見学予約フォーム</span></a>
      <div class="contact-section__button-wrapper">
        <a
          href="tel:0120-846-940"
          class="contact-section__button link-button"><span>0120-846-940</span></a>
        <span class="contact-section__button-desc">9:15~17:00（水曜定休）</span>
      </div>
    </div>
  </section>

  <section class="access fadeIn">
    <h2 class="page-title">アクセス</h2>

    <video
      src="<?php echo get_template_directory_uri(); ?>/images/access/access.mp4"
      class="access__video"
      controls
      poster="<?php echo get_template_directory_uri(); ?>/images/access/access-screenshot.webp"
      playsinline></video>
    <h3 class="content-title access__title">
      世界遺産 識名園の正面に位置する<br class="sp" />琉球識名院・琉球御廟
    </h3>
    <p class="access__text body-copy">
      琉球識名院・琉球御廟は、世界遺産識名園の正面に位置し、<br
        class="pc" />那覇インターから車で5分、那覇市中心から車で15分。バス路線は下記の5路線が乗り入れてます。
    </p>
    <h4 class="content-title access__subtitle">■バス路線情報</h4>
    <p class="body-copy">
      市内線(路線バス)「識名園前」バス停下車<br />
      2番 識名・開南線 / 3番 松川新都心線 / 4番 新川おもろまち線 / 5番
      識名・牧志線 / 14番 牧志開南循環線
    </p>
    <div class="access__maps fadeIn">
      <img src="<?php echo get_template_directory_uri(); ?>/images/common/map02.png" alt="" class="access__map" />
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7159.441673391875!2d127.715022!3d26.205758!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x34e5694fc77fa23f%3A0xd1255428ad04f65a!2z44CSOTAyLTAwNzIg5rKW57iE55yM6YKj6KaH5biC55yf5Zyw77yS!5e0!3m2!1sja!2sjp!4v1752566307096!5m2!1sja!2sjp"
        width="600"
        height="450"
        style="border: 0"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        class="access__map access__map--google"></iframe>
    </div>
  </section>

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item">琉球御廟の美</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>