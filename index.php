<?php get_header(); ?>
<main>
  <div class="container">
    <!-- Секция категорий -->
    <?php
    error_log('Loading template-parts/categories');
    echo '<!-- Loading template-parts/categories -->';
    get_template_part('template-parts/categories');
    ?>

    <!-- Посты по всем категориям -->
    <section class='category-list'>
      <?php
      // wp_cache_flush();
      error_log('Cache flushed');

      $categories = get_categories(array(
        'hide_empty' => true,
        'orderby' => 'term_order',
        'order' => 'ASC',
      ));

      error_log('Categories found: ' . count($categories) . ', Slugs: ' . implode(', ', wp_list_pluck($categories, 'slug')));
      echo '<!-- Categories found: ' . count($categories) . ', Slugs: ' . esc_html(implode(', ', wp_list_pluck($categories, 'slug'))) . ' -->';

      if (empty($categories)) {
        error_log('No categories found');
        echo '<!-- No categories found -->';
        ?>
        <p>Нет категорий с постами.</p>
        <?php
      }

      foreach ($categories as $category_obj) {
        $args = array(
          'posts_per_page' => 3,
          'category_name' => $category_obj->slug,
          'post_status' => 'publish',
          'no_found_rows' => true,
        );

        error_log('Query args for ' . $category_obj->name . ': ' . var_export($args, true));
        echo '<!-- Query args for ' . esc_html($category_obj->name) . ': ' . var_export($args, true) . ' -->';

        $query = new WP_Query($args);
        error_log('SQL for ' . $category_obj->name . ': ' . $query->request);
        $post_count = $query->post_count;

        if ($post_count === 0) {
          error_log('Category skipped: ' . $category_obj->name . ', Post count: ' . $post_count . ', Slug: ' . $category_obj->slug);
          echo '<!-- Category skipped: ' . esc_html($category_obj->name) . ', Post count: ' . $post_count . ', Slug: ' . esc_html($category_obj->slug) . ' -->';
          wp_reset_postdata();
          continue;
        }

        error_log('Rendering category: ' . $category_obj->name . ', Slug: ' . $category_obj->slug . ', Post count: ' . $post_count);
        echo '<!-- Rendering category: ' . esc_html($category_obj->name) . ', Slug: ' . esc_html($category_obj->slug) . ', Post count: ' . $post_count . ' -->';
        ?>
        <div class="category">
          <div class="category-header">
            <h2 class="category-title"><?php echo esc_html($category_obj->name); ?></h2>
            <?php
            $count_args = array(
              'posts_per_page' => -1,
              'category_name' => $category_obj->slug,
              'post_status' => 'publish',
              'no_found_rows' => false,
            );
            $count_query = new WP_Query($count_args);
            $total_posts = $count_query->post_count;
            wp_reset_postdata();

            if ($total_posts > 3): ?>
              <a href="<?php echo esc_url(get_category_link($category_obj->term_id)); ?>" class="category-btn">
                <span>Все</span>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <mask id="mask0_2075_1612" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="8"
                    height="14">
                    <rect width="8" height="14" fill="#808080" />
                  </mask>
                  <g mask="url(#mask0_2075_1612)">
                    <path
                      d="M1.37474 1.21858C1.77895 0.895216 2.35761 0.937141 2.71263 1.29866L2.78099 1.37483L6.78099 6.37483C7.07316 6.74005 7.07316 7.25961 6.78099 7.62483L2.78099 12.6248C2.43597 13.056 1.80598 13.1261 1.37474 12.7811C0.943797 12.436 0.873625 11.806 1.21849 11.3748L4.71849 6.99983L1.21849 2.62483L1.15892 2.54183C0.884022 2.11614 0.970583 1.54202 1.37474 1.21858Z"
                      fill="#2288EE" />
                  </g>
                </svg>
              </a>
            <?php endif; ?>
          </div>
          <?php if ($query->have_posts()): ?>
            <section class="posts">
              <?php
              $post_titles = [];
              while ($query->have_posts()):
                $query->the_post();
                $post_titles[] = get_the_title();
                get_template_part('template-parts/post'); // Вызов шаблона поста
              endwhile;
              ?>
            </section>
            <?php
            error_log('Posts rendered for ' . $category_obj->name . ': ' . implode(', ', $post_titles));
            echo '<!-- Posts rendered for ' . esc_html($category_obj->name) . ': ' . esc_html(implode(', ', $post_titles)) . ' -->';
          else:
            error_log('No posts found for category: ' . $category_obj->name . ', Slug: ' . $category_obj->slug);
            echo '<!-- No posts found for category: ' . esc_html($category_obj->name) . ', Slug: ' . esc_html($category_obj->slug) . ' -->';
            ?>
            <p>Постов пока нет.</p>
            <?php
          endif;
          wp_reset_postdata();
          ?>
        </div>
        <?php
      }
      ?>
    </section>
  </div>
</main>
<?php get_footer(); ?>