<?php

/**
 * カスタム投稿タイプ カスタム分類
 */
add_action('init', 'register_post_type_and_taxonomy');
function register_post_type_and_taxonomy()
{
  register_post_type(
    'news',
    array(
      'labels' => array(
        'name' => '新着情報',
        'add_new_item' => '新着情報を追加',
        'edit_item' => '新着情報の編集',
      ),
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
        'page-attributes',
      ),
    )
  );
  register_taxonomy(
    'news_category',
    'news',
    array(
      'labels' => array(
        'name' => 'カテゴリでさがす',
        'add_new_item' => 'カテゴリを追加',
        'edit_item' => 'カテゴリの編集',
      ),
      'hierarchical' => true,
      'show_admin_column' => true,
    )
  );
  register_post_type(
    'event',
    array(
      'labels' => array(
        'name' => 'キャンペーン・イベント',
        'add_new_item' => 'キャンペーン・イベントを追加',
        'edit_item' => 'キャンペーン・イベントの編集',
      ),
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
        'page-attributes',
      ),
    )
  );
  register_post_type(
    'feedback',
    array(
      'labels' => array(
        'name' => 'お客様からの声',
        'add_new_item' => 'お客様からの声を追加',
        'edit_item' => 'お客様からの声の編集',
      ),
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
        'page-attributes',
      ),
    )
  );
  register_post_type(
    'top_notices',
    array(
      'labels' => array(
        'name' => 'トップページ重要なお知らせ',
        'add_new_item' => 'トップページ重要なお知らせを追加',
        'edit_item' => 'トップページ重要なお知らせの編集',
      ),
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
        'page-attributes',
      ),
    )
  );
  register_taxonomy(
    'event_category',
    'event',
    array(
      'labels' => array(
        'name' => 'カテゴリー',
        // 'add_new_item' => 'カテゴリを追加',
        // 'edit_item' => 'カテゴリの編集',
      ),
      'hierarchical' => true,
      'show_admin_column' => true,
    )
  );
}

add_image_size('product_size01', 200, 200, array('center', 'center'));

// アイキャッチ画像を利用できるようにする
// add_theme_support('post-thumbnails');
