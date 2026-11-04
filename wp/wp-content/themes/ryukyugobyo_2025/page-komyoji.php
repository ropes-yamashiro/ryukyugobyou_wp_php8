<?php get_header(); ?>
<main>
  <div class="selector fadeIn">
    <h1 class="beauty__title page-title">光明寺 琉球識名院について</h1>
    <nav class="navigation">
      <div class="mobile-dropdown">
        <div class="dropdown-header">
          <span class="dropdown-title">光明寺 琉球識名院について</span>
        </div>
        <ul class="dropdown-list">
          <li>
            <a href="/beauty/" class="nav-item">琉球御廟の美</a>
          </li>
          <li>
            <span class="nav-item">琉球御廟の設備</span>
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
            <a href="/komyoji/" class="nav-item active">光明寺 琉球識名院について</a>
          </li>
          <li><a href="/faq/" class="nav-item">よくある質問</a></li>
        </ul>
      </div>
    </nav>
  </div>

  <section class="komyoji fadeIn">
    <div class="komyoji__item komyoji__item--video">
      <video
        src="<?php echo get_template_directory_uri(); ?>/images/komyoji/06_jushoku.mp4"
        controls
        class="komyoji__item-video"
        poster="<?php echo get_template_directory_uri(); ?>/images/komyoji/aisatsu-screenshot.webp"
        playsinline></video>
      <div class="komyoji__item-content">
        <h2 class="section-title komyoji__item-title">
          琉球識名院について
        </h2>
        <p class="body-copy">
          光明寺 沖縄別院
          琉球識名院が2022年2月に建立されます。琉球識名院は沖縄最大級の木造建築寺院としても注目されています。
          和風・琉球風それぞれの様式が融合した新たな寺院は沖縄に新しい和みの場と新しい供養の場を提供します。
        </p>
      </div>
    </div>
    <div class="komyoji__item fadeIn">
      <img
        src="<?php echo get_template_directory_uri(); ?>/images/komyoji/aisatsu.webp"
        alt=""
        class="komyoji__item-image" />
      <h2 class="section-title komyoji__item-title">
        琉球識名院<br class="sp" />住職のあいさつ
      </h2>
      <p class="body-copy">
        新型コロナ感染症の影響で延期しておりました琉球識名院の落慶法要を、令和５年２月１１日に、多くの方々にお祝いいただきながら執り行いました。
        鮮やかな衣装に身をまとった可愛らしい稚児行列に始まり、美しい琉球瓦の大屋根を映す水盤が花に彩られ、金色の鴟尾が輝くなか、本堂で約20名の僧侶による読経、琉球古典音楽の献奏などが厳粛に行われました。
        　琉球識名院は、ご本尊である阿弥陀如来のはかりしれない智慧と慈悲の光のご加護の下、仏法を伝え、人々の心に安らぎを与え、また、音楽や伝統芸能にあふれ、気軽に参拝いただける親しみのあるお寺であり続けたいと考えています。
      </p>
      <span class="body-copy komyoji__item-sign">無量寿山 光明寺 琉球識名院<br class="sp" />住職 大洞 龍真</span>
    </div>
  </section>

  <section class="komyoji-gallery fadeIn">
    <div class="gallery__decor"></div>
    <div class="swiper gallery__swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide gallery__item" slide-index="0">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-1.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              沖縄開教への想い
            </h2>
            <p class="gallery__item-text">
              仏さまの智恵は、普く三千大千世界を隅々まで照らし、誰一人捨てることなく救い取るとされています。
              それは、生者・死者の隔てなく、誰もが同じように救われるという意味で
              す。 その智恵に触れた者は、仏さまと同じ境地に至ります。
              そして心の迷いは打ち消えて、慈悲の心が生まれるのです。
              それゆえ、仏教が繁盛する土地では、人々の心が穏やかになり、争いはなくなり、ご先祖さまに守られ幸せに暮らすことができると言われます。<br />
              この素晴らしい仏教を沖縄の皆様に伝えるために、私たちは那覇市に新たな寺院を建立することにいたしました。
              永代にわたって、皆様のご先祖さまを供養し、仏教に触れる場所を担っていきたいと思います。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="1">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-2.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              識名という土地への想い
            </h2>
            <p class="gallery__item-text">
              琉球王朝の迎賓館であった識名園があるこの土地は、沖縄の歴史を紐解くのにとても重要な場所のひとつです。
              そして現代においても、多くのお墓が集まり、人々にとってご先祖
              さまを敬う尊い場所です。
              私たちは寺院を設計するに当たって、沖縄に相応しい外観を持った本堂や山門など、何より地域との調和を大切にいたしました。
              この地に仏教の火を灯し、地域に馴染み、末永く皆さまに親しまれる寺院となることを目指します。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="2">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-3.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              沖縄最大級の木造建築寺院
            </h2>
            <p class="gallery__item-text">
              より恒久的にその地に存在できるものでありたい。との願いから本堂は木造建築といたしました。
              約1,300年前に建立され現在も残る木造建築物「奈良斑鳩の法隆寺」のように、
              木を適材適所に組み合わせて大切に使えば、とても永く保つことができます。
              使うとともに風合いが増し風格も備わるのも木造ならではのものと考えます。
              皆様に愛され、共に齢を重ね、地域に永く根付く寺院となることを願い本堂を木造といたしました。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="3">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-4.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              材料へのこだわり
            </h2>
            <p class="gallery__item-text">
              現在では大径の樹木は日本だけでなく殆どの国で存在しなくなりました。また、伐採後も十分に自然乾燥させないと割れてしまったり大きく反ったりするために直ぐには使えま
              せん。
              木造建築とするには不可欠な樹木を世界中が視野に探していたところ、
              非常に偶然にも
              「想定樹齢1,000年以上、直径1,000㎜級の大変貴重なの米ヒバ（カナダ産檜）」が奈良の吉野に16年間保管されていることが判明し、当寺院建立に使用させていただくことに理解をいただきました。
              この無垢檜は香りが良いだけでなく、ヒノキチオールというシロアリ等の害虫に対する抗菌効果が非常に高い成分が含まれており、腐りにくく耐久性が大変に優れています。まさに最高の樹木と巡り合うことができました。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="4">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-5.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">建設設計の趣旨</h2>
            <p class="gallery__item-text">
              南に識名園、北には首里城という世界遺産に囲まれた「識名歴史エリア」に位置する当地は琉球王朝の時代から琉球松並木の真珠道が通る風光明媚な場所であったと伝えら
              れております。
              南側の山門をくぐると、水盤越しに本堂を仰ぎ見ることができます。
              圧迫感を与えない寄棟形式の赤瓦葺きの屋根には黄金色の鴟尾が置かれ、水盤にはこの本堂や碧空が映り込むように設計しました。
              拝所は赤瓦とともに琉球石灰岩の石壁とし優しく落ち着いた意匠としています。
              庭園には緑豊かな識名園と連続する場所として、
              植栽樹種には琉球松やガジュマル等の沖縄産樹木を配し、また舗装や囲い塀にも琉球石灰岩を使用し、本寺院への信者や参拝者はもとより、識名園への来訪者が気軽に立ち寄ることができる憩いの場を計画しています。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="0">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-1.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              沖縄開教への想い
            </h2>
            <p class="gallery__item-text">
              仏さまの智恵は、普く三千大千世界を隅々まで照らし、誰一人捨てることなく救い取るとされています。
              それは、生者・死者の隔てなく、誰もが同じように救われるという意味で
              す。 その智恵に触れた者は、仏さまと同じ境地に至ります。
              そして心の迷いは打ち消えて、慈悲の心が生まれるのです。
              それゆえ、仏教が繁盛する土地では、人々の心が穏やかになり、争いはなくなり、ご先祖さまに守られ幸せに暮らすことができると言われます。<br />
              この素晴らしい仏教を沖縄の皆様に伝えるために、私たちは那覇市に新たな寺院を建立することにいたしました。
              永代にわたって、皆様のご先祖さまを供養し、仏教に触れる場所を担っていきたいと思います。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="1">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-2.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              識名という土地への想い
            </h2>
            <p class="gallery__item-text">
              琉球王朝の迎賓館であった識名園があるこの土地は、沖縄の歴史を紐解くのにとても重要な場所のひとつです。
              そして現代においても、多くのお墓が集まり、人々にとってご先祖
              さまを敬う尊い場所です。
              私たちは寺院を設計するに当たって、沖縄に相応しい外観を持った本堂や山門など、何より地域との調和を大切にいたしました。
              この地に仏教の火を灯し、地域に馴染み、末永く皆さまに親しまれる寺院となることを目指します。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="2">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-3.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              沖縄最大級の木造建築寺院
            </h2>
            <p class="gallery__item-text">
              より恒久的にその地に存在できるものでありたい。との願いから本堂は木造建築といたしました。
              約1,300年前に建立され現在も残る木造建築物「奈良斑鳩の法隆寺」のように、
              木を適材適所に組み合わせて大切に使えば、とても永く保つことができます。
              使うとともに風合いが増し風格も備わるのも木造ならではのものと考えます。
              皆様に愛され、共に齢を重ね、地域に永く根付く寺院となることを願い本堂を木造といたしました。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="3">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-4.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">
              材料へのこだわり
            </h2>
            <p class="gallery__item-text">
              現在では大径の樹木は日本だけでなく殆どの国で存在しなくなりました。また、伐採後も十分に自然乾燥させないと割れてしまったり大きく反ったりするために直ぐには使えま
              せん。
              木造建築とするには不可欠な樹木を世界中が視野に探していたところ、
              非常に偶然にも
              「想定樹齢1,000年以上、直径1,000㎜級の大変貴重なの米ヒバ（カナダ産檜）」が奈良の吉野に16年間保管されていることが判明し、当寺院建立に使用させていただくことに理解をいただきました。
              この無垢檜は香りが良いだけでなく、ヒノキチオールというシロアリ等の害虫に対する抗菌効果が非常に高い成分が含まれており、腐りにくく耐久性が大変に優れています。まさに最高の樹木と巡り合うことができました。
            </p>
          </div>
        </div>
        <div class="swiper-slide gallery__item" slide-index="4">
          <img
            src="<?php echo get_template_directory_uri(); ?>/images/komyoji/gallery-5.webp"
            alt=""
            class="gallery__item-image" />
          <div class="gallery__item-content">
            <h2 class="block-title gallery__item-title">建設設計の趣旨</h2>
            <p class="gallery__item-text">
              南に識名園、北には首里城という世界遺産に囲まれた「識名歴史エリア」に位置する当地は琉球王朝の時代から琉球松並木の真珠道が通る風光明媚な場所であったと伝えら
              れております。
              南側の山門をくぐると、水盤越しに本堂を仰ぎ見ることができます。
              圧迫感を与えない寄棟形式の赤瓦葺きの屋根には黄金色の鴟尾が置かれ、水盤にはこの本堂や碧空が映り込むように設計しました。
              拝所は赤瓦とともに琉球石灰岩の石壁とし優しく落ち着いた意匠としています。
              庭園には緑豊かな識名園と連続する場所として、
              植栽樹種には琉球松やガジュマル等の沖縄産樹木を配し、また舗装や囲い塀にも琉球石灰岩を使用し、本寺院への信者や参拝者はもとより、識名園への来訪者が気軽に立ち寄ることができる憩いの場を計画しています。
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="gallery__buttons">
      <div class="gallery__button gallery__button--prev"></div>
      <div class="gallery__button gallery__button--next"></div>
    </div>
  </section>

  <section class="events fadeIn">
    <h2 class="section-title">イベント・キャンペーン</h2>

    <div class="swiper events__swiper">
      <div class="swiper-wrapper">
        <?php
        $args = array(
          'post_type' => 'event',/* カスタム投稿名 */
          'posts_per_page' => 4,/* 表示する数 */
          'paged' => $paged,
          'paged' => get_query_var('paged'),
          'orderby'        => 'date',   // ★ 追加：投稿日でソート
          'order'          => 'DESC',
          'tax_query'      => array(
            array(
              'taxonomy' => 'event_category', // カスタム分類名
              'field'    => 'slug',
              'terms'    => 'event',   // 表示したいスラッグ
            ),
          ),
        );
        ?>
        <?php
        $my_query = new WP_Query($args); ?>
        <?php if ($my_query->have_posts()) : // 投稿がある場合 
        ?>
          <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
            <!-- ▽ ループ開始 ▽ -->
            <a
              href="<?php the_permalink(); ?>"
              class="swiper-slide events__swiper-item">
              <div class="event__image__wrap">
                <?php if (get_field('event_img01')) : ?>
                  <img src="<?php the_field('event_img01'); ?>" alt="<?php the_title(); ?>" class="event__image" />
                <?php elseif (!get_field('event_img01')) : ?>
                  <img src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp" alt="<?php the_title(); ?>" class="event__image" />
                <?php endif; ?>
              </div>
              <div class="events__swiper-tags">
                <span class="events__swiper-tag events__swiper-tag--date"><?php echo get_the_date('Y.m.d'); ?></span>
                <?php
                if ($terms = get_the_terms($post->ID, 'event_category')) {
                  foreach ($terms as $term) {
                    echo '<span class="events__swiper-tag events__swiper-tag--category">' . esc_html($term->name) . '</span>';
                  }
                }
                ?>
              </div>
              <h3 class="body-copy body-copy--spaced events__swiper-title">
                <?php the_title(); ?>
              </h3>
            </a>
            <!-- △ ループ終了 △ -->
          <?php endwhile; ?>
        <?php else : // 投稿がない場合 
        ?>
          <div class="propertye__no-post">
            現在キャンペーン・イベントはございません。
          </div>
        <?php endif;
        wp_reset_postdata(); ?>
      </div>
    </div>

    <a href="/events-list/" class="link-button">
      <span>一覧を見る</span>
    </a>
  </section>

  <section class="about fadeIn">
    <div class="about__banner">
      <h2 class="page-title about__banner-title">
        無量寿山 光明寺について
      </h2>
    </div>
    <div class="about__wrapper">
      <img src="<?php echo get_template_directory_uri(); ?>/images/komyoji/entry.webp" alt="" class="about__image" />
      <p class="about__text">
        光明寺の歴史は古く、その起源は1200年前の延喜式神名帳に記載された意富布良（大洞）神社まで遡ります。この神社はいつしか神仏一体神宮社になりました。室町時代に京都本願寺第八代住職の蓮如上人の教化によって神社と寺院が分離され、それが現在の光明寺の祖となる近江祖坊となったのです。<br />
        その後、時を経て岐阜と京都に光明寺岐阜本院、光明寺京都本院瑠璃光院などに発展していきました。
        現在では、町屋光明寺、千葉光明寺、新宿光明寺瑠璃光院、埼玉光明寺などの別院を有しています。
      </p>
    </div>
  </section>

  <section class="contact-section fadeIn">
    <h2 class="section-title">お問い合わせ</h2>
    <p class="body-copy body-copy--narrow contact-section__text">
      以下の問い合わせフォームまたはお電話にてお気軽にお問合せください。
    </p>
    <div class="contact-section__buttons">
      <!-- <a href="/contact/" class="contact-section__button link-button"><span>お問合わせフォーム</span></a> -->
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
      <li class="breadcrumb__item">光明寺 琉球識名院について</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>