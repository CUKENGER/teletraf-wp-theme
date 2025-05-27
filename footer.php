<?php
/**
 * Footer template
 */
?>
<footer class="footer">
  <div class="footer__container">
    <div class="footer__inner">
      <div>
        <span
          class='footer-copyright'><?php echo esc_html(get_theme_mod('tgx_footer_copyright', 'TGX | блог © 2025')); ?></span>
      </div>
      <div class="footer-right">
        <?php
        $privacy_policy_text = esc_html(get_theme_mod('tgx_privacy_policy', 'Политика конфиденциальности'));
        $privacy_policy_link = esc_url(get_theme_mod('tgx_privacy_policy_link', ''));
        if ($privacy_policy_link) {
          echo '<a href="' . $privacy_policy_link . '" class="footer-link" target="_blank" rel="noopener">' . $privacy_policy_text . '</a>';
        } else {
          echo '<span>' . $privacy_policy_text . '</span>';
        }
        ?>
        <?php
        $public_offer_text = esc_html(get_theme_mod('tgx_public_offer', 'Публичная оферта'));
        $public_offer_link = esc_url(get_theme_mod('tgx_public_offer_link', ''));
        if ($public_offer_link) {
          echo '<a href="' . $public_offer_link . '" class="footer-link" target="_blank" rel="noopener">' . $public_offer_text . '</a>';
        } else {
          echo '<span>' . $public_offer_text . '</span>';
        }
        ?>
      </div>
    </div>
  </div>
  <!-- Debug: Footer rendered on <?php echo esc_html(date('Y-m-d H:i:s')); ?> -->
</footer>
<?php wp_footer(); ?>
</body>

</html>