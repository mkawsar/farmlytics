/**
 * Farm type enum. Keys match backend App\Enums\FarmType.
 */

export const FARM_TYPES = {
    dairy: 'Dairy',
    fattening: 'Fattening',
    mixed: 'Mixed',
};

/**
 * @param {string|null|undefined} type
 * @returns {string}
 */
export function farmTypeLabel(type) {
    return type ? (FARM_TYPES[type] ?? type) : '—';
}
