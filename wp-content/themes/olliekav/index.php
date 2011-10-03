<?php get_header(); ?>

	<?#php query_posts($query_string . '&cat=-80, -79')?>
	<?php query_posts('cat=-80, -79'); ?>
  	<?php if (have_posts()) : ?>

  		<?php while (have_posts()) : the_post(); ?>
  		<?php if( ($wp_query->current_post + 1) < ($wp_query->post_count) ) {
  			$lastpostclass = '';
  		} else {
  			$lastpostclass = 'last';
  		}?>

		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">

			<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

			<?php include (TEMPLATEPATH . '/_/inc/meta.php' ); ?>

			<div class="entry">
				<?php the_content(); ?>
			</div>

			<footer class="postmetadata">
				<?php the_tags('Tags: ', ', ', '<br />'); ?>
				Posted in <?php the_category(', ') ?> | 
				<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</footer>

		</article>

	<?php endwhile; ?>

	<?php include (TEMPLATEPATH . '/_/inc/nav.php' ); ?>

	<?php else : ?>

		<h2>Not Found</h2>

	<?php endif; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
