<?php get_header(); ?>
<main>
  <section class="testimonials fadeIn">
    <h1 class="page-title testimonials__title">お客様からの声</h1>
    <p class="body-copy body-copy--spaced testimonials__intro">
      お客様からいただいた、契約をする決めてになったポイントや、<br />
      琉球御廟の印象をお聞きしました。
    </p>
    <div class="filter">
      <div class="filter-type filter-type--active" data-index="0">
        お客様からの声
      </div>
      <?php /*
      <div class="filter-type" data-index="1">天国への手紙</div>
      <div class="filter-type" data-index="2">法要の様子</div>
      */ ?>
      <div class="filter-type-indicator"></div>
    </div>

    <div class="cards">
      <?php
      $args = array(
        'post_type' => 'feedback',/* カスタム投稿名 */
        'posts_per_page' => 12,/* 表示する数 */
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
        <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
          <!-- ▽ ループ開始 ▽ -->
          <a href="<?php the_permalink(); ?>" class="card fadeInUp">
            <!-- <span class="card__date">0000.00.00</span> -->

            <?php if (get_field('feedback_img01')) : ?>
              <div class="card__image__wrap">
                <img src="<?php the_field('feedback_img01'); ?>" alt="<?php the_title(); ?>" class="event__image" />
              </div>
            <?php elseif (!get_field('feedback_img01')) : ?>
              <?php /*<img src="<?php echo get_template_directory_uri(); ?>/images/common/no-image.webp" alt="<?php the_title(); ?>" class="event__image" />*/ ?>
            <?php endif; ?>

            <p class="card__text">
              <?php the_title(); ?>
            </p>
            <div class="card__button">続きを見る</div>
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
      <?php /*
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      <a href="/post-details/" class="card fadeInUp">
        <span class="card__date">0000.00.00</span>
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/common/default-image.webp"
          alt=""
          class="card__image" />
        <p class="card__text">
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
          本土からの移住者ですが、沖縄のお墓をさがしていたところ住宅購入や地鎮祭などでお世話になっている地元の方から
        </p>
        <div class="card__button">続きを見る</div>
      </a>
      */ ?>
    </div>
    <?php if (function_exists("wp_pagenavi")) wp_pagenavi(array('query' => $my_query)); ?>
    <!-- <ul class="pager">
      <li class="pager__item">
        <a href="#" class="prev-btn"></a>
      </li>
      <li class="pager__item">
        <a href="#">1</a>
      </li>
      <li class="pager__item"><a href="#">2</a></li>
      <li class="pager__item"><a href="#">3</a></li>
      <li class="pager__item pager__item--active"><a href="#">4</a></li>
      <li class="pager__item"><a href="#">5</a></li>
      <li class="pager__item">
        <a href="#" class="next-btn"></a>
      </li>
    </ul> -->
  </section>

  <section class="contact-section">
    <h2 class="page-title">お問い合わせ</h2>
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
      <li class="breadcrumb__item">お客様からの声</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>