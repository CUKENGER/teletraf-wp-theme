<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $site_name = get_bloginfo('name'); // Название сайта
  $site_description = get_theme_mod('tgx_site_description', get_bloginfo('description')); // Глобальное описание из Customizer
  $site_keywords = get_theme_mod('tgx_site_keywords', 'монетизация, telegram, заработок, блог'); // Глобальные ключевые слова
  
  if (is_front_page() || is_home()) {
    $title = $site_name . ' | ' . $site_description;
    $description = $site_description ?: 'Блог о монетизации Telegram, заработке и развитии каналов';
    $keywords = $site_keywords;
  } elseif (is_single()) {
    $custom_title = get_post_meta(get_the_ID(), '_tgx_seo_title', true);
    $custom_description = get_post_meta(get_the_ID(), '_tgx_seo_description', true);
    $custom_keywords = get_post_meta(get_the_ID(), '_tgx_seo_keywords', true);
    $title = $custom_title ?: get_the_title() . ' | ' . $site_name;
    $description = $custom_description ?: wp_trim_words(get_the_excerpt(), 30, '...');
    $keywords = $custom_keywords ?: implode(', ', wp_list_pluck(get_the_category(), 'name')) ?: 'telegram, монетизация';
  } elseif (is_category()) {
    $category = get_queried_object();
    $category_description = get_term_meta($category->term_id, '_tgx_category_description', true);
    $category_keywords = get_term_meta($category->term_id, '_tgx_category_keywords', true);
    $title = $category->name . ' | ' . $site_name;
    $description = $category_description ?: $site_description;
    $keywords = $category_keywords ?: $site_keywords;
  } else {
    $title = wp_title('', false) . ' | ' . $site_name;
    $description = $site_description;
    $keywords = $site_keywords;
  }
  ?>
  <title><?php echo esc_html($title); ?></title>
  <meta name="description" content="<?php echo esc_attr($description); ?>">
  <meta name="keywords" content="<?php echo esc_attr($keywords); ?>">
  <?php if (get_site_icon_url()): ?>
    <link rel="icon" href="<?php echo esc_url(get_site_icon_url()); ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_site_icon_url(180)); ?>">
  <?php else: ?>
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/favicon.ico'); ?>"
      type="image/x-icon">
  <?php endif; ?>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="header-container">
    <div class="header-content">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo">
        <img src="<?php echo esc_url(get_theme_mod('tgx_logo', get_template_directory_uri() . '/assets/logo.svg')); ?>"
          alt="Logo">
      </a>
      <div class="header-right">
        <div class="header-search-container">
          <div class="header-search-wrapper">
            <?php
            $search_icon = get_theme_mod('tgx_search_icon', get_template_directory_uri() . '/assets/search-input.svg');
            $search_open_icon = get_theme_mod('tgx_search_icon', get_template_directory_uri() . '/assets/search.svg');
            $search_icon_path = str_replace(get_site_url(), ABSPATH, $search_icon);
            $search_open_icon_path = str_replace(get_site_url(), ABSPATH, $search_open_icon);
            if (file_exists($search_icon_path)) {
              $svg_content = file_get_contents($search_icon_path);
              echo $svg_content;
            } else {
              error_log('Search icon not found: ' . $search_icon_path);
              echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>';
            }
            ?>
            <input type="text" class="header-search" placeholder="Искать...">
            <button class="header-search-clear">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="header-search-results"></div>
        </div>
        <div class="search-icon-container">
          <?php
          if (file_exists($search_open_icon_path)) {
            $svg_content = file_get_contents($search_open_icon_path);
            echo $svg_content;
          } else {
            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>';
          }
          ?>
        </div>
        <a href="<?php echo esc_url(get_theme_mod('tgx_telegram_link', 'https://t.me/+OZZKJb_5At42NzJi')); ?>"
          class="tg-icon-container" target="_blank" rel="noopener noreferrer">
          <?php
          $telegram_icon = get_theme_mod('tgx_telegram_icon', get_template_directory_uri() . '/assets/tg-button.svg');
          $telegram_icon_path = str_replace(get_site_url(), ABSPATH, $telegram_icon);
          if (file_exists($telegram_icon_path)) {
            $svg_content = file_get_contents($telegram_icon_path);
            $svg_content = preg_replace('/(fill|stroke)="[^"]*"/i', 'fill="currentColor" stroke="none"', $svg_content);
            $svg_content = preg_replace('/(style|class|id)="[^"]*"/i', '', $svg_content);
            echo $svg_content;
          } else {
            error_log('Telegram icon not found: ' . $telegram_icon_path);
            echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 4.86 12.4c-.59-.19-.6.66-.01.86l2.87 1.37 1.06 3.02z"/></svg>';
          }
          ?>
        </a>
      </div>
    </div>
  </header>
  <!-- Модалка поиска для мобильных -->
  <div class="search-overlay">
    <div class="search-overlay-header">
      <div class="search-overlay-logo">
        <img src="<?php echo esc_url(get_theme_mod('tgx_logo', get_template_directory_uri() . '/assets/logo.svg')); ?>"
          alt="Logo">
      </div>
      <button class="search-overlay-close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="search-overlay-content">
      <form class="search-overlay-form">
        <div class="search-overlay-input-wrapper">
          <?php
          if (file_exists($search_icon_path)) {
            $svg_content = file_get_contents($search_icon_path);
            echo $svg_content;
          } else {
            error_log('Search icon not found: ' . $search_icon_path);
            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>';
          }
          ?>
          <input type="text" class="header-search search-overlay-input" placeholder="Поиск...">
          <button class="search-overlay-clear">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </form>
      <div class="search-overlay-results"></div>
    </div>
  </div>