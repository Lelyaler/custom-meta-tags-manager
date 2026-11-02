<?php
/**
 * Plugin Name:       Custom Meta Tags Manager
 * Plugin URI:        https://github.com/Lelyaler/custom-meta-tags-manager
 * Description:       Универсальный модуль управления мета-тегами сайта: OpenGraph, Twitter Cards, Canonical, Robots, Hreflang и микроразметка JSON-LD.
 * Version:           1.2.2
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Valeria Chernyavskaya
 * Author URI:        https://lelyaler.github.io/Portfolio/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-meta-tags
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Подключение стилей и скриптов в панели администрирования
 */
add_action( 'admin_enqueue_scripts', 'cmt_admin_scripts' );
function cmt_admin_scripts( $hook_suffix ) {
	if ( 'toplevel_page_custom_meta_tags' === $hook_suffix ) {
		wp_enqueue_style( 'cmt_admin_styles', plugin_dir_url( __FILE__ ) . 'admin/css/admin-style.css', array(), '1.2.1' );
		wp_enqueue_script( 'cmt_admin_scripts', plugin_dir_url( __FILE__ ) . 'admin/js/admin-script.js', array(), '1.2.1', true );
	}
}

/**
 * Создание таблицы базы данных при активации плагина
 */
register_activation_hook( __FILE__, 'cmt_create_meta_tags_table' );
function cmt_create_meta_tags_table() {
	global $wpdb;
	$table_name      = $wpdb->prefix . 'meta_tags';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		tag_name varchar(255) NOT NULL,
		tag_content text NOT NULL,
		pages text NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

/**
 * Определение категории мета-тега для группировки и фильтрации
 */
function cmt_get_tag_category( $tag_name ) {
	$tag = strtolower( trim( (string) $tag_name ) );
	if ( 0 === strpos( $tag, 'og:' ) || 0 === strpos( $tag, 'fb:' ) || 0 === strpos( $tag, 'twitter:' ) ) {
		return 'og';
	}
	if ( 'robots' === $tag || 0 === strpos( $tag, 'googlebot' ) ) {
		return 'robots';
	}
	if ( 'canonical' === $tag || 'hreflang' === $tag || 0 === strpos( $tag, 'hreflang:' ) ) {
		return 'canonical';
	}
	if ( 'json-ld' === $tag || 'schema:json-ld' === $tag || 'schema' === $tag ) {
		return 'schema';
	}
	return 'meta';
}

/**
 * Проверка таргетинга страниц
 */
function cmt_matches_target_pages( $pages_raw ) {
	$pages_raw = trim( (string) $pages_raw );
	if ( empty( $pages_raw ) ) {
		return true;
	}

	$current_id = ( is_page() || is_single() ) ? get_queried_object_id() : 0;
	$is_front   = is_front_page() || is_home();

	$rules = array_map( 'trim', explode( ',', strtolower( $pages_raw ) ) );

	foreach ( $rules as $rule ) {
		if ( '' === $rule ) {
			continue;
		}

		if ( ( 'home' === $rule || 'front' === $rule ) && $is_front ) {
			return true;
		}

		if ( 'post' === $rule && is_single() ) {
			return true;
		}

		if ( 'page' === $rule && is_page() && ! $is_front ) {
			return true;
		}

		if ( 'category' === $rule && is_category() ) {
			return true;
		}

		if ( 'archive' === $rule && is_archive() ) {
			return true;
		}

		if ( 'search' === $rule && is_search() ) {
			return true;
		}

		if ( '404' === $rule && is_404() ) {
			return true;
		}

		if ( is_numeric( $rule ) && (int) $rule === (int) $current_id ) {
			return true;
		}
	}

	return false;
}

/**
 * Вывод одного мета-тега в head с корректным синтаксисом HTML5
 */
function cmt_render_single_tag( $tag_name, $tag_content ) {
	$name       = trim( (string) $tag_name );
	$name_lower = strtolower( $name );
	$content    = trim( (string) $tag_content );

	if ( empty( $name ) || empty( $content ) ) {
		return;
	}

	// Каноническая ссылка
	if ( 'canonical' === $name_lower ) {
		echo '<link rel="canonical" href="' . esc_url( $content ) . '" />' . "\n";
		return;
	}

	// Языковая разметка Hreflang
	if ( 0 === strpos( $name_lower, 'hreflang:' ) ) {
		$lang = substr( $name, 9 );
		echo '<link rel="alternate" hreflang="' . esc_attr( $lang ) . '" href="' . esc_url( $content ) . '" />' . "\n";
		return;
	}

	// Микроразметка JSON-LD с защитой от разрыва тега script
	if ( 'json-ld' === $name_lower || 'schema:json-ld' === $name_lower || 'schema' === $name_lower ) {
		$safe_json = str_replace( '</script>', '<\/script>', wp_strip_all_tags( $content ) );
		echo '<script type="application/ld+json">' . $safe_json . '</script>' . "\n";
		return;
	}

	// OpenGraph и Facebook
	if ( 0 === strpos( $name_lower, 'og:' ) || 0 === strpos( $name_lower, 'fb:' ) ) {
		echo '<meta property="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
		return;
	}

	// Twitter Card
	if ( 0 === strpos( $name_lower, 'twitter:' ) ) {
		echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
		return;
	}

	// Поисковые директивы Robots
	if ( 'robots' === $name_lower || 0 === strpos( $name_lower, 'googlebot' ) ) {
		echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
		return;
	}

	// HTTP-EQUIV
	if ( 0 === strpos( $name_lower, 'http-equiv:' ) ) {
		$header = substr( $name, 11 );
		echo '<meta http-equiv="' . esc_attr( $header ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
		return;
	}

	// Стандартные мета-теги (description, keywords, author, etc.)
	echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
}

/**
 * Вывод сохранённых тегов в секцию head
 */
add_action( 'wp_head', 'cmt_add_meta_tags', 1 );
function cmt_add_meta_tags() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'meta_tags';

	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
		return;
	}

	$meta_tags = $wpdb->get_results( "SELECT * FROM $table_name" );

	if ( $meta_tags ) {
		echo "<!-- Custom Meta Tags -->\n";
		foreach ( $meta_tags as $tag ) {
			if ( cmt_matches_target_pages( $tag->pages ) ) {
				cmt_render_single_tag( $tag->tag_name, $tag->tag_content );
			}
		}
		echo "<!-- /Custom Meta Tags -->\n";
	}
}

