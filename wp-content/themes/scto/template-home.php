<?php
/*
 * Template Name: Template Home
 * =============================
 * */

get_header();
?>
<div class="wrap">
    <div id="primary" class="content-area">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php
                    the_content();
                    ?>
                </div><!-- .entry-content -->
            </article><!-- #post-## -->
            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
        endwhile; // End of the loop.
        ?>

    </div><!-- #primary -->
</div><!-- .wrap -->
<?php
get_footer();
?>
