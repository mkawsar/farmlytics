/**
 * Animal group enum. Keys match backend App\Enums\Group.
 */

export const GROUPS = {
    lactating: 'Lactating',
    dry: 'Dry',
    pregnant: 'Pregnant',
    fattening: 'Fattening',
    calf: 'Calf',
};

/**
 * @param {string|null|undefined} group
 * @returns {string}
 */
export function groupLabel(group) {
    return group ? (GROUPS[group] ?? group) : '—';
}
