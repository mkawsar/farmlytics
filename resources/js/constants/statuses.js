/**
 * Animal status enum. Keys match backend App\Enums\Status.
 */

export const STATUSES = {
    active: 'Active',
    sold: 'Sold',
    dead: 'Dead',
};

/**
 * @param {string|null|undefined} status
 * @returns {string}
 */
export function statusLabel(status) {
    return status ? (STATUSES[status] ?? status) : '—';
}
