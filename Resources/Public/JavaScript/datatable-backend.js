import AjaxRequest from '@typo3/core/ajax/ajax-request.js';
import DocumentService from '@typo3/core/document-service.js';
import Icons from '@typo3/backend/icons.js';

/**
 * @param {string} key
 * @param {string} fallback
 * @returns {string}
 */
function translate(key, fallback) {
  return TYPO3?.lang?.[key] ?? fallback;
}

/**
 * @param {Array<{virtual?: boolean}>} columns
 * @param {number} displayIndex
 * @returns {number}
 */
function toServerColumnIndex(columns, displayIndex) {
  let serverIndex = -1;
  for (let i = 0; i <= displayIndex; i += 1) {
    if (!columns[i]?.virtual) {
      serverIndex += 1;
    }
  }
  return serverIndex;
}

/**
 * Build DataTables-compatible request payload for the TYPO3 backend endpoint.
 * Virtual (client-only) columns are omitted; order indices are remapped.
 *
 * @param {object} state
 * @returns {object}
 */
function buildRequestPayload(state) {
  const columns = [];

  state.columns.forEach((column, displayIndex) => {
    if (column.virtual) {
      return;
    }
    columns.push({
      data: column.data,
      name: column.data,
      searchable: column.searchable !== false,
      orderable: column.orderable !== false,
      search: {
        value: state.columnSearch[displayIndex] ?? '',
        regex: false,
      },
    });
  });

  let order = [];
  if (state.orderColumnIndex >= 0 && !state.columns[state.orderColumnIndex]?.virtual) {
    const serverOrderIndex = toServerColumnIndex(state.columns, state.orderColumnIndex);
    if (serverOrderIndex >= 0) {
      order = [{ column: serverOrderIndex, dir: state.orderDirection }];
    }
  }

  return {
    draw: state.draw,
    start: state.start,
    length: state.length,
    search: {
      value: state.globalSearch,
      regex: false,
    },
    columns,
    order,
  };
}

/**
 * @param {HTMLElement} panel
 * @param {object} options
 */
function createControls(panel, options) {
  const toolbar = document.createElement('div');
  toolbar.className = 't3-datatable-toolbar';

  const filters = document.createElement('div');
  filters.className = 't3-datatable-toolbar__filters';

  const lengthGroup = document.createElement('div');
  lengthGroup.className = 't3-datatable-length';

  const lengthLabel = document.createElement('label');
  lengthLabel.className = 't3-datatable-length__label';
  lengthLabel.textContent = translate('length.label', 'Show');

  const length = document.createElement('select');
  length.className = 'form-select form-select-sm t3-datatable-length__select';
  length.setAttribute('aria-label', translate('length.label', 'Show'));

  const current = options.pageLength ?? 25;
  const choices = Array.from(new Set([10, 25, 50, 100, current]))
    .sort((a, b) => a - b);
  choices.forEach((value) => {
    const opt = document.createElement('option');
    opt.value = String(value);
    opt.textContent = String(value);
    if (value === current) {
      opt.selected = true;
    }
    length.append(opt);
  });

  const lengthId = `${(panel?.querySelector('table')?.id) ?? 't3-datatable'}-length`;
  length.id = lengthId;
  lengthLabel.htmlFor = lengthId;
  lengthGroup.append(lengthLabel, length);

  const searchGroup = document.createElement('div');
  searchGroup.className = 'input-group t3-datatable-search';

  const search = document.createElement('input');
  search.type = 'search';
  search.className = 'form-control';
  search.placeholder = options.searchPlaceholder
    ?? translate('search.placeholder', 'Search records');
  search.setAttribute('aria-label', search.placeholder);

  searchGroup.append(search);
  filters.append(lengthGroup, searchGroup);

  const nav = document.createElement('nav');
  nav.setAttribute('aria-labelledby', 't3-datatable-pagination');

  const pagination = document.createElement('ul');
  pagination.className = 'pagination mb-0';

  const rangeItem = document.createElement('li');
  rangeItem.className = 'page-item';
  const rangeLink = document.createElement('span');
  rangeLink.className = 'page-link';
  rangeLink.id = 't3-datatable-pagination';
  rangeItem.append(rangeLink);

  const prevItem = document.createElement('li');
  prevItem.className = 'page-item';
  const prev = document.createElement('button');
  prev.type = 'button';
  prev.className = 'page-link border-0 bg-transparent';
  prev.setAttribute('aria-label', translate('pagination.previous', 'Previous'));
  prev.title = translate('pagination.previous', 'Previous');
  prevItem.append(prev);

  const nextItem = document.createElement('li');
  nextItem.className = 'page-item';
  const next = document.createElement('button');
  next.type = 'button';
  next.className = 'page-link border-0 bg-transparent';
  next.setAttribute('aria-label', translate('pagination.next', 'Next'));
  next.title = translate('pagination.next', 'Next');
  nextItem.append(next);

  pagination.append(rangeItem, prevItem, nextItem);
  nav.append(pagination);

  toolbar.append(filters, nav);
  panel.prepend(toolbar);

  Icons.getIcon('actions-view-paging-previous', Icons.sizes.small).then((markup) => {
    prev.innerHTML = markup;
  }).catch(() => {
    prev.textContent = '‹';
  });
  Icons.getIcon('actions-view-paging-next', Icons.sizes.small).then((markup) => {
    next.innerHTML = markup;
  }).catch(() => {
    next.textContent = '›';
  });

  return { toolbar, length, search, rangeLink, prevItem, prev, nextItem, next, panel };
}

