<li class="wp-block wp-block-kubio-iconlistitem  position-relative wp-block-kubio-iconlistitem__item atelier-wp-front-header__k__jcIWO_UNs6K-item atelier-wp-local-134-item" data-kubio="kubio/iconlistitem">
	<?php $link = \ColibriWP\Theme\View::getData( 'link_value' ); ?>
	<?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>"><?php endif; ?>
	<div class="position-relative wp-block-kubio-iconlistitem__text-wrapper atelier-wp-front-header__k__jcIWO_UNs6K-text-wrapper atelier-wp-local-134-text-wrapper">
		<span class="h-svg-icon wp-block-kubio-iconlistitem__icon atelier-wp-front-header__k__jcIWO_UNs6K-icon atelier-wp-local-134-icon" name="icons8-line-awesome/envelope-o">
			<?php $icon = \ColibriWP\Theme\View::getData('icon'); if (isset($icon['content'])) echo $icon['content'] ?>
		</span>
		<span class="position-relative wp-block-kubio-iconlistitem__text atelier-wp-front-header__k__jcIWO_UNs6K-text atelier-wp-local-134-text">
			<?php echo esc_html(\ColibriWP\Theme\View::getData('text')); ?>
		</span>
	</div>
	<?php if ( $link ) : ?></a><?php endif; ?>
	<div class="last-el-spacer position-relative wp-block-kubio-iconlistitem__divider-wrapper atelier-wp-front-header__k__jcIWO_UNs6K-divider-wrapper atelier-wp-local-134-divider-wrapper"></div>
	<div class="position-relative wp-block-kubio-iconlistitem__divider-wrapper atelier-wp-front-header__k__jcIWO_UNs6K-divider-wrapper atelier-wp-local-134-divider-wrapper"></div>
</li>
