<div class="wp-block wp-block-kubio-query-layout  position-relative wp-block-kubio-query-layout__outer atelier-wp-index__k__1MCYzfcZN-outer atelier-wp-local-365-outer d-flex h-section-global-spacing align-items-lg-center align-items-md-center align-items-center" data-kubio="kubio/query-layout" id="blog-layout">
	<div class="position-relative wp-block-kubio-query-layout__inner atelier-wp-index__k__1MCYzfcZN-inner atelier-wp-local-365-inner h-section-grid-container h-section-boxed-container">
		<div class="wp-block wp-block-kubio-row  position-relative wp-block-kubio-row__container atelier-wp-index__k__baLWB4dRKjp-container atelier-wp-local-366-container gutters-row-lg-2 gutters-row-v-lg-0 gutters-row-md-0 gutters-row-v-md-0 gutters-row-0 gutters-row-v-0" data-kubio="kubio/row">
			<div class="position-relative wp-block-kubio-row__inner atelier-wp-index__k__baLWB4dRKjp-inner atelier-wp-local-366-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-2 gutters-col-v-lg-0 gutters-col-md-0 gutters-col-v-md-0 gutters-col-0 gutters-col-v-0">
				<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container atelier-wp-index__k__kxeqsSpdy-n-container atelier-wp-local-367-container d-flex h-col-lg h-col-md h-col-auto" data-kubio="kubio/column">
					<div class="position-relative wp-block-kubio-column__inner atelier-wp-index__k__kxeqsSpdy-n-inner atelier-wp-local-367-inner d-flex h-flex-basis h-px-lg-2 v-inner-lg-0 h-px-md-2 v-inner-md-0 h-px-2 v-inner-0">
						<div class="position-relative wp-block-kubio-column__align atelier-wp-index__k__kxeqsSpdy-n-align atelier-wp-local-367-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-start align-self-md-start align-self-start">
							<div class="wp-block wp-block-kubio-query  position-relative wp-block-kubio-query__container atelier-wp-index__k__CtKC_EuIZL-container atelier-wp-local-368-container" data-kubio="kubio/query">
								<div class="wp-block wp-block-kubio-query-loop  position-relative wp-block-kubio-query-loop__container atelier-wp-index__k__vrf0UGkWrN-container atelier-wp-local-369-container gutters-row-lg-2 gutters-row-v-lg-2 gutters-row-md-2 gutters-row-v-md-2 gutters-row-0 gutters-row-v-2" data-kubio="kubio/query-loop" data-kubio-component="masonry" data-kubio-settings="{&quot;enabled&quot;:&quot;1&quot;,&quot;targetSelector&quot;:&quot;.wp-block-kubio-query-loop__inner&quot;}">
									<div class="position-relative wp-block-kubio-query-loop__inner atelier-wp-index__k__vrf0UGkWrN-inner atelier-wp-local-369-inner h-row">
										<?php atelier_wp_theme()->get('archive-loop')->render(array (
  'view' => 'content/index/loop-item',
)); ?>
									</div>
								</div>
								<?php if(atelier_wp_has_pagination()): ?>
								<div class="wp-block wp-block-kubio-query-pagination  position-relative wp-block-kubio-query-pagination__container atelier-wp-index__k__vD7AVCTELY-container atelier-wp-local-376-container gutters-row-lg-2 gutters-row-v-lg-2 gutters-row-md-2 gutters-row-v-md-2 gutters-row-0 gutters-row-v-2" data-kubio="kubio/query-pagination">
									<div class="position-relative wp-block-kubio-query-pagination__inner atelier-wp-index__k__vD7AVCTELY-inner atelier-wp-local-376-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-2 gutters-col-v-lg-2 gutters-col-md-2 gutters-col-v-md-2 gutters-col-0 gutters-col-v-2">
										<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container atelier-wp-index__k__tBYU0uM8Xx-container atelier-wp-local-377-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
											<div class="position-relative wp-block-kubio-column__inner atelier-wp-index__k__tBYU0uM8Xx-inner atelier-wp-local-377-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-2 h-px-md-0 v-inner-md-2 h-px-0 v-inner-2">
												<div class="position-relative wp-block-kubio-column__align atelier-wp-index__k__tBYU0uM8Xx-align atelier-wp-local-377-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
													<?php if(atelier_wp_has_pagination_button(true)): ?>
													<div class="position-relative wp-block-kubio-pagination-nav-button__spacing atelier-wp-index__k__ELgmeRXRD--spacing atelier-wp-local-378-spacing">
														<span class="wp-block wp-block-kubio-pagination-nav-button  position-relative wp-block-kubio-pagination-nav-button__outer atelier-wp-index__k__ELgmeRXRD--outer atelier-wp-local-378-outer kubio-button-container" data-kubio="kubio/pagination-nav-button">
															<a class="position-relative wp-block-kubio-pagination-nav-button__link atelier-wp-index__k__ELgmeRXRD--link atelier-wp-local-378-link h-w-100 h-global-transition" href="<?php atelier_wp_get_navigation_button_link(true); ?>">
																<span class="position-relative wp-block-kubio-pagination-nav-button__text atelier-wp-index__k__ELgmeRXRD--text atelier-wp-local-378-text kubio-inherit-typography">
																	<?php esc_html_e('Previous', 'atelier-wp'); ?>
																</span>
															</a>
														</span>
													</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
										<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container atelier-wp-index__k__P2OarhUKUK-container atelier-wp-local-379-container d-flex h-col-lg h-col-md h-col" data-kubio="kubio/column">
											<div class="position-relative wp-block-kubio-column__inner atelier-wp-index__k__P2OarhUKUK-inner atelier-wp-local-379-inner d-flex h-flex-basis h-px-lg-2 v-inner-lg-2 h-px-md-2 v-inner-md-2 h-px-1 v-inner-2">
												<div class="position-relative wp-block-kubio-column__align atelier-wp-index__k__P2OarhUKUK-align atelier-wp-local-379-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-center align-self-md-center align-self-center">
													<div class="wp-block wp-block-kubio-pagination-numbers  position-relative wp-block-kubio-pagination-numbers__outer atelier-wp-index__k__tRiQFlrj8q-outer atelier-wp-local-380-outer" data-kubio="kubio/pagination-numbers">
														<?php atelier_wp_pagination_numbers(); ?>
													</div>
												</div>
											</div>
										</div>
										<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container atelier-wp-index__k__2nd5yuWmz9-container atelier-wp-local-381-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
											<div class="position-relative wp-block-kubio-column__inner atelier-wp-index__k__2nd5yuWmz9-inner atelier-wp-local-381-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-2 h-px-md-0 v-inner-md-2 h-px-0 v-inner-2">
												<div class="position-relative wp-block-kubio-column__align atelier-wp-index__k__2nd5yuWmz9-align atelier-wp-local-381-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
													<?php if(atelier_wp_has_pagination_button()): ?>
													<div class="position-relative wp-block-kubio-pagination-nav-button__spacing atelier-wp-index__k__ELgmeRXRD--spacing atelier-wp-local-382-spacing">
														<span class="wp-block wp-block-kubio-pagination-nav-button  position-relative wp-block-kubio-pagination-nav-button__outer atelier-wp-index__k__ELgmeRXRD--outer atelier-wp-local-382-outer kubio-button-container" data-kubio="kubio/pagination-nav-button">
															<a class="position-relative wp-block-kubio-pagination-nav-button__link atelier-wp-index__k__ELgmeRXRD--link atelier-wp-local-382-link h-w-100 h-global-transition" href="<?php atelier_wp_get_navigation_button_link(); ?>">
																<span class="position-relative wp-block-kubio-pagination-nav-button__text atelier-wp-index__k__ELgmeRXRD--text atelier-wp-local-382-text kubio-inherit-typography">
																	<?php esc_html_e('Next', 'atelier-wp'); ?>
																</span>
															</a>
														</span>
													</div>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
