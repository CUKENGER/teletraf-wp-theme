<?php get_header(); ?>
<main>
  <div class="container">
    <article class="single-post">
      <?php if (have_posts()):
        while (have_posts()):
          the_post(); ?>
          <header class="post-header">
            <div class="post-tags">
              <span><?php echo get_the_date('j F'); ?></span>
              <svg width="4" height="4" viewBox="0 0 4 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle opacity="0.5" cx="2" cy="2" r="2" fill="currentColor" />
              </svg>
              <a href="<?php
              $categories = get_the_category();
              $category = !empty($categories) ? $categories[0] : null;
              echo $category ? esc_url(get_category_link($category->term_id)) : esc_url(home_url('/category/prochee/'));
              ?>" class="post-tags-icon" aria-label="Перейти к категории <?php
              $category_name = $category ? esc_html($category->name) : 'Прочее';
              echo esc_attr($category_name);
              ?>">
                <?php
                $category_slug = $category ? $category->slug : 'prochee';
                $category_icons = tgh_get_category_icons();
                $icon_url = isset($category_icons[$category_slug]) ? $category_icons[$category_slug] : $category_icons['prochee'];
                $icon_path = str_replace(get_site_url(), ABSPATH, $icon_url);

                if (WP_DEBUG) {
                  error_log('Post: ' . get_the_title() . ', Category: ' . $category_name . ', Slug: ' . $category_slug . ', Icon: ' . $icon_url);
                }

                if (file_exists($icon_path)) {
                  $svg_content = file_get_contents($icon_path);
                  if ($svg_content && stripos($svg_content, '<svg') !== false) {
                    $svg_content = preg_replace('/<mask[^>]*>.*?<\/mask>/s', '', $svg_content);
                    $svg_content = preg_replace('/<g[^>]*mask="[^"]*"[^>]*>/', '<g>', $svg_content);
                    $svg_content = preg_replace('/fill="#15152A"/i', 'stroke="currentColor" fill="none"', $svg_content);
                    $svg_content = preg_replace('/(id|class|style)="[^"]*"/i', '', $svg_content);
                    $svg_content = preg_replace('/stroke="[^"]*"/i', 'stroke="currentColor"', $svg_content);
                    if (!preg_match('/viewBox="[^"]*"/i', $svg_content)) {
                      $svg_content = preg_replace('/<svg/i', '<svg viewBox="0 0 24 24"', $svg_content);
                    }
                    $svg_content = preg_replace('/<svg/i', '<svg role="img" aria-hidden="true"', $svg_content);
                    echo '<span class="post-category-icon">' . $svg_content . '</span>';
                  } else {
                    if (WP_DEBUG) {
                      error_log('Invalid SVG for category: ' . $category_name);
                    }
                  }
                } else {
                  if (WP_DEBUG) {
                    error_log('SVG not found: ' . $icon_path);
                  }
                }
                ?>
                <span class="post-category-name"><?php echo $category_name; ?></span>
              </a>
            </div>
            <h1 class="single-post-title"><?php the_title(); ?></h1>
            <div class="post-author">
              <span
                class="post-author-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 30, 'mystery', 'Аватар пользователя'); ?></span>
              <span class="post-author-name"><?php the_author(); ?></span>
            </div>
          </header>
          <div class="post-cover">
            <?php if (has_post_thumbnail()): ?>
              <?php the_post_thumbnail('large', [
                'alt' => get_the_title(),
                'class' => 'post-cover-image',
                'loading' => 'lazy'
              ]); ?>
            <?php else: ?>
              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/post.png'); ?>" alt="post"
                class="post-cover-image" loading="lazy">
            <?php endif; ?>
          </div>
          <div class="post-content">
            <?php the_content(); ?>
            <?php
            echo do_shortcode('[banner_pulse]');
            ?>
          </div>
          <?php if (get_field('external_source')): ?>
            <div class="external-links">
              <p>Источник: <a href="<?php echo esc_url(get_field('external_source')); ?>" target="_blank"
                  class="source-link">Перейти</a></p>
            </div>
          <?php endif; ?>
        <?php endwhile; ?>
        <!-- Секция похожих постов -->
        <?php
        $current_post_id = get_the_ID();
        $related_posts = get_field('related_posts');
        $related_titles = [];
        $args = [];
        $posts_needed = 2;
        $found_posts = 0;

        // Шаг 1: Проверяем ACF-поле related_posts
        if ($related_posts && is_array($related_posts) && count($related_posts) > 0) {
          $args = [
            'post_type' => 'post',
            'post__in' => array_slice($related_posts, 0, $posts_needed),
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'orderby' => 'post__in',
          ];
          $related_query = new WP_Query($args);
          $found_posts = $related_query->post_count;
          wp_reset_postdata();
        }

        // Шаг 2: Если недостаточно постов из ACF, ищем по категориям
        if ($found_posts < $posts_needed) {
          $categories = get_the_category();
          $category_ids = !empty($categories) ? wp_list_pluck($categories, 'term_id') : [];
          if (!empty($category_ids)) {
            $args = [
              'post_type' => 'post',
              'posts_per_page' => $posts_needed - $found_posts,
              'post__not_in' => [$current_post_id],
              'category__in' => $category_ids,
              'post_status' => 'publish',
              'ignore_sticky_posts' => true,
            ];
            $category_query = new WP_Query($args);
            $found_posts = $category_query->post_count;
            wp_reset_postdata();
            // Если в категориях 0 постов, очищаем args для шага 3
            if ($found_posts == 0) {
              $args = [];
            }
          }
        }

        // Шаг 3: Если в категориях 0 постов и найдено меньше 2 постов, берем любые
        if ($found_posts < $posts_needed && empty($args)) {
          $args = [
            'post_type' => 'post',
            'posts_per_page' => $posts_needed - $found_posts,
            'post__not_in' => [$current_post_id],
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
          ];
        }

        // Финальный запрос
        $related_query = new WP_Query($args);
        if (WP_DEBUG) {
          error_log('Related posts query for post ID ' . $current_post_id . ': ' . print_r($args, true));
        }

        if ($related_query->have_posts()): ?>
          <div class='related-post-wrapper'>
            <section class="related-posts" aria-labelledby="related-posts-title">
              <div class="related-posts__container">
                <h2 id="related-posts-title" class='category-title'>Похожие статьи</h2>
                <div class="posts">
                  <?php
                  while ($related_query->have_posts()):
                    $related_query->the_post();
                    $related_titles[] = get_the_title();
                    get_template_part('template-parts/post');
                  endwhile;
                  if (WP_DEBUG) {
                    error_log('Related posts rendered for post ID ' . $current_post_id . ': ' . implode(', ', $related_titles));
                  }
                  ?>
                </div>
              </div>
            </section>
          </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
      <?php else: ?>
        <p>Пост не найден.</p>
      <?php endif; ?>
    </article>
  </div>
</main>
<?php get_footer(); ?>