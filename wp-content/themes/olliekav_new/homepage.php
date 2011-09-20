<?php
/*
Template Name: Home Page
*/
?>

<?php get_header(); ?>

<?php if (have_posts()) : ?>
<?php query_posts('cat=80&posts_per_page=-1'); ?>
  <section id="latest_work" class="clearfix">
    <?php $count = 0;
      while (have_posts()) : the_post();
      $count++;
      if( $count == 3 ) $style = 'last';
      else $style = ''; ?>
      <article <?php post_class($style) ?> id="post-<?php the_ID(); ?>">
        <?php get_the_image( array( 'meta_key' => array( 'Medium', 'medium' ), 'size' => 'medium', 'image_scan' => true ) ); ?>
        <div>
          <h2><?php the_title(); ?></h2>
          <p><?php the_excerpt_reloaded(100, FALSE, 'none', TRUE, 'read more &raquo;', FALSE, 2); ?></p>
        </div>
      </article>
    <?php endwhile; ?>
  </section>
<?php endif ?>

<?php get_footer(); ?>