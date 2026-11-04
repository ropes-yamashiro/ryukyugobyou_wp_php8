<?php get_header(); ?>
<main>
  <section class="news fadeIn">
    <h1 class="page-title">新着情報(その他お知らせ)</h1>
    <div class="filter">
      <a href="/news-list/" class="filter-type">
        全ての情報
      </a>
      <a href="/news-closed/" class="filter-type filter-type--active">
        休館日の<br class="sp" />お知らせ
      </a>
      <a href="/news-others/" class="filter-type">
        その他<br class="sp" />お知らせ
      </a>
      <a href="/news-commercial/" class="filter-type" data-index="0">テレビCM</a>
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
        'order'          => 'DESC',
        'tax_query'      => array(
          array(
            'taxonomy' => 'news_category', // カスタム分類名
            'field'    => 'slug',
            'terms'    => 'commercial',   // 表示したいスラッグ
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
          <a href="<?php the_permalink(); ?>" class="news__item">
            <span class="news__item-date"><?php echo get_the_date('Y.m.d'); ?></span>
            <p class="body-copy news__item-text">
              <?php the_title(); ?>
            </p>
          </a>
          <!-- △ ループ終了 △ -->
        <?php endwhile; ?>
        <?php if (function_exists("wp_pagenavi")) wp_pagenavi(array('query' => $my_query)); ?>
      <?php else : // 投稿がない場合 
      ?>
        <div class="propertye__no-post">
          <div class="propertye__no-post">
            <div class="news__movie"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/9Am0hn4nSGM?si=HRAp_4x-McdePNt4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
            <div class="news__movie"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/8xXPHZ8Kf_Q?si=GWQvNBIg1PlAUObV" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
            <!-- 現在新着情報はございません。 -->
          </div>
        </div>
      <?php endif;
      wp_reset_postdata(); ?>

    </div>

  </section>

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item"><a href="/news-list/">新着情報一覧</a></li>
      <li class="breadcrumb__item">新着情報(その他お知らせ)</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>