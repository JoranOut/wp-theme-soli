<?php
$startTime = microtime(true);


$fp_info = get_soli_fp_info();
$image = wp_get_attachment_image_src($fp_info['frontpage_background'],'full');
global $myrows;
$myrowsquery = implode(", ",$myrows);
$myrowsquery = ($myrowsquery)? $myrowsquery : 0;
 ?>


<article class="extended-results">
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo $image[0] ?>)">
	</header>
	<div class="page-content">
		<?php get_search_form()?>
    <label class="lbl_toggle_fc" for="toggle_fc">filters</label>
    <input id="toggle_fc" name="toggle_fc" class="toggle_fc" type="checkbox"/>
    <div class="filter_container">
			<h3>Type</h3>
			<label class="<?php checkcheckbox("page") ?>" for="page">Pagina</label>
			<label class="<?php checkcheckbox("news") ?>" for="news">Nieuws</label>
			<label class="<?php checkcheckbox("event") ?>" for="event">Evenement</label>
      <?php if(is_user_logged_in()){?>
        <label class="<?php checkcheckbox("music") ?>" for="music">Muziek</label>
  			<label class="<?php checkcheckbox("mededeling") ?>" for="mededeling">Mededeling</label>
      <?php } else { ?>
        <p style="color:red;width: 100%;font-size: 1.4rem;">Log in om muziek en mededelingen te kunnen zoeken.</p>
      <?php } ?>
			<h3>Orkesten</h3>
			<label class="<?php checkcheckbox("Harmonie") ?>" for="Harmonie">Harmonie</label>
			<label class="<?php checkcheckbox("KleinOrkest") ?>" for="KleinOrkest">Klein Orkest</label>
			<label class="<?php checkcheckbox("Marsorkest") ?>" for="Marsorkest">Marsorkest</label>
			<label class="<?php checkcheckbox("Bigband") ?>" for="Bigband">Big band</label>
			<label class="<?php checkcheckbox("Opleidingsorkest") ?>" for="Opleidingsorkest">Opleidingsorkest</label>
			<label class="<?php checkcheckbox("Slagwerkgroep") ?>" for="Slagwerkgroep">Slagwerkgroep</label>
			<label class="<?php checkcheckbox("Opstapklas") ?>" for="Opstapklas">Opstapklas</label>
			<label class="<?php checkcheckbox("Samenspeelklas") ?>" for="Samenspeelklas">Samenspeelklas</label>
			<label class="<?php checkcheckbox("Volwassenopstapklas") ?>" for="Volwassenopstapklas">Volwassen opstapklas</label>
			<label class="<?php checkcheckbox("Twirlteam") ?>" for="Twirlteam">Twirlteam</label>
			<label class="<?php checkcheckbox("StilOrkest") ?>" for="StilOrkest">Stil Orkest</label>
			<label class="<?php checkcheckbox("Funband") ?>" for="Funband">Funband</label>
		</div>
		<div class="results">
    <?php
		$types = array();
		if(!empty($_POST['news'])) array_push($types, "post");
		if(!empty($_POST['page'])) array_push($types, "page");
		if(!empty($_POST['music'])) array_push($types, "music");
		if(!empty($_POST['mededeling'])) array_push($types, "mededelingen");

    $groups = array();
    if(!empty($_POST['Harmonie'])) array_push($groups, 8);
    if(!empty($_POST['KleinOrkest'])) array_push($groups, 5);
    if(!empty($_POST['Marsorkest'])) array_push($groups, 4);
    if(!empty($_POST['Bigband'])) array_push($groups, 23);
    if(!empty($_POST['Opleidingsorkest'])) array_push($groups, 11);
    if(!empty($_POST['Slagwerkgroep'])) array_push($groups, 18);
    if(!empty($_POST['Opstapklas'])) array_push($groups, 12);
    if(!empty($_POST['Samenspeelklas'])) array_push($groups, 14);
    if(!empty($_POST['Volwassenopstapklas'])) array_push($groups, 20);
    if(!empty($_POST['Twirlteam'])) array_push($groups, 10);
    if(!empty($_POST['StilOrkest'])) array_push($groups, 22);
    if(!empty($_POST['Funband'])) array_push($groups, 21);
    if(!empty($groups)) array_push($groups, 3);

    // Sanitize search term
    $search_term = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';

    if(!empty($groups)) echo "Niet gevonden wat je zocht? Verfijn je zoekopdracht of verwijder je filters.";

    // Build type condition safely
    $type_condition = '';
    if(!empty($types)) {
      $type_placeholders = array_fill(0, count($types), '%s');
      $type_condition = " AND p.post_type IN (" . implode(',', $type_placeholders) . ")";
    }

    // Build group condition safely (groups are already integers from our whitelist)
    $group_condition = '';
    if(!empty($groups)) {
      $group_placeholders = array_fill(0, count($groups), '%d');
      $group_condition = " AND a.group_id IN (" . implode(',', $group_placeholders) . ")";
    }

    // Sanitize myrowsquery - ensure all values are integers
    $myrows_safe = array_map('intval', (array)$myrows);
    $myrowsquery_safe = !empty($myrows_safe) ? implode(',', $myrows_safe) : '0';

    $searcheventquery = '';
    $query_params = array();

    $uam_active = soli_is_uam_active();

    if(!empty($_POST['event']) || empty($types)) {
      $uam_join_event = $uam_active ? "LEFT JOIN {$wpdb->prefix}uam_accessgroup_to_object a ON p.ID = a.object_id" : "";
      $searcheventquery = "
        SELECT DISTINCT
        p.ID, p.post_author, m.meta_value as post_date, p.post_date_gmt,
        p.post_content, p.post_title, p.post_excerpt, p.post_status,
        p.comment_status, p.ping_status, p.post_password, p.post_name,
        p.to_ping, p.pinged, p.post_modified, p.post_modified_gmt,
        p.post_content_filtered, p.post_parent, p.guid, p.menu_order,
        p.post_type, p.post_mime_type, p.comment_count
        FROM {$wpdb->prefix}posts p
        {$uam_join_event}
        INNER JOIN {$wpdb->prefix}postmeta m ON p.ID = m.post_id
        WHERE (p.post_title LIKE %s
        OR p.post_content LIKE %s)
        AND p.post_type = 'tribe_events'
        AND p.post_status = 'publish'
        AND m.meta_key = '_EventStartDate'
        AND p.ID NOT IN ({$myrowsquery_safe})";
      $query_params[] = '%' . $wpdb->esc_like($search_term) . '%';
      $query_params[] = '%' . $wpdb->esc_like($search_term) . '%';
      if(!empty($groups) && $uam_active) {
        $searcheventquery .= $group_condition;
        $query_params = array_merge($query_params, $groups);
      }
    }

    if((!empty($_POST['event'])) xor empty($types)) {
      $searcheventquery .= "
        UNION ";
    }

    if(!(!empty($_POST['event']) && empty($types))) {
      $uam_join_post = $uam_active ? "LEFT JOIN {$wpdb->prefix}uam_accessgroup_to_object a ON p.ID = a.object_id" : "";
      $searcheventquery .= "
        SELECT DISTINCT
        p.ID, p.post_author, p.post_date, p.post_date_gmt, p.post_content, p.post_title,
        p.post_excerpt, p.post_status, p.comment_status, p.ping_status,
        p.post_password, p.post_name, p.to_ping, p.pinged, p.post_modified,
        p.post_modified_gmt, p.post_content_filtered, p.post_parent, p.guid,
        p.menu_order, p.post_type, p.post_mime_type, p.comment_count
        FROM {$wpdb->prefix}posts p
        {$uam_join_post}
        WHERE (p.post_title LIKE %s
        OR p.post_content LIKE %s)
        AND p.post_status = 'publish'
        AND p.ID NOT IN ({$myrowsquery_safe})
        AND p.post_type != 'tribe_events'";
      $query_params[] = '%' . $wpdb->esc_like($search_term) . '%';
      $query_params[] = '%' . $wpdb->esc_like($search_term) . '%';
      if(!empty($types)) {
        $searcheventquery .= $type_condition;
        $query_params = array_merge($query_params, $types);
      }
      if(!empty($groups) && $uam_active) {
        $searcheventquery .= $group_condition;
        $query_params = array_merge($query_params, $groups);
      }
    }

    $searcheventquery .= "
      ORDER BY post_date DESC
      LIMIT 30";

    // Prepare and execute query safely
    if(!empty($query_params)) {
      $searcheventquery = $wpdb->prepare($searcheventquery, $query_params);
    }
    $eventposts = $wpdb->get_results($searcheventquery);

    if ( $eventposts ){
      foreach( $eventposts as $post ){
        setup_postdata($post);
        get_template_part('template-parts/item-search-excerpt');
      } wp_reset_postdata();
    } else {
      echo 'Geen pagina\'s gevonden met de zoekterm \''.esc_html($search_term).'\'';
    }
     ?>
		</div>
  </div>
</article>
