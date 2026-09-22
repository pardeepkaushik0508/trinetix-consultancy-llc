<?php
/**
 * Media field helpers and admin enqueue.
 *
 * @package Trinetix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue admin media scripts for settings and meta boxes.
 *
 * @param string $hook Current admin page.
 */
function trinetix_admin_media_enqueue( string $hook ): void {
	$screens = array( 'post.php', 'post-new.php', 'toplevel_page_trinetix-settings' );
	if ( ! in_array( $hook, $screens, true ) && false === strpos( $hook, 'trinetix-settings' ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'trinetix-admin',
		trinetix_asset_uri( 'assets/css/editor.css' ),
		array(),
		trinetix_asset_version( 'assets/css/editor.css' )
	);

	$js = <<<'JS'
(function($){
  function bindMedia(button, input, preview, type){
    $(document).on('click', button, function(e){
      e.preventDefault();
      var frame = wp.media({
        title: 'Select media',
        button: { text: 'Use this media' },
        library: type ? { type: type } : undefined,
        multiple: false
      });
      frame.on('select', function(){
        var attachment = frame.state().get('selection').first().toJSON();
        $(input).val(attachment.id).trigger('change');
        if (preview) {
          var url = attachment.url;
          if (attachment.type === 'image') {
            $(preview).html('<img src="'+url+'" alt="" style="max-width:180px;height:auto;" />');
          } else {
            $(preview).html('<span>'+url+'</span>');
          }
        }
      });
      frame.open();
    });
  }
  bindMedia('.trinetix-media-upload', null, null, null);
  $(document).on('click', '.trinetix-media-upload', function(e){
    e.preventDefault();
    var $btn = $(this);
    var target = $btn.data('target');
    var preview = $btn.data('preview');
    var type = $btn.data('type') || '';
    var frame = wp.media({
      title: 'Select media',
      button: { text: 'Use this media' },
      library: type ? { type: type } : undefined,
      multiple: false
    });
    frame.on('select', function(){
      var attachment = frame.state().get('selection').first().toJSON();
      $(target).val(attachment.id).trigger('change');
      if (preview) {
        if (attachment.type === 'image') {
          $(preview).html('<img src="'+attachment.url+'" alt="" style="max-width:180px;height:auto;" />');
        } else {
          $(preview).html('<code>'+attachment.url+'</code>');
        }
      }
    });
    frame.open();
  });
  $(document).on('click', '.trinetix-media-clear', function(e){
    e.preventDefault();
    var target = $(this).data('target');
    var preview = $(this).data('preview');
    $(target).val('0');
    if (preview) { $(preview).empty(); }
  });
})(jQuery);
JS;

	wp_add_inline_script( 'jquery', $js );
}
add_action( 'admin_enqueue_scripts', 'trinetix_admin_media_enqueue' );

/**
 * Render a media picker field.
 *
 * @param string $name     Input name.
 * @param int    $value    Attachment ID.
 * @param string $label    Label.
 * @param string $type     Media type filter (image|video|'').
 * @param string $id       Field ID.
 */
function trinetix_render_media_field( string $name, int $value, string $label, string $type = 'image', string $id = '' ): void {
	$id      = $id ? $id : sanitize_key( $name );
	$preview = '';

	if ( $value > 0 ) {
		if ( 'video' === $type ) {
			$url = wp_get_attachment_url( $value );
			if ( $url ) {
				$preview = '<code>' . esc_html( $url ) . '</code>';
			}
		} else {
			$img = wp_get_attachment_image( $value, 'medium', false, array( 'style' => 'max-width:180px;height:auto;' ) );
			if ( $img ) {
				$preview = $img;
			}
		}
	}

	printf(
		'<div class="trinetix-media-field" style="margin:12px 0;">
			<label for="%1$s"><strong>%2$s</strong></label><br />
			<input type="hidden" id="%1$s" name="%3$s" value="%4$d" />
			<div id="%1$s-preview" class="trinetix-media-preview" style="margin:8px 0;">%5$s</div>
			<button type="button" class="button trinetix-media-upload" data-target="#%1$s" data-preview="#%1$s-preview" data-type="%6$s">%7$s</button>
			<button type="button" class="button trinetix-media-clear" data-target="#%1$s" data-preview="#%1$s-preview">%8$s</button>
		</div>',
		esc_attr( $id ),
		esc_html( $label ),
		esc_attr( $name ),
		(int) $value,
		$preview, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from WP APIs.
		esc_attr( $type ),
		esc_html__( 'Select', 'trinetix' ),
		esc_html__( 'Clear', 'trinetix' )
	);
}
