<?php get_header(); ?>
<main>
  <section class="events fadeIn">
    <h1 class="page-title">キャンペーン・<br class="sp" />イベント一覧</h1>
    <div class="filter">
      <a href="/events-list/" class="filter-type filter-type--active" data-index="0">
        全ての情報
      </a>
      <a href="/events-campaign/" class="filter-type">キャンペーン</a>
      <a href="/events-event/" class="filter-type">イベント</a>

      <div class="filter-type-indicator"></div>
    </div>

    <div class="events__wrapper">
      <?php
      $args = array(
        'post_type' => 'event',/* カスタム投稿名 */
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
          <a href="<?php the_permalink(); ?>" class="event fadeInUp">
            <div class="event__image__wrap">
              <?php if (get_field('event_img01')) : ?>
                <img src="<?php the_field('event_img01'); ?>" alt="<?php the_title(); ?>" class="event__image" />
              <?php elseif (!get_field('event_img01')) : ?>
                <img src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp" alt="<?php the_title(); ?>" class="event__image" />
              <?php endif; ?>
            </div>
            <div class="event__tags">
              <span class="event__date"><?php echo get_the_date('Y.m.d'); ?></span>
              <?php
              if ($terms = get_the_terms($post->ID, 'event_category')) {
                foreach ($terms as $term) {
                  echo '<span class="event__category">' . esc_html($term->name) . '</span>';
                }
              }
              ?>
            </div>
            <h2 class="event__title">
              <?php the_title(); ?>
            </h2>
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
      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-11.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-10.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-9.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-8.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-7.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-6.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-5.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-4.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-3.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-2.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/event-1.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
      </a>

      <a href="/event-details.html" class="event fadeInUp">
        <img
          src="<?php echo get_template_directory_uri(); ?>/images/events/no-image.webp"
          alt=""
          class="event__image" />
        <div class="event__tags">
          <span class="event__date">0000.00.00</span>
          <span class="event__category">カテゴリ</span>
        </div>
        <h2 class="event__title">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </h2>
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

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item">キャンペーン・イベント</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>