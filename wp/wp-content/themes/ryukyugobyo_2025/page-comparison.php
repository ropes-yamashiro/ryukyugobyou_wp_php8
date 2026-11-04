<?php get_header(); ?>
<main>
  <div class="selector fadeIn">
    <h1 class="beauty__title page-title">
      費用について・琉球御廟と個人墓地 比較
    </h1>
    <nav class="navigation">
      <div class="mobile-dropdown">
        <div class="dropdown-header">
          <span class="dropdown-title">費用・個人墓地との比較</span>
        </div>
        <ul class="dropdown-list">
          <li>
            <a href="/beauty/" class="nav-item">琉球御廟の美</a>
          </li>
          <li>
            <a href="/facilities/" class="nav-item">琉球御廟の設備</a>
          </li>
          <li>
            <a href="/comparison/" class="nav-item active">費用・個人墓地との比較</a>
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

  <section class="comparison-intro fadeIn">
    <video
      src="<?php echo get_template_directory_uri(); ?>/images/comparison/comparison_02.mp4"
      controls
      class="comparison-intro__video"
      playsinline
      poster="<?php echo get_template_directory_uri(); ?>/images/comparison/comparison-screenshot_02.png"></video>
  </section>

  <section class="pricing fadeIn">
    <h2 class="section-title pricing__title">費用について</h2>
    <p class="pricing__text body-copy">
      納骨数に応じた初期費用にかかる費用と維持管理費だけと極めてシンプルな費用設計です。
    </p>

    <h3 class="section-title pricing__subtitle">初期費用</h3>
    <div class="pricing__wrapper">
      <div class="pricing__item">
        <h4 class="pricing__item-title">納骨数1名様</h4>
        <div class="pricing__item-content pricing__item--new-icon">
          <h5 class="pricing__item-subtitle">おー人様タイプ</h5>
          <div class="pricing__item-price">
            <span class="pricing__item-value">32</span>
            <span class="pricing__item-unit">万円</span>
          </div>
          <p class="pricing__item-desc">
            ●すぐに永代供養ではなく<br />
            　7年の安置後なので迷っている方向け<br />
            ●管理費負担は含まれており<br />
            　低価格で満足いただけるタイプ<br />
          </p>
        </div>
      </div>
      <div class="pricing__item">
        <h4 class="pricing__item-title">納骨数1名様～4名様推奨</h4>
        <div class="pricing__item-content">
          <h5 class="pricing__item-subtitle">家族・夫婦タイプ</h5>
          <div class="pricing__item-price">
            <span class="pricing__item-value">95</span>
            <span class="pricing__item-unit">万円</span>
          </div>
          <p class="pricing__item-desc">
            ●御夫婦とお子様、<br />
            　もしくはご両親等、<br />
            　納骨数が限られるタイプ <br />
            ※人数により納骨方法は異なります。
          </p>
        </div>
      </div>
      <div class="pricing__item">
        <h4 class="pricing__item-title">納骨数5名様～8名様推奨</h4>
        <div class="pricing__item-content">
          <h5 class="pricing__item-subtitle">承継タイプ</h5>
          <div class="pricing__item-price">
            <span class="pricing__item-value">125</span>
            <span class="pricing__item-unit">万円</span>
          </div>
          <p class="pricing__item-desc">
            ●子どもに継承することを<br />
            　前提としたご家族向けタイプ<br />
            ●改葬後の遺骨の収蔵を<br />
            　検討しているご家族向けタイプ
          </p>
        </div>
      </div>
    </div>

    <h2 class="section-title pricing__title fadeIn">
      価格に含まれているもの
    </h2>
    <div class="pricing-inclusions fadeIn">
      <div class="pricing-inclusion">
        <h3 class="pricing-inclusion__title">永代使用料</h3>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/comparison/pricing-1.webp"
          alt=""
          class="pricing-inclusion__image" />
        <h4 class="pricing-inclusion__subtitle">
          当納骨堂の永代に渡るご使用料金
        </h4>
      </div>
      <div class="pricing-inclusion">
        <h3 class="pricing-inclusion__title">永代供養</h3>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/comparison/pricing-2.webp"
          alt=""
          class="pricing-inclusion__image" />
        <h4 class="pricing-inclusion__subtitle">
          後継者不在となっても永代供養する費用
        </h4>
      </div>
      <div class="pricing-inclusion">
        <h3 class="pricing-inclusion__title">遺骨収蔵厨子</h3>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/comparison/pricing-3.webp"
          alt=""
          class="pricing-inclusion__image" />
        <h4 class="pricing-inclusion__subtitle">
          大切な遺骨を収蔵する収納具
        </h4>
      </div>
    </div>
  </section>

  <section class="comparison fadeIn" id="comparison">
    <h2 class="section-title comparison__title">琉球御廟と個人墓地 比較</h2>
    <p class="body-copy comparison__text">
      お墓を購入する動機やお悩みは様々です。お墓をご検討されている方がもっとも気になるポイントをピックアップし、
      琉球御廟と個人墓地の特徴をわかりやすく比較しました。
    </p>

    <table class="comparison__table">
      <thead>
        <tr>
          <th></th>
          <th>
            <img src="<?php echo get_template_directory_uri(); ?>/images/comparison/table-1.webp" alt="" />
            <span>外にあるお墓</span>
          </th>
          <th>
            <img src="<?php echo get_template_directory_uri(); ?>/images/comparison/table-2.webp" alt="" />
            <span>琉球御廟</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>購入費用</th>
          <td>土地・お墓込みで 300万円以上</td>
          <td>永代使用料<br class="sp" />85万円から</td>
        </tr>
        <tr>
          <th>
            維持管理<br class="sp" />
            費用
          </th>
          <td>不要だが自身で管理維持する<br class="sp" />必要</td>
          <td>年間12,000 円の管理費が必要</td>
        </tr>
        <tr>
          <th>お墓の管理</th>
          <td>
            草刈りや掃除を<br class="sp" />
            自身で行う
          </td>
          <td>
            光明寺が<br class="sp" />
            管理清掃
          </td>
        </tr>
        <tr>
          <th>
            駐車場・<br class="sp" />
            トイレ
          </th>
          <td>自身で確保する</td>
          <td>敷地内・周辺契約駐車場50台・トイレ完備</td>
        </tr>
        <tr>
          <th>お墓の継承</th>
          <td>お墓が継承できない場合、自分で墓じまい</td>
          <td>お墓が継承できない場合、光明寺が永代供養</td>
        </tr>
        <tr>
          <th>施設</th>
          <td>自身で確保する</td>
          <td>
            本堂・法要室・和風庭園・<br class="sp" />
            ロビー
          </td>
        </tr>
        <tr>
          <th>
            バリア<br class="sp" />
            フリー
          </th>
          <td>車いすではお参りしにくいところが多い</td>
          <td>車いすでの墓参りも可能</td>
        </tr>
        <tr>
          <th>天候・気候</th>
          <td>傘やテントを用意しておく必要がある</td>
          <td>雨天や猛暑日でも気軽にお参りができる</td>
        </tr>
        <tr>
          <th>資産価値</th>
          <td>個人資産になる</td>
          <td>個人資産にはならない</td>
        </tr>
        <tr>
          <th>不動産登録</th>
          <td>必要</td>
          <td>不要</td>
        </tr>
        <tr>
          <th>
            墓地の<br class="sp" />
            経営許可
          </th>
          <td>必要</td>
          <td>不要</td>
        </tr>
        <tr>
          <th>セキュリ<br class="sp" />ティー</th>
          <td>自身で施錠などの方法で<br class="sp" />管理する</td>
          <td>ITで遺骨を個別管理</td>
        </tr>
      </tbody>
    </table>
  </section>

  <section class="information fadeIn">
    <h2 class="section-title information__title">お墓の移設もサポート</h2>
    <p class="body-copy information__text">
      琉球御廟には毎日、たくさんの方が改葬や墓じまいのご相談に来られます。
      またその多くの方が「ご先祖に悪いのでは？」「親戚に反対されたらどうしよう…」「お墓以外の費用がどれくらいかかるかわからない」など不安を感じています。しかしながら、改葬することによってお墓参りに行くのが便利になり、ゆっくりと心を落ち着かせてご先さまに想いを馳せ、感謝の気持ちを込めたより手厚いご供養が可能になります。
    </p>
    <div class="information__videos">
      <img src="<?php echo get_template_directory_uri(); ?>/images/comparison/support-pack.png" alt="">
    </div>

    <!-- <div class="information__videos">
      <div class="information__video">
        <video src=""></video>
        <h3 class="information__video-title">墓じまいについて</h3>
      </div>
      <div class="information__video">
        <video src=""></video>
        <h3 class="information__video-title">お墓の内部調査</h3>
      </div>
    </div> -->
  </section>

  <section class="information information--left fadeIn">
    <h2 class="section-title information__title">
      生前での<br class="sp" />ご準備についても<br class="sp" />お気軽に<br
        class="sp" />ご相談ください。
    </h2>
    <p class="body-copy information__text">
      琉球御廟では、生前契約も承っております。
      生前にご自身で契約しておくことで、「自分の希望がきちんと形になる安心感」「家族に負担をかけずにすむという安らぎ」を得ることができます。
      ご家族との時間を大切にしながら、これからを安心して過ごすための、やさしい備えのかたちとして、ぜひご検討ください。
    </p>
  </section>

  <section class="contact-section fadeIn">
    <h2 class="section-title">お問い合わせ</h2>
    <p class="body-copy body-copy--narrow contact-section__text">
      問い合わせフォームまたはお電話にてお気軽にお問合せください。
    </p>
    <div class="contact-section__buttons">
      <!-- <a href="/contact.html" class="contact-section__button link-button"><span>お問合わせフォーム</span></a> -->
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
      <li class="breadcrumb__item">
        費用について・琉球御廟と個人墓地 比較
      </li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>