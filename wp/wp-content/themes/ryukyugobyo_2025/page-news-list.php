<?php get_header(); ?>
<main>
  <section class="news fadeIn">
    <h1 class="page-title">新着情報一覧</h1>
    <div class="filter">
      <a href="/news-list/" class="filter-type filter-type--active" data-index="0">
        全ての情報
      </a>
      <a href="/news-closed/" class="filter-type">
        休館日の<br class="sp" />お知らせ
      </a>
      <a href="/news-others/" class="filter-type">
        その他<br class="sp" />お知らせ
      </a>
      <a href="/news-commercial/" class="filter-type">テレビCM</a>
      <div class="filter-type-indicator"></div>
    </div>

    <div class="news__wrapper fadeInUp">
      <?php
      $args = array(
        'post_type' => 'news',/* カスタム投稿名 */
        'posts_per_page' => 10,/* 表示する数 */
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
          <a href="<?php the_permalink(); ?>" class="news__item">
            <span class="news__item-date"><?php echo get_the_date('Y.m.d'); ?></span>
            <p class="body-copy news__item-text">
              <?php the_title(); ?>
            </p>
          </a>
          <!-- △ ループ終了 △ -->
        <?php endwhile; ?>
      <?php else : // 投稿がない場合 
      ?>
        <div class="propertye__no-post">
          現在新着情報はございません。
        </div>
      <?php endif;
      wp_reset_postdata(); ?>
      <!-- <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a>
      <a href="/news-details.html" class="news__item">
        <span class="news__item-date">0000.00.00</span>
        <p class="body-copy news__item-text">
          タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示タイトル表示
        </p>
      </a> -->
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
      <li class="breadcrumb__item">新着情報一覧</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>