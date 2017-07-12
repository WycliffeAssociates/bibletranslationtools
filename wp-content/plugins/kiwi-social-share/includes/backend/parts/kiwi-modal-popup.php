<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="kiwi-modal fade">
    <div class="kiwi-modal-content">
        <span class="close-modal"><i class="kicon-times"></i></span>
        <header class="modal-header">
            <div class="modal-header-content">
                <h3 class="modal-title"><?php echo esc_html__( 'Get your premium version now!', 'kiwi-social-share' ); ?></h3>
                <span class="modal-subtitle"><?php echo esc_html__( 'Take advantage of the large number of professional features anad take
 your business one step further!', 'kiwi-social-share' ); ?></span>
            </div>
        </header>
        <section class="modal-content" id="page-one">
            <div class="price-box text-center">
                <span class="price"><span class="currency">$</span>19</span>
                <a href="#" class="button-modal"
                   data-action="modal-buy-now"><?php echo esc_html__( 'Buy Now', 'kiwi-social-share' ); ?></a>
            </div>
        </section>

        <section class="modal-content form" id="page-two">
			<?php $user = wp_get_current_user(); ?>
            <div class="clearfix">
                <div class="half">
                    <label
                            for="kiwi_product_upsell_first_name"> <?php echo esc_html__( 'First name', 'kiwi-social-share' ); ?> </label>
                    <input type="text" value="<?php echo esc_attr( $user->user_firstname ) ?>"
                           id="kiwi_product_upsell_first_name" name="kiwi_product_upsell[first_name]"/>
                </div>
                <div class="half">
                    <label
                            for="kiwi_product_upsell_last_name"> <?php echo esc_html__( 'Last name', 'kiwi-social-share' ); ?> </label>
                    <input type="text" value="<?php echo esc_attr( $user->user_lastname ) ?>"
                           id="kiwi_product_upsell_last_name" name="kiwi_product_upsell[last_name]"/>
                </div>
            </div>

            <div class="clearfix">
                <label
                        for="kiwi_product_upsell_email"> <?php echo esc_html__( 'Email where we can send the invoice to.', 'kiwi-social-share' ); ?> </label>
                <input type="email" value="<?php echo esc_attr( $user->user_email ) ?>" id="kiwi_product_upsell_email"
                       name="kiwi_product_upsell[email]"/>
            </div>

            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" checked id="kiwi_product_upsell_type_a"
                       name="kiwi_product_upsell[type]"
                       value="consumer"/>

                <strong></strong>
				<?php echo esc_html__( 'Consumer', 'kiwi-social-share' ); ?>
            </label>
            <label class="epsilon-ui-radio ui-radio-inline">
                <input type="radio" id="kiwi_product_upsell_type_b"
                       name="kiwi_product_upsell[type]"
                       value="business"/>

                <strong></strong>
				<?php echo esc_html__( 'Business', 'kiwi-social-share' ); ?>
            </label>
            <div class="company-group" style="display:none">
                <div class="clearfix">
                    <label
                            for="kiwi_product_upsell_company_name"> <?php echo esc_html__( 'Company name', 'kiwi-social-share' ); ?> </label>
                    <input type="text" id="kiwi_product_upsell_company_name" name="kiwi_product_upsell[company_name]"/>
                </div>
                <div class="clearfix">
                    <label
                            for="kiwi_product_upsell_vat"> <?php echo esc_html__( 'VAT', 'kiwi-social-share' ); ?> </label>
                    <input type="text" id="kiwi_product_upsell_vat" name="kiwi_product_upsell[vat]"/>
                </div>
            </div>

            <input type="hidden" id="kiwi_product_upsell_product" name="kiwi_product_upsell[product]" value="150641"/>
            <a href="#" class="button-modal"
               data-action="continue-to-checkout"><?php echo esc_html__( 'Continue with secure checkout', 'kiwi-social-share' ); ?></a>
        </section>
        <footer class="modal-footer text-center">
            <div class="row">
                <div class="col-md-12">
                    <a target="_blank"
                       href="https://machothemes.com/plugin/kiwi-pro/?utm_source=worg&utm_medium=kiwi-modal-page&utm_campaign=upsell"><?php echo esc_html__( 'Click here to see all the pro features', 'kiwi-social-share' ); ?></a>
                    <p class="muted"><?php echo esc_html__( 'No contract. No hassle. You can cancel your subscription at anytime
					without any cancellation period.', 'kiwi-social-share' ); ?></p>
                </div>
            </div>
        </footer>
    </div>
</div>
