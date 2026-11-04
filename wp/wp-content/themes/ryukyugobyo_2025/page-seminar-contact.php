<?php get_header(); ?>

<main>
  <section class="contact fadeIn">
    <h1 class="page-title contact__title">お寺体験予約</h1>
    <!-- <p class="body-copy contact__entry">
      2025年12月・2026年1月の開催予定はありません。<br>
      次回は2026年2月開催予定です。
    </p> -->
	<?php if( get_field('体験タイトル') ):?>	
    <p class="body-copy contact__entry">
      <?php the_field('体験タイトル'); ?> <strong><?php the_field('体験開催日'); ?></strong><br />
    </p>
	<?php endif;?>
    <?php remove_filter('the_content', 'wpautop'); ?>
    <?php the_content(); ?>
    <p class="content-title">
      前日または当日の<br class="sp" />見学予約の場合は、<br
        class="sp" />お電話にてご予約ください。
    </p>
    <a href="tel:0120-846-940" class="link-button"><span>0120-846-940</span></a>
  </section>

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item">セミナー予約</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>