/**
 * @param {HTMLTableSectionElement} thead
 * @param {number} orderColumnIndex
 * @param {string} orderDirection
 */
function updateSortIndicators(thead, orderColumnIndex, orderDirection) {
  Array.from(thead.rows[0]?.cells ?? []).forEach((th, index) => {
    th.classList.remove('t3-datatable-sorted-asc', 't3-datatable-sorted-desc');
    if (index === orderColumnIndex) {
      th.classList.add(orderDirection === 'asc' ? 't3-datatable-sorted-asc' : 't3-datatable-sorted-desc');
    }
  });
}

/**
 * Apply a column render result to a table cell.
 *
 * @param {HTMLTableCellElement} td
 * @param {{render?: Function, html?: boolean}} column
 * @param {*} value
 * @param {object} row
 */
function applyCellRender(td, column, value, row) {
  if (typeof column.render !== 'function') {
    td.textContent = value === null || value === undefined ? '' : String(value);
    return;
  }

  const rendered = column.render(value, row, td);
  if (rendered === null || rendered === undefined) {
    return;
  }
  if (rendered instanceof Node) {
    td.replaceChildren(rendered);
    return;
  }
  if (column.html) {
    td.innerHTML = String(rendered);
    return;
  }
  td.textContent = String(rendered);
}

/**
 * Initialise a backend DataTable bound to a registered grid identifier.
 *
 * @param {string|HTMLTableElement} selector
 * @param {{
 *   gridIdentifier: string,
 *   columns: Array<{
 *     data: string,
 *     title: string,
 *     searchable?: boolean,
 *     orderable?: boolean,
 *     virtual?: boolean,
 *     html?: boolean,
 *     render?: Function,
 *   }>,
 *   pageLength?: number,
 *   searchPlaceholder?: string,
 *   onRowsRendered?: (tbody: HTMLTableSectionElement, rows: object[]) => void,
 * }} options
 */
