'use strict';

import Sortable from 'sortablejs';
import axios from 'axios';
import { environment } from './environments/environment';

let sortableInstance: Sortable | null = null;

/**
 * CSRF / API token setup
 */
const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
const apiToken = document.querySelector<HTMLMetaElement>('meta[name="api-token"]')?.content;

axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['Accept'] = 'application/json';

if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

if (apiToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${apiToken}`;
}

/**
 * Collect current row order from DOM
 */
function getOrder(): number[] {
    const rows = document.querySelectorAll<HTMLTableRowElement>(
        '#page-list-table tbody tr'
    );

    const order: number[] = [];

    rows.forEach((row) => {
        const id = row.getAttribute('data-id');
        if (id) {
            order.push(Number(id));
        }
    });

    return order;
}

/**
 * Send reorder to backend
 */
async function sendOrder(): Promise<void> {
    try {
        const order = getOrder();

        await axios.put(
            `${environment.REST_API_BASE}/pages/reorder`,
            { order }
        );

        console.log('Order saved:', order);
    } catch (err) {
        console.error('Reorder failed:', err);
    }
}

/**
 * Recalculate UI priority column
 */
function renumberTable(): void {
    const rows = document.querySelectorAll<HTMLTableRowElement>(
        '#page-list-table tbody tr'
    );

    rows.forEach((row, index) => {
        const el = row.querySelector<HTMLElement>('.priority');
        if (el) {
            el.textContent = String(index + 1);
        }
    });
}

/**
 * Init SortableJS
 */
function initSortable(): void {
    const tbody = document.querySelector<HTMLTableSectionElement>(
        '#page-list-table tbody'
    );

    if (!tbody) return;

    sortableInstance = Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        forceFallback: true,
        fallbackClass: 'sortable-fallback',
        ghostClass: 'sortable-ghost',
        onEnd: async () => {
            renumberTable();
            await sendOrder();
        }
    });
}

/**
 * Destroy SortableJS
 */
function destroySortable(): void {
    sortableInstance?.destroy();
    sortableInstance = null;
}

/**
 * Delete handler (event delegation)
 */
function initDeleteHandler(): void {
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;

        const btn = target.closest('.btn-delete');
        if (!btn) return;

        const row = btn.closest('tr');
        if (!row) return;

        const ok = confirm('Delete this item?');

        if (ok) {
            row.remove();
            renumberTable();
        }
    });
}

/**
 * Toggle reorder mode
 * (Blade-ben előre definiált .reorder-col oszlopot használ)
 */
function toggleOrderMode(): void {
    const table = document.getElementById('page-list-table');
    const btn = document.getElementById('orderer');

    if (!table) return;

    const isActive = table.classList.contains('reorder-active');

    if (isActive) {
        table.classList.remove('reorder-active');

        btn?.classList.remove('btn-success');
        btn?.classList.add('btn-default');

        destroySortable();
    } else {
        table.classList.add('reorder-active');

        btn?.classList.remove('btn-default');
        btn?.classList.add('btn-success');

        initSortable();
    }
}

/**
 * Init module
 */
export default function dragndroporder(): void {
    const btn = document.getElementById('orderer');

    if (btn) {
        btn.addEventListener('click', toggleOrderMode);
    }

    initDeleteHandler();
}

document.addEventListener('DOMContentLoaded', () => {
    dragndroporder();
});

/**
 * Global exposure (Blade fallback)
 */
(window as any).dragndroporder = dragndroporder;