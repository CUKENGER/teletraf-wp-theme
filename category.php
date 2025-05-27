<?php get_header(); ?>
<main>
	<div class="container">
		<!-- Секция категорий -->
		<?php get_template_part('template-parts/categories'); ?>

		<!-- Посты текущей категории -->
		<section class="category">
			<div class="category-header">
				<?php
				$current_cat = get_queried_object();
				error_log('Current category: ' . $current_cat->name . ', Slug: ' . $current_cat->slug);
				echo '<!-- Current category: ' . esc_html($current_cat->name) . ', Slug: ' . esc_html($current_cat->slug) . ' -->';
				?>
				<h2 class="category-title"><?php echo esc_html($current_cat->name); ?></h2>
			</div>
			<?php if (have_posts()): ?>
				<section class="category-posts">
					<?php
					$post_titles = [];
					while (have_posts()):
						the_post();
						$post_titles[] = get_the_title();
						get_template_part('template-parts/post');
					endwhile;
					error_log('Posts rendered for category ' . $current_cat->name . ': ' . implode(', ', $post_titles));
					echo '<!-- Posts rendered for category ' . esc_html($current_cat->name) . ': ' . esc_html(implode(', ', $post_titles)) . ' -->';
					?>
				</section>
			<?php else: ?>
				<p>Постов пока нет.</p>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php get_footer(); ?>