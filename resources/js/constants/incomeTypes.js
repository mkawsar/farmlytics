/**
 * Income type enum. Keys match backend App\Enums\IncomeType.
 */

export const INCOME_TYPES = {
    milk_sale: 'Milk sale',
    animal_sale: 'Animal sale',
    dung_sale: 'Dung sale',
    biogas_sale: 'Biogas sale',
    calf_sale: 'Calf sale',
    other: 'Other',
};

export function incomeTypeLabel(value) {
    return value ? (INCOME_TYPES[value] ?? value) : '—';
}
