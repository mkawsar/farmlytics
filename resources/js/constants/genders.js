/**
 * Gender enum. Keys match backend App\Enums\Gender.
 */

export const GENDERS = {
    male: 'Male',
    female: 'Female',
};

/**
 * @param {string|null|undefined} gender
 * @returns {string}
 */
export function genderLabel(gender) {
    return gender ? (GENDERS[gender] ?? gender) : '—';
}
