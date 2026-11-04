  <?php get_header(); ?>
  <main>
    <section class="post fadeIn">
      <div class="post__tags">
        <!-- <span class="post__tag">カテゴリ</span>
        <time class="post__tag">投稿日時：0000年00月00日</time> -->
      </div>

      <h1 class="post__title">
        <?php the_title(); ?>
      </h1>

      <div class="post__wrapper fadeInUp">
        <div class="fadeInUp">
          <?php the_content(); ?>
          <div class="sns__container post-details__social fadeInUp">
            <button class="action-button" onclick="likeArticle()">
              <?php echo do_shortcode('[posts_like_dislike]'); ?>
            </button>
            <a
              href="https://twitter.com/intent/tweet?text=ぜひご覧ください！ <?php echo rawurlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>"
              class="social-item"
              target="_blank">
              <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-x.svg" alt="Xポスト" class="social-item__icon" />
              <span class="social-item__text">ポスト</span>
            </a>
            <a
              href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>"
              target="_blank"
              class="social-item">
              <img src="<?php echo get_template_directory_uri(); ?>/images/common/icon-line-white.svg" alt="LINEで送る" class="social-item__icon" />
              <span class="social-item__text">LINEで送る</span>
            </a>
          </div>
          <div class="post__navigation">
            <?php
            $prev = get_previous_post_link('%link', '<span>前のページへ</span>');
            if ($prev) echo str_replace('<a ', '<a class="navigation-button navigation-button-p" ', $prev);
            ?>
            <?php
            $next = get_next_post_link('%link', '<span>次のページへ</span>');
            if ($next) echo str_replace('<a ', '<a class="navigation-button navigation-button-n" ', $next);
            ?>
          </div>
        </div>
      </div>
    </section>

    <div class="breadcrumb">
      <ul class="breadcrumb__list">
        <li class="breadcrumb__item">
          <a href="/" aria-label="ホームページに戻る">TOP</a>
        </li>
        <!-- この部分は親ページによって変わります。 -->
        <li class="breadcrumb__item">
          <a href="/feedback-list/">お客様からの声</a>
        </li>
        <li class="breadcrumb__item">記事詳細ページ</li>
      </ul>
    </div>
  </main>
  <?php get_footer(); ?>