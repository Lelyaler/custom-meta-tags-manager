# Custom Meta Tags Manager — WordPress SEO & Meta Suite

Профессиональный плагин WordPress для динамического управления поисковыми мета-тегами (Technical SEO), социальными протоколами (OpenGraph / Twitter Cards), каноническими ссылками, директивами поисковой индексации (meta robots), мультиязычными связками Hreflang и микроразметкой JSON-LD из панели администратора.

Плагин спроектирован с учётом требований команд поисковой оптимизации (SEO) в высококонкурентных нишах (iGaming, медиа, e-commerce, промо-лендинги) и работает автономно с любой WordPress-темой.

## Возможности

- **Нативный интерфейс в светлых тонах WordPress Admin UI:**
  - Органично интегрируется со стандартной панелью управления WordPress (`postbox`, `widefat`, `subsubsub`);
  - Сводка активных тегов по категориям (OpenGraph, Robots, Canonical, Schema);
  - Быстрые кликабельные пресеты для директив (`og:title`, `og:image`, `canonical`, `robots`, `hreflang`, `schema:json-ld`);
  - Живой мгновенный поиск по таблице и фильтрация по вкладкам категорий.
- **Корректный синтаксический вывод в `<head>`:**
  - `canonical` → `<link rel="canonical" href="...">`
  - `hreflang:*` → `<link rel="alternate" hreflang="..." href="...">`
  - `robots` / `googlebot` → `<meta name="robots" content="...">`
  - `og:*`, `fb:*` → `<meta property="..." content="...">`
  - `twitter:*` → `<meta name="twitter:..." content="...">`
  - `schema:json-ld` → `<script type="application/ld+json">...</script>`
  - Стандартные мета-теги (`description`, `keywords`, `author`, `theme-color`).
- **Гибкий таргетинг страниц:**
  - Оставьте поле пустым — тег применится ко всему сайту;
  - `home` / `front` — только для главной страницы;
  - `post` — для всех одиночных записей блога;
  - `page` — для статических страниц;
  - ID через запятую (`12, 45, 98`) — точечное назначение на конкретные публикации.
- **Хранение в БД через `$wpdb`:**
  - Оптимизированная таблица `{prefix}_meta_tags` с автосозданием через `dbDelta`.
- **Безопасность и стандарты WordPress:**
  - Проверка привилегий `manage_options`;
  - Nonce-защита (`check_admin_referer`, `wp_nonce_field`) для создания, удаления и применения пресетов;
  - Серверная санитизация данных (`sanitize_text_field`, `sanitize_textarea_field`) и безопасное экранирование при рендере.
- **Чистая деинсталляция:** при удалении через панель управления файл `uninstall.php` удаляет созданную таблицу из базы данных.

## Установка

1. Разместите папку плагина в директории `wp-content/plugins/custom-meta-tags`.
2. Перейдите в админ-панель WordPress в раздел **Плагины** и активируйте **Custom Meta Tags Manager**.
3. В левом меню появится пункт **Meta Tags** для управления тегами и пресетами.
