<template>
    <FwbTable hoverable>
        <FwbTableHead>
            <slot name="head" />
        </FwbTableHead>
        <FwbTableBody>
            <slot />
        </FwbTableBody>
    </FwbTable>
</template>

<script setup>
import { FwbTable, FwbTableBody, FwbTableHead } from 'flowbite-vue';

/**
 * Reusable table wrapper using Flowbite. Use #head for header cells, default slot for body rows.
 * Parent should use FwbTableHeadCell in #head and FwbTableRow / FwbTableCell for rows.
 *
 * Example:
 *   <Table>
 *     <template #head>
 *       <FwbTableHeadCell>Name</FwbTableHeadCell>
 *       <FwbTableHeadCell>Actions</FwbTableHeadCell>
 *     </template>
 *     <FwbTableRow v-for="item in items" :key="item.id">
 *       <FwbTableCell>...</FwbTableCell>
 *     </FwbTableRow>
 *     <FwbTableRow v-if="empty">
 *       <FwbTableCell colspan="5">No data.</FwbTableCell>
 *     </FwbTableRow>
 *   </Table>
 */
</script>
