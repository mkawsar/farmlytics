<template>
    <div class="overflow-hidden shadow-md sm:rounded-lg">
        <slot />
        <slot name="pagination" />
    </div>
</template>

<script setup>
/**
 * Reusable wrapper for a data table with optional pagination.
 * Use default slot for the table (e.g. FwbTable). Use #pagination slot for Pagination component.
 *
 * Example:
 *   <DataTable>
 *     <FwbTable hoverable>
 *       <FwbTableHead>...</FwbTableHead>
 *       <FwbTableBody>...</FwbTableBody>
 *     </FwbTable>
 *     <template #pagination>
 *       <Pagination :paginator="items" />
 *     </template>
 *   </DataTable>
 */
</script>
