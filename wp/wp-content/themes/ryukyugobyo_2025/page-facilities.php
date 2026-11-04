<?php get_header(); ?>
<main>
  <div class="selector fadeIn">
    <h1 class="beauty__title page-title">琉球御廟の設備</h1>
    <nav class="navigation">
      <div class="mobile-dropdown">
        <div class="dropdown-header">
          <span class="dropdown-title">琉球御廟の設備</span>
        </div>
        <ul class="dropdown-list">
          <li>
            <a href="/beauty/" class="nav-item">琉球御廟の美</a>
          </li>
          <li>
            <span class="nav-item active">琉球御廟の設備</span>
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

  <section class="facilities-intro fadeIn">
    <!-- <video src="" class="facilities-intro__video" playsinline></video> -->
    <img class="facilities-intro__video" src="<?php echo get_template_directory_uri(); ?>/images/facilities/facilities__indoor.jpg" alt="">
    <img src="" alt="">
    <p class="facilities-intro__text body-copy">
      無量寿山 光明寺 沖縄別院 琉球識名院内に
      設置された自動搬送式納骨堂が琉球御廟です。<br />
      琉球御廟は、沖縄県内初の「室内墓所」として、沖縄のお墓の悩みを解決します。<br />
      “新しい供養のカタチ”がここに誕生しました。
    </p>
  </section>

  <section class="problems fadeIn">
    <h2 class="section-title problems__title">近年増えているお墓の悩み</h2>

    <div class="swiper problems__swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide problems__slide">
          年をとってしまって<br />
          なかなかお墓参りに<br />
          行けなくなった。
        </div>
        <div class="swiper-slide problems__slide">
          市街地に住んでいるので、<br />
          遠くのお墓の管理が<br />
          大変になってしまった。
        </div>
        <div class="swiper-slide problems__slide">
          お墓を<br />
          継いでくれる<br />
          人がいない。
        </div>
        <div class="swiper-slide problems__slide">
          子供にお墓を<br />
          継がせるのは可哀想。
        </div>
        <div class="swiper-slide problems__slide">
          お墓を作りたいが、<br />
          土地がないし、<br />
          費用がかかる。
        </div>
      </div>
    </div>
    <h2 class="problems__title section-title">
      そんな悩みから<br class="sp" />生まれたのが<br class="sp" />室内墓所
      琉球御廟です。
    </h2>
  </section>

  <section class="indoor fadeIn">
    <div class="indoor__wrapper">
      <img
        src="<?php echo get_template_directory_uri(); ?>/images/facilities/indoor.webp"
        alt="室内墓所"
        class="indoor__image" />
      <div class="indoor__content">
        <h2 class="section-title indoor__title">室内墓所とは</h2>
        <p class="indoor__text body-copy">
          室内墓所とは、文字通り室内にあるお墓です。その中でも最近特に注目されているのが、自動搬送式納骨堂というものです。<br />
          自動搬送納骨堂とはコンピュータ制御された納骨堂から参拝ブースへと遺骨が入った厨子が運ばれ、通常のお墓と同じようにお参りが出来るというシステムです。
        </p>
      </div>
    </div>
    <p class="indoor__text body-copy">
      このようなタイプのお墓が登場したのには、様々な理由があります。昨今、上記のようなお墓にまつわるニュースなどを一度は目にしたことがあるのではないでしょうか。沖縄ではさらに駐車場の問題、アクセスの問題、天候の問題などお墓の悩みは非常に多いのです。
      それらの問題解決の手段として室内墓所は誕生しました。現在は首都圏、関西圏を中心に数多くの室内墓所が作られています。
    </p>
  </section>

  <section class="benefits fadeIn">
    <h2 class="section-title benefits__title">室内墓所のメリット</h2>
    <div class="swiper benefits__swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-1.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">アクセスが便利</h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-2.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">掃除などの管理が不要</h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-3.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">
            永代供養が付いている<br />場合が多い
          </h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-4.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">
            雨天でも猛暑でも<br />
            快適にお参りが出来る
          </h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-5.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">
            手ぶらでの<br />
            お参りが可能
          </h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-6.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">
            低価格でお墓を<br />
            もつことができる
          </h3>
        </div>
        <div class="swiper-slide benefits__slide">
          <div class="benefits__slide-image">
            <img src="<?php echo get_template_directory_uri(); ?>/images/facilities/benefit-7.svg" alt="" />
          </div>
          <h3 class="benefits__slide-title">
            宗旨・宗派が<br />
            問われないことが多い
          </h3>
        </div>
      </div>
    </div>
  </section>

  <section class="ossuary fadeIn">
    <div class="ossuary__wrapper">
      <div class="ossuary__item">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/facilities/ossuary-1_02.png"
          alt=""
          class="ossusary__item-image" />
        <div class="ossuary__item-content">
          <h2 class="section-title ossuary__item-title">
            琉球御廟の<br class="sp" />自動搬送式納骨堂
          </h2>
          <p class="body-copy ossuary__item-text">
            琉球御廟は、沖縄初の室内墓所であると同時に、沖縄初の自動搬送式納骨堂でもあります。<br
              class="pc" />最新のITで制御されたシステムを活用し、ご遺骨を個別に直接参拝することができます。
          </p>
        </div>
      </div>
      <div class="ossuary__item fadeIn">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/facilities/ossuary-2.webp"
          alt=""
          class="ossusary__item-image" />
        <div class="ossuary__item-content">
          <h2 class="section-title ossuary__item-title">
            琉球御廟の安心のシステム
          </h2>
          <p class="body-copy ossuary__item-text">
            トヨタL&Fが開発したコンピュータ制御の自動搬送式納骨システムを採用いたしました。トヨタL&Fは、物流現場や倉庫などの保管・搬送に関わるシステム・機器を提供しており、多くの企業で採用されており、高い信頼性を誇っています。<br />
            お預かりした大切なご遺骨を、責任を持ってお守りします。ICチップによって制御されておりセキュリティも万全です。
          </p>
        </div>
      </div>
      <div class="ossuary__item fadeIn">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/facilities/ossuary-3.webp"
          alt=""
          class="ossusary__item-image" />
        <div class="ossuary__item-content">
          <h2 class="section-title ossuary__item-title">
            お骨を納める厨子
          </h2>
          <p class="body-copy ossuary__item-text">
            ご遺骨は、納骨袋にご遺骨を移し替えて、厨子と呼ばれる納骨箱に納めます。厨子はご夫婦をはじめ、６名様程度の収蔵が目安となっています。ご希望であれば古いご遺骨から、合同参拝所にお移しすることも可能です。永代供養付きなので万一将来に代が途絶えても、永代に<br
              class="pc" />渡ってご供養いたします。
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="prayer fadeIn">
    <div class="room">
      <video
        src="<?php echo get_template_directory_uri(); ?>/images/facilities/03_sanpaishitu.mp4"
        class="room__video"
        controls
        poster="<?php echo get_template_directory_uri(); ?>/images/facilities/facility-screenshot.webp"
        playsinline></video>
      <div class="room__content">
        <h2 class="section-title room__title">参拝室</h2>
        <p class="body-copy room__text">
          墓石と厨子の後ろの壁は、沖縄発・日本で唯一の染色技法であるサンゴ染めで描かれています。時間とともに変化する光に沖縄の美しい海と空を組み合わせ、見るものに希望とやすらぎを感じさせるデザインです。
        </p>
      </div>
    </div>

    <div class="features fadeIn">
      <h2 class="section-title features__title">参拝室の特徴</h2>
      <div class="features__wrapper">
        <div class="features__item">
          <h3 class="section-subtitle features__item-title">
            天候に左右されない<br />
            快適な空間
          </h3>
          <p class="features__item-text">
            室内の参拝室は空調が完備され、天候の影響を受けることなくいつでも快適に安心してお参りができます。
          </p>
        </div>
        <div class="features__item">
          <h3 class="section-subtitle features__item-title">
            広いスペースで<br />
            お供え物も置ける
          </h3>
          <p class="features__item-text">
            お墓はお供え物を置くスペースが十分にあり、清明祭の時などたくさんのお供え物を置くことが可能です。
          </p>
        </div>
        <div class="features__item">
          <h3 class="section-subtitle features__item-title">
            ご参拝の人数に応じて<br />
            参拝室が選べる
          </h3>
          <p class="features__item-text">
            全12室の参拝室は、1Fが6室、2Fが6室ご用意されています。ご参拝の人数やご気分で参拝室を選ぶことができます。
          </p>
        </div>
      </div>
    </div>

    <div class="floors fadeIn">
      <div class="floor">
        <h3 class="section-subtitle floor__title">1F　光と海のイメージ</h3>

        <div class="swiper floor__swiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-1.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 1】曙光（しょうこう）<br />
                東の空にさしてくる夜明けの光
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-2.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 2】光輝（こうき）<br />
                光を放つほどの輝き
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-3.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 3】眩耀（げんよう）<br />
                降り注ぐ眩しい光
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-4.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 4】光耀（こうよう）<br />
                美しく彩られた光
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-5.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 5】晩輝（ばんき）<br />
                衰えない夕日の光
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-1-6.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【海 6】清輝（せいき）<br />
                奥深い本質的な輝き
              </h4>
            </div>
          </div>
          <div class="floor__buttons">
            <div class="floor__button floor__button--prev"></div>
            <div class="floor__button floor__button--next"></div>
          </div>
        </div>
      </div>

      <div class="floor fadeIn">
        <h3 class="section-subtitle floor__title">2F　光と空のイメージ</h3>
        <div class="swiper floor__swiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-1.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 1】旭日（きょくじつ）<br />
                天にのぼる朝日のように<br />
                勢いが盛んなこと
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-2.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 2】春陽（しゅんよう）<br />
                若々しい新緑が生い茂り<br />
                活力や夢に満ちている
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-3.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 3】炎陽（えんよう）<br />
                燃え盛るような<br />
                勢いに満ちている
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-4.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 4】秋陽（しゅうよう）<br />
                実り豊かに<br />
                充実している
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-5.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 5】愛日（あいじつ）<br />
                時間を惜しんで熟成
              </h4>
            </div>
            <div class="swiper-slide floor__slide">
              <img
                src="<?php echo get_template_directory_uri(); ?>/images/facilities/floor-2-6.webp"
                alt=""
                class="floor__slide-image" />
              <h4 class="floor__slide-title">
                【空 6】夕日（せきじつ）<br />
                夜空に輝く昔日<br />
                面影や積日、多くの日々
              </h4>
            </div>
          </div>
          <div class="floor__buttons">
            <div class="floor__button floor__button--prev"></div>
            <div class="floor__button floor__button--next"></div>
          </div>
        </div>
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

  <section class="access fadeIn" id="access">
    <div class="access__wrapper">
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
    </div>

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
      <li class="breadcrumb__item">琉球御廟の設備</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>