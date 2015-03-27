<form action="<?php echo $action; ?>" method="post" class="<?php echo "{$slug}-form" ?>">

  <fieldset>

    <?php echo $nonce_field ?>
    <input type="hidden" name="<?php echo $slug; ?>[list_id]" value="<?php echo $list_id; ?>">
    <input type="hidden" name="<?php echo $slug; ?>[double_optin]" value="<?php echo $double_optin ? 1 : 0; ?>">
    <input type="hidden" name="<?php echo $slug; ?>[send_welcome]" value="<?php echo $send_welcome ? 1 : 0; ?>">

    <?php if( $redirect_to ): ?>
      <input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">
    <?php endif; ?>

    <?php $i = 0; foreach( $groupings as $grouping ): ?>

      <input type="hidden" name="<?php echo $slug; ?>[groupings][<?php echo $i; ?>][<?php echo $grouping['identifier']; ?>]" value="<?php echo $grouping[$grouping['identifier']]; ?>">

      <?php foreach( $grouping['groups'] as $group ): ?>
        <input type="hidden" name="<?php echo $slug; ?>[groupings][<?php echo $i; ?>][groups][]" value="<?php echo $group; ?>">
      <?php endforeach; ?>

    <?php $i++; endforeach; ?>

    <?php if( $title || $subtitle ): ?>

      <div class="<?php echo "{$slug}-form-header" ?>">
        <?php if( $title ): ?>
          <h2><?php echo $title; ?></h2>
        <?php endif; ?>

        <?php if( $subtitle ): ?>
          <p><?php echo $subtitle; ?></p>
        <?php endif; ?>
      </div class="<?php echo "{$slug}-form-header" ?>">

    <?php endif; ?>

    <div class="<?php echo "{$slug}-form-body" ?>">
      <?php if( $message ): ?>
        <div class="message" data-status="<?php echo $status; ?>"<?php if( $error_code ) echo sprintf( ' data-error-code="$s"', $error_code ) ?>>
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <input type="email" name="<?php echo $slug; ?>[email]" value="" placeholder="<?php echo $email_placeholder; ?>" required>

      <input type="submit" value="<?php echo $submit_label; ?>">
    </div class="<?php echo "{$slug}-form-body" ?>">

    <?php if( $footer_text ): ?>

      <div class="<?php echo "{$slug}-form-footer" ?>">
        <p><?php echo $footer_text; ?></p>
      </div class="<?php echo "{$slug}-form-footer" ?>">

    <?php endif; ?>

  </fieldset>

</form>
