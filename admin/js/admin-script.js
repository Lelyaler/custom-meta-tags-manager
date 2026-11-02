/**
 * Управление интерфейсом панели кастомных мета-тегов
 * Реализует быстрые пресеты для специалистов и живую фильтрацию таблицы
 */
document.addEventListener('DOMContentLoaded', function () {
  // Быстрые пресеты
  const presetButtons = document.querySelectorAll('.cmt-preset-btn');
  const tagNameInput = document.getElementById('tag_name');
  const tagContentInput = document.getElementById('tag_content');
  const tagPagesInput = document.getElementById('pages');

  if (presetButtons.length && tagNameInput && tagContentInput) {
    presetButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        const tag = this.getAttribute('data-tag') || '';
        const placeholder = this.getAttribute('data-placeholder') || '';
        const val = this.getAttribute('data-value') || '';
        const pages = this.getAttribute('data-pages') || '';

        tagNameInput.value = tag;
        tagContentInput.placeholder = placeholder;

        if (val) {
          tagContentInput.value = val;
        }

        if (pages && tagPagesInput && !tagPagesInput.value) {
          tagPagesInput.value = pages;
        }

        tagContentInput.focus();
      });
    });
  }

  // Живой поиск по сохранённым мета-тегам
  const searchInput = document.getElementById('cmt-search-input');
  const tableRows = document.querySelectorAll('.cmt-tags-table tbody tr:not(.no-meta-tags)');

  if (searchInput && tableRows.length) {
    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();

      tableRows.forEach(function (row) {
        const text = row.textContent.toLowerCase();
        if (!query || text.indexOf(query) !== -1) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }

  // Фильтрация по типам мета-тегов (вкладки)
  const filterTabs = document.querySelectorAll('.cmt-filter-tab');
  if (filterTabs.length && tableRows.length) {
    filterTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        filterTabs.forEach(function (t) {
          t.classList.remove('active');
        });
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        tableRows.forEach(function (row) {
          const rowCategory = row.getAttribute('data-category') || 'all';
          if (filter === 'all' || rowCategory === filter) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    });
  }
});
