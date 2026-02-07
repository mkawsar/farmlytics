/**
 * Shed type enum. Keys match backend App\Enums\ShedType.
 */

export const SHED_TYPES = {
    milking: 'Milking',
    calf: 'Calf',
    quarantine: 'Quarantine',
};

/**
 * @param {string|null|undefined} type
 * @returns {string}
 */
export function shedTypeLabel(type) {
    return type ? (SHED_TYPES[type] ?? type) : '—';
}
