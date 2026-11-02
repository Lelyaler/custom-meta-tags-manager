<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap cmt-admin-page">
  <div class="cmt-header-bar">
    <div class="cmt-header-title">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'Настройки мета-тегов и SEO', 'custom-meta-tags' ); ?></h1>
      <span class="cmt-version-tag"><?php esc_html_e( 'v1.2.0', 'custom-meta-tags' ); ?></span>
      <p class="description">
        <?php esc_html_e( 'Центр управления мета-тегами сайта: OpenGraph, Twitter Cards, Canonical, Robots, Hreflang и микроразметка JSON-LD с поддержкой таргетинга страниц.', 'custom-meta-tags' ); ?>
      </p>
    </div>
    <form method="post" class="cmt-seed-form">
      <?php wp_nonce_field( 'cmt_seed_igaming_nonce', 'cmt_seed_nonce' ); ?>
      <button type="submit" name="seed_igaming" class="button button-secondary" onclick="return confirm('Загрузить рекомендуемый базовый набор SEO & OpenGraph тегов?');">
        <?php esc_html_e( 'Загрузить базовые SEO-пресеты', 'custom-meta-tags' ); ?>
      </button>
    </form>
  </div>

  <?php if ( ! empty( $notice ) ) : ?>
    <div class="notice notice-success is-dismissible">
      <p><?php echo esc_html( $notice ); ?></p>
    </div>
  <?php endif; ?>

  <!-- Сводка активных тегов -->
  <div class="cmt-stats-container">
    <div class="cmt-stat-box">
      <div class="cmt-stat-number"><?php echo esc_html( $stats['total'] ); ?></div>
      <div class="cmt-stat-label"><?php esc_html_e( 'Всего тегов', 'custom-meta-tags' ); ?></div>
    </div>
    <div class="cmt-stat-box cmt-stat-box--og">
      <div class="cmt-stat-number"><?php echo esc_html( $stats['og'] ); ?></div>
      <div class="cmt-stat-label"><?php esc_html_e( 'OpenGraph & соцсети', 'custom-meta-tags' ); ?></div>
    </div>
    <div class="cmt-stat-box cmt-stat-box--robots">
      <div class="cmt-stat-number"><?php echo esc_html( $stats['robots'] ); ?></div>
      <div class="cmt-stat-label"><?php esc_html_e( 'Robots & директивы', 'custom-meta-tags' ); ?></div>
    </div>
    <div class="cmt-stat-box cmt-stat-box--canonical">
      <div class="cmt-stat-number"><?php echo esc_html( $stats['canonical'] ); ?></div>
      <div class="cmt-stat-label"><?php esc_html_e( 'Canonical & Hreflang', 'custom-meta-tags' ); ?></div>
    </div>
  </div>

  <div class="cmt-main-content">
    <!-- Форма добавления мета-тега -->
    <div class="cmt-postbox">
      <div class="cmt-postbox-header">
        <h2><?php esc_html_e( 'Добавить новый мета-тег', 'custom-meta-tags' ); ?></h2>
      </div>
      <div class="cmt-postbox-inside">
        <!-- Блок быстрых пресетов -->
        <div class="cmt-presets-bar">
          <span class="cmt-presets-title"><?php esc_html_e( 'Быстрые шаблоны:', 'custom-meta-tags' ); ?></span>
          <div class="cmt-presets-chips">
            <button type="button" class="cmt-preset-btn" data-tag="og:title" data-placeholder="Официальный сайт и каталог товаров">
              + og:title
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="og:description" data-placeholder="Широкий ассортимент, выгодные цены, быстрая доставка и гарантия качества">
              + og:description
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="og:image" data-placeholder="<?php echo esc_url( home_url( '/wp-content/uploads/og-preview.jpg' ) ); ?>">
              + og:image
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="canonical" data-placeholder="<?php echo esc_url( home_url( '/' ) ); ?>" data-pages="home">
              + canonical
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="robots" data-value="noindex, nofollow" data-placeholder="noindex, nofollow" data-pages="">
              + robots: noindex
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="robots" data-value="index, follow, max-snippet:-1, max-image-preview:large" data-placeholder="index, follow, max-snippet:-1, max-image-preview:large">
              + robots: index
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="twitter:card" data-value="summary_large_image" data-placeholder="summary_large_image">
              + twitter:card
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="hreflang:en" data-placeholder="<?php echo esc_url( home_url( '/en/' ) ); ?>">
              + hreflang
            </button>
            <button type="button" class="cmt-preset-btn" data-tag="schema:json-ld" data-placeholder='{"@context":"https://schema.org","@type":"Organization","name":"My Company","url":"<?php echo esc_url( home_url() ); ?>"}'>
              + JSON-LD Schema
            </button>
          </div>
        </div>

        <form method="post" class="cmt-form">
          <?php wp_nonce_field( 'cmt_add_meta_tag_nonce', 'cmt_nonce' ); ?>
          
          <div class="cmt-form-row">
            <div class="cmt-field-group">
              <label for="tag_name"><?php esc_html_e( 'Название тега или директива (tag_name):', 'custom-meta-tags' ); ?> <span class="required">*</span></label>
              <input type="text" id="tag_name" name="tag_name" class="regular-text" placeholder="og:title, canonical, robots..." required>
              <p class="description"><?php esc_html_e( 'Поддерживает og:*, twitter:*, robots, canonical, hreflang:*, schema:json-ld', 'custom-meta-tags' ); ?></p>
            </div>

            <div class="cmt-field-group">
              <label for="pages"><?php esc_html_e( 'Целевые страницы (таргетинг):', 'custom-meta-tags' ); ?></label>
              <input type="text" id="pages" name="pages" class="regular-text" placeholder="home, 2, 45 (пусто — для всех)">
              <p class="description"><?php esc_html_e( 'Оставьте пустым для всего сайта, либо: home, post, page, или ID через запятую.', 'custom-meta-tags' ); ?></p>
            </div>
          </div>

          <div class="cmt-field-group cmt-field-group--full">
            <label for="tag_content"><?php esc_html_e( 'Значение тега / URL / JSON-код (content):', 'custom-meta-tags' ); ?> <span class="required">*</span></label>
            <textarea id="tag_content" name="tag_content" rows="4" class="large-text code" placeholder="Введите текст, ссылку или валидный JSON-LD..." required></textarea>
          </div>

          <div class="cmt-form-submit">
            <button type="submit" name="submit" class="button button-primary button-large">
              <?php esc_html_e( 'Сохранить мета-тег', 'custom-meta-tags' ); ?>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Список мета-тегов -->
    <div class="cmt-postbox">
      <div class="cmt-postbox-header cmt-table-header-flex">
        <h2><?php esc_html_e( 'Список сохранённых тегов', 'custom-meta-tags' ); ?></h2>
        
        <div class="cmt-search-wrapper">
          <input type="search" id="cmt-search-input" class="regular-text" placeholder="<?php esc_attr_e( 'Поиск по тегам...', 'custom-meta-tags' ); ?>">
        </div>
      </div>
      <div class="cmt-postbox-inside no-padding">
        <!-- Вкладки фильтрации -->
        <div class="cmt-subsubsub-bar">
          <ul class="subsubsub">
            <li><button type="button" class="cmt-filter-tab active" data-filter="all"><?php esc_html_e( 'Все', 'custom-meta-tags' ); ?> <span class="count">(<?php echo esc_html( $stats['total'] ); ?>)</span></button> |</li>
            <li><button type="button" class="cmt-filter-tab" data-filter="og">OpenGraph <span class="count">(<?php echo esc_html( $stats['og'] ); ?>)</span></button> |</li>
            <li><button type="button" class="cmt-filter-tab" data-filter="robots">Robots <span class="count">(<?php echo esc_html( $stats['robots'] ); ?>)</span></button> |</li>
            <li><button type="button" class="cmt-filter-tab" data-filter="canonical">Canonical / Hreflang <span class="count">(<?php echo esc_html( $stats['canonical'] ); ?>)</span></button> |</li>
            <li><button type="button" class="cmt-filter-tab" data-filter="schema">JSON-LD <span class="count">(<?php echo esc_html( $stats['schema'] ); ?>)</span></button></li>
          </ul>
        </div>

        <table class="wp-list-table widefat fixed striped cmt-tags-table">
          <thead>
            <tr>
              <th style="width: 50px;">ID</th>
              <th style="width: 140px;"><?php esc_html_e( 'Категория', 'custom-meta-tags' ); ?></th>
              <th style="width: 220px;"><?php esc_html_e( 'Имя тега', 'custom-meta-tags' ); ?></th>
              <th><?php esc_html_e( 'Содержимое', 'custom-meta-tags' ); ?></th>
              <th style="width: 160px;"><?php esc_html_e( 'Таргетинг', 'custom-meta-tags' ); ?></th>
              <th style="width: 90px; text-align: right;"><?php esc_html_e( 'Действие', 'custom-meta-tags' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if ( ! empty( $meta_tags ) && is_array( $meta_tags ) ) : ?>
              <?php foreach ( $meta_tags as $tag ) : ?>
                <?php
                $cat = cmt_get_tag_category( $tag->tag_name );
                $badge_class = 'cmt-badge--meta';
                $badge_label = 'META';

                if ( 'og' === $cat ) {
                  $badge_class = 'cmt-badge--og';
                  $badge_label = 'OpenGraph';
                } elseif ( 'robots' === $cat ) {
                  $badge_class = 'cmt-badge--robots';
                  $badge_label = 'Robots';
                } elseif ( 'canonical' === $cat ) {
                  $badge_class = 'cmt-badge--canonical';
                  $badge_label = 'Canonical';
                } elseif ( 'schema' === $cat ) {
                  $badge_class = 'cmt-badge--schema';
                  $badge_label = 'JSON-LD';
                }
                ?>
                <tr data-category="<?php echo esc_attr( $cat ); ?>">
                  <td class="cmt-cell-id"><?php echo esc_html( $tag->id ); ?></td>
                  <td>
                    <span class="cmt-badge <?php echo esc_attr( $badge_class ); ?>">
                      <?php echo esc_html( $badge_label ); ?>
                    </span>
                  </td>
                  <td><code class="cmt-tag-code"><?php echo esc_html( $tag->tag_name ); ?></code></td>
                  <td>
                    <div class="cmt-cell-content">
                      <?php echo esc_html( wp_trim_words( $tag->tag_content, 20, '...' ) ); ?>
                    </div>
                  </td>
                  <td>
                    <?php if ( ! empty( $tag->pages ) ) : ?>
                      <span class="cmt-page-pill"><?php echo esc_html( $tag->pages ); ?></span>
                    <?php else : ?>
                      <span class="cmt-target-all"><?php esc_html_e( 'Все страницы', 'custom-meta-tags' ); ?></span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right;">
                    <form method="post" onsubmit="return confirm('<?php esc_attr_e( 'Удалить этот мета-тег?', 'custom-meta-tags' ); ?>');" class="cmt-delete-form">
                      <?php wp_nonce_field( 'cmt_delete_meta_tag_nonce', 'cmt_delete_nonce' ); ?>
                      <input type="hidden" name="tag_id" value="<?php echo esc_attr( $tag->id ); ?>">
                      <button type="submit" name="delete" class="button-link-delete">
                        <?php esc_html_e( 'Удалить', 'custom-meta-tags' ); ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr class="no-meta-tags">
                <td colspan="6" class="cmt-empty-message">
                  <?php esc_html_e( 'Мета-теги пока не добавлены. Заполните форму выше или загрузите базовый набор.', 'custom-meta-tags' ); ?>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