/**
 * Регистрация страницы в главном меню администратора
 */
add_action( 'admin_menu', 'cmt_add_admin_menu' );
function cmt_add_admin_menu() {
	add_menu_page(
		__( 'Настройки Meta Tags', 'custom-meta-tags' ),
		__( 'Meta Tags', 'custom-meta-tags' ),
		'manage_options',
		'custom_meta_tags',
		'cmt_settings_page',
		'dashicons-tag',
		80
	);
}

/**
 * Страница управления мета-тегами
 */
function cmt_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'У вас недостаточно прав для доступа к этой странице.', 'custom-meta-tags' ) );
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'meta_tags';
	$notice     = '';

	// Обработка добавления мета-тега
	if ( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'cmt_add_meta_tag_nonce', 'cmt_nonce' );

		$tag_name    = sanitize_text_field( wp_unslash( $_POST['tag_name'] ?? '' ) );
		$tag_content = wp_unslash( $_POST['tag_content'] ?? '' );
		$pages       = sanitize_text_field( wp_unslash( $_POST['pages'] ?? '' ) );

		if ( in_array( strtolower( $tag_name ), array( 'json-ld', 'schema:json-ld', 'schema' ), true ) ) {
			$tag_content = wp_strip_all_tags( trim( $tag_content ) );
		} else {
			$tag_content = sanitize_textarea_field( $tag_content );
		}

		if ( ! empty( $tag_name ) && ! empty( $tag_content ) ) {
			$wpdb->insert(
				$table_name,
				array(
					'tag_name'    => $tag_name,
					'tag_content' => $tag_content,
					'pages'       => $pages,
				),
				array( '%s', '%s', '%s' )
			);
			$notice = __( 'Мета-тег успешно добавлен.', 'custom-meta-tags' );
		}
	}

	// Обработка удаления мета-тега
	if ( isset( $_POST['delete'] ) ) {
		check_admin_referer( 'cmt_delete_meta_tag_nonce', 'cmt_delete_nonce' );

		$tag_id = isset( $_POST['tag_id'] ) ? absint( $_POST['tag_id'] ) : 0;
		if ( $tag_id > 0 ) {
			$wpdb->delete(
				$table_name,
				array( 'id' => $tag_id ),
				array( '%d' )
			);
			$notice = __( 'Мета-тег успешно удалён.', 'custom-meta-tags' );
		}
	}

	// Обработка быстрой загрузки базовых SEO-пресетов
	if ( isset( $_POST['seed_igaming'] ) ) {
		check_admin_referer( 'cmt_seed_igaming_nonce', 'cmt_seed_nonce' );

		$presets = array(
			array( 'canonical', home_url( '/' ), 'home' ),
			array( 'robots', 'index, follow, max-snippet:-1, max-image-preview:large', '' ),
			array( 'og:type', 'website', 'home' ),
			array( 'og:locale', 'ru_RU', '' ),
			array( 'twitter:card', 'summary_large_image', '' ),
		);

		foreach ( $presets as $preset ) {
			$exists = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name WHERE tag_name = %s AND pages = %s",
					$preset[0],
					$preset[2]
				)
			);
			if ( ! $exists ) {
				$wpdb->insert(
					$table_name,
					array(
						'tag_name'    => $preset[0],
						'tag_content' => $preset[1],
						'pages'       => $preset[2],
					),
					array( '%s', '%s', '%s' )
				);
			}
		}
		$notice = __( 'Базовый набор тегов успешно добавлен в реестр.', 'custom-meta-tags' );
	}

	// Получение всех сохранённых тегов
	$meta_tags = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );

	// Подсчёт статистики для панели
	$stats = array(
		'total'     => count( $meta_tags ),
		'og'        => 0,
		'robots'    => 0,
		'canonical' => 0,
		'schema'    => 0,
	);

	foreach ( $meta_tags as $tag ) {
		$cat = cmt_get_tag_category( $tag->tag_name );
		if ( isset( $stats[ $cat ] ) ) {
			$stats[ $cat ]++;
		}
	}

	include plugin_dir_path( __FILE__ ) . 'admin/views/settings-view.php';
}
