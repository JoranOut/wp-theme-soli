<?php
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function get_user_ical_hash($user_id = null){
  if($user_id){
    global $wpdb;
    $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id ) );
    if(empty($count ) || 1 > $count) return false;
    $hash = get_user_meta($user_id,"ical_hash",true);
    if(!$hash){
      $hash = random_str(16);
      update_user_meta($user_id,"ical_hash",$hash);
    }
    return $hash;
  }
}
?>
