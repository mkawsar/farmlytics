/**
 * Expense type enum. Keys match backend App\Enums\ExpenseType.
 */

export const EXPENSE_TYPES = {
    fodder: 'Fodder',
    concentrate: 'Concentrate',
    medicine: 'Medicine',
    cow_purchase: 'Cow purchase',
    labour: 'Labour',
    electricity: 'Electricity',
    water: 'Water',
    other: 'Other',
};

export function expenseTypeLabel(value) {
    return value ? (EXPENSE_TYPES[value] ?? value) : '—';
}
