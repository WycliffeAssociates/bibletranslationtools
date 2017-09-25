<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="sl-kiwi-tab-socialIdentity <?php echo ( $hash === 'sl-kiwi-tab-socialIdentity' ) ? 'epsilon-tab-active' : ''; ?>">
	<h2>
		<span><?php echo esc_html__( 'Social identities', 'kiwi-social-share' ) ?></span>
	</h2>
	<div class="clearfix">
		<label
			for="kiwi-twitter-username"> <?php echo esc_html__( 'Twitter Username', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-twitter-username"
		       name="kiwi_social_identities[twitter_username]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'twitter_username', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label
			for="kiwi-facebook-page-url"> <?php echo esc_html__( 'Facebook Page Url', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-facebook-page-url" name="kiwi_social_identities[facebook_page_url]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_page_url', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
	<div class="clearfix">
		<label for="kiwi-facebook-app-id"> <?php echo esc_html__( 'Facebook App Id', 'kiwi-social-share' ); ?> </label>
		<input type="text" id="kiwi-facebook-app-id" name="kiwi_social_identities[facebook_app_id]"
		       value="<?php echo esc_attr( Kiwi_Social_Share_Helper::get_setting_value( 'facebook_app_id', '', 'kiwi_social_identities' ) ); ?>"/>
	</div>
</div>