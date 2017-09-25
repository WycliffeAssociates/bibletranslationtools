<div<?php FLBuilder::render_module_attributes( $module ); ?>>
	<div class="fl-module-content fl-node-content">
		<?php ob_start();
		include $module->dir . 'includes/frontend.php';

		$out = ob_get_clean();

		echo apply_filters( 'fl_builder_render_module_content', $out, $module );
		?>
	</div>
</div>
