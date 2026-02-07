/**
 * Payment status enum. Keys match backend App\Enums\PaymentStatus.
 */

export const PAYMENT_STATUSES = {
    pending: 'Pending',
    partial: 'Partial',
    paid: 'Paid',
};

export function paymentStatusLabel(value) {
    return value ? (PAYMENT_STATUSES[value] ?? value) : '—';
}
