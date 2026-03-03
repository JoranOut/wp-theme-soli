<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */

setup_postdata( $post );

$is_event = ( 'tribe_events' === get_post_type() );

/**
 * The Events Calendar may be inactive (especially in multisite),
 * so guard plugin function calls.
 */
$is_uitgaanstip = false;
if ( $is_event && function_exists( 'tribe_event_in_category' ) ) {
    $is_uitgaanstip = (bool) tribe_event_in_category( 'uitgaanstip' );
}

$classes = $is_uitgaanstip ? 'post-excerpt-template tip' : 'post-excerpt-template';
?>

<section id="post-<?php the_ID(); ?>"
    <?php post_class( $classes ); ?>
    onclick="window.location.href='<?php echo esc_url( get_permalink() ); ?>'">

    <div class="date" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
        url(<?php echo esc_url( get_soli_post_image( $post, 'medium' ) ); ?>)">

        <div>
            <?php
            if ( 'post' === get_post_type() ) :
                wrap_element( get_the_time( 'D' ), 'strong' );
                wrap_element( get_the_time( 'd.m.y' ), 'em' );

            elseif ( $is_event ) :
                $raw_event_date = isset( $post->event_date ) ? $post->event_date : get_post_meta( $post->ID, '_EventStartDate', true );
                $date_ts = $raw_event_date ? strtotime( $raw_event_date ) : false;

                if ( $date_ts ) {
                    wrap_element( date( 'D', $date_ts ), 'strong' );
                    wrap_element( date( 'd.m.y', $date_ts ), 'em' );
                }
            endif;
            ?>
        </div>
    </div>

    <div class="content">
        <?php
        if ( $is_uitgaanstip ) {
            echo '<h3>Uitgaanstip!</h3>';
        }
        the_title( '<h2 class="entry-title">', '</h2>' );
        ?>

        <div class="entry-content">
            <?php
            if ( 'post' === get_post_type() || 'page' === get_post_type() ) :
                echo esc_html( wp_strip_all_tags( get_the_excerpt() ) );
            elseif ( $is_event ) :
                limit_excerpt( wp_strip_all_tags( get_post()->post_content ), 160 );
            endif;
            ?>
        </div>

        <a href="<?php echo esc_url( get_permalink() ); ?>">lees meer</a>
    </div>
</section>
