<?php get_header(); ?>
<main>
  <section class="contact fadeIn">
    <h1 class="page-title contact__title">お問い合わせフォーム 完了</h1>

    <?php remove_filter('the_content', 'wpautop'); ?>
    <?php the_content(); ?>
  </section>

  <div class="breadcrumb">
    <ul class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/" aria-label="ホームページに戻る">TOP</a>
      </li>
      <li class="breadcrumb__item">お問い合わせフォーム 完了</li>
    </ul>
  </div>
</main>
<?php get_footer(); ?>