export function initDataTable(selector, options) {
  const table = typeof selector === 'string'
    ? document.querySelector(selector)
    : selector;

  if (!(table instanceof HTMLTableElement)) {
    throw new Error('initDataTable: table element not found.');
  }
  if (!options?.gridIdentifier) {
    throw new Error('initDataTable: gridIdentifier is required.');
  }
  if (!Array.isArray(options.columns) || options.columns.length === 0) {
    throw new Error('initDataTable: columns are required.');
  }

  const ajaxUrl = TYPO3.settings.ajaxUrls.t3datatable_data;
  if (!ajaxUrl) {
    throw new Error('initDataTable: TYPO3.settings.ajaxUrls.t3datatable_data is not registered.');
  }

  const panel = table.closest('[data-t3-datatable-panel]') ?? table.parentElement;
  const tbody = table.tBodies[0] ?? table.createTBody();
  const thead = table.tHead ?? table.createTHead();
  if (!thead.rows.length) {
    const row = thead.insertRow();
    options.columns.forEach((column) => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = column.title ?? column.data;
      if (column.orderable !== false) {
        th.classList.add('t3-datatable-orderable');
      }
      row.append(th);
    });
  } else {
    Array.from(thead.rows[0]?.cells ?? []).forEach((th, index) => {
      if (options.columns[index]?.orderable !== false) {
        th.classList.add('t3-datatable-orderable');
      }
    });
  }

  const state = {
    draw: 1,
    start: 0,
    length: options.pageLength ?? 25,
    globalSearch: '',
    columnSearch: {},
    orderColumnIndex: -1,
    orderDirection: 'asc',
    columns: options.columns,
    recordsFiltered: 0,
  };

  const controls = createControls(panel, options);
  let searchTimer = 0;

  const renderRows = (rows) => {
    tbody.replaceChildren();
    rows.forEach((row) => {
      const tr = tbody.insertRow();
      options.columns.forEach((column) => {
        const td = tr.insertCell();
        const value = column.virtual ? undefined : row[column.data];
        applyCellRender(td, column, value, row);
      });
    });
    if (typeof options.onRowsRendered === 'function') {
      options.onRowsRendered(tbody, rows);
    }
  };

  const updateInfo = () => {
    const from = state.recordsFiltered === 0 ? 0 : state.start + 1;
    const to = Math.min(state.start + state.length, state.recordsFiltered);
    const rangeTemplate = translate('pagination.range', '%1$s-%2$s of %3$s');
    controls.rangeLink.textContent = rangeTemplate
      .replace('%1$s', String(from))
      .replace('%2$s', String(to))
      .replace('%3$s', String(state.recordsFiltered));

    const hasPrevious = state.start > 0;
    const hasNext = state.start + state.length < state.recordsFiltered;
    controls.prevItem.classList.toggle('disabled', !hasPrevious);
    controls.prev.disabled = !hasPrevious;
    controls.nextItem.classList.toggle('disabled', !hasNext);
    controls.next.disabled = !hasNext;
    updateSortIndicators(thead, state.orderColumnIndex, state.orderDirection);
  };

  const setLoading = (isLoading) => {
    panel?.classList.toggle('is-loading', isLoading);
  };

  const load = async () => {
    setLoading(true);
    try {
      const payload = buildRequestPayload(state);
      const response = await new AjaxRequest(ajaxUrl)
        .withQueryArguments({ grid: options.gridIdentifier })
        .post(payload);
      const data = await response.resolve('json');

      if (data.error) {
        throw new Error(data.error);
      }

      state.draw = Number(data.draw ?? state.draw);
      state.recordsFiltered = Number(data.recordsFiltered ?? 0);
      renderRows(Array.isArray(data.data) ? data.data : []);
      updateInfo();
    } finally {
      setLoading(false);
    }
  };

  controls.length.addEventListener('change', () => {
    const next = Number.parseInt(controls.length.value, 10);
    if (Number.isNaN(next) || next <= 0) {
      return;
    }
    state.length = next;
    state.start = 0;
    state.draw += 1;
    load().catch(console.error);
  });

  controls.search.addEventListener('input', () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => {
      state.globalSearch = controls.search.value.trim();
      state.start = 0;
      state.draw += 1;
      load().catch(console.error);
    }, 300);
  });

  controls.prev.addEventListener('click', () => {
    if (state.start <= 0) {
      return;
    }
    state.start = Math.max(0, state.start - state.length);
    state.draw += 1;
    load().catch(console.error);
  });

  controls.next.addEventListener('click', () => {
    if (state.start + state.length >= state.recordsFiltered) {
      return;
    }
    state.start += state.length;
    state.draw += 1;
    load().catch(console.error);
  });

  Array.from(thead.rows[0]?.cells ?? []).forEach((th, index) => {
    const column = options.columns[index];
    if (!column || column.orderable === false) {
      return;
    }
    th.addEventListener('click', () => {
      if (state.orderColumnIndex === index) {
        state.orderDirection = state.orderDirection === 'asc' ? 'desc' : 'asc';
      } else {
        state.orderColumnIndex = index;
        state.orderDirection = 'asc';
      }
      state.draw += 1;
      load().catch(console.error);
    });
  });

  load().catch(console.error);

  const api = {
    reload: () => {
      state.draw += 1;
      return load();
    },
  };

  if (panel instanceof HTMLElement) {
    panel.__t3DatatableApi = api;
  }

  return api;
}

/**
 * Initialise a single table element from its `data-*` configuration.
 *
 * @param {HTMLTableElement} table
 */
function initFromDataset(table) {
  if (table.dataset.t3DatatableInitialised === '1') {
    return;
  }
  table.dataset.t3DatatableInitialised = '1';

  let columns;
  try {
    columns = JSON.parse(table.dataset.columns ?? '[]');
  } catch (error) {
    console.error('t3-datatable: invalid data-columns JSON.', error);
    return;
  }

  const pageLength = Number.parseInt(table.dataset.pageLength ?? '', 10);

  initDataTable(table, {
    gridIdentifier: table.dataset.t3Datatable,
    columns,
    pageLength: Number.isNaN(pageLength) ? undefined : pageLength,
    searchPlaceholder: table.dataset.searchPlaceholder,
  });
}

/**
 * Demo module helper: refresh button in the card header.
 */
function initDemoModuleChrome() {
  const refreshBtn = document.getElementById('t3-datatable-refresh-btn');
  refreshBtn?.addEventListener('click', () => {
    const panel = document.querySelector('[data-t3-datatable-panel]');
    panel?.__t3DatatableApi?.reload().catch(console.error);
  });
}

/**
 * Auto-initialise every `[data-t3-datatable]` table on the page. This keeps the
 * backend Content Security Policy intact by avoiding inline scripts entirely.
 */
export function bootstrap() {
  document
    .querySelectorAll('table[data-t3-datatable]')
    .forEach((table) => initFromDataset(table));
  initDemoModuleChrome();
}

DocumentService.ready().then(() => bootstrap());
