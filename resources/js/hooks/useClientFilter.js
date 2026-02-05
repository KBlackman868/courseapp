import { useState, useMemo, useDeferredValue, useCallback } from 'react';

/**
 * useClientFilter - A reusable hook for client-side filtering with search and dropdown filters.
 *
 * Features:
 * - Case-insensitive text search across multiple fields
 * - Multiple dropdown/select filters
 * - useDeferredValue for smooth typing (prevents UI jitter)
 * - Optional debounce via useDeferredValue (built-in to React 18+)
 * - Client-side pagination support
 *
 * @param {Object} options
 * @param {Array} options.items - The array of items to filter (from Inertia props)
 * @param {Array<string>} options.searchFields - Fields to search in (e.g., ['name', 'email'])
 * @param {number} options.itemsPerPage - Items per page for pagination (default: 20)
 *
 * @returns {Object} Filter state and helpers
 *
 * @example
 * const {
 *   query,
 *   setQuery,
 *   filters,
 *   setFilter,
 *   filteredItems,
 *   paginatedItems,
 *   currentPage,
 *   setCurrentPage,
 *   totalPages,
 *   clearAll,
 * } = useClientFilter({
 *   items: users,
 *   searchFields: ['first_name', 'last_name', 'email'],
 *   itemsPerPage: 20,
 * });
 */
export function useClientFilter({
    items = [],
    searchFields = [],
    itemsPerPage = 20,
}) {
    // Local state for search query
    const [query, setQuery] = useState('');

    // Local state for dropdown filters (key-value pairs)
    const [filters, setFilters] = useState({});

    // Pagination state
    const [currentPage, setCurrentPage] = useState(1);

    // Use deferred value for search to prevent UI jitter during rapid typing
    // This is React 18's built-in way to handle this - no manual debounce needed
    const deferredQuery = useDeferredValue(query);

    /**
     * Set a single filter value
     * @param {string} key - Filter key (e.g., 'role', 'status')
     * @param {string} value - Filter value (e.g., 'admin', 'active')
     */
    const setFilter = useCallback((key, value) => {
        setFilters((prev) => ({
            ...prev,
            [key]: value,
        }));
        // Reset to page 1 when filter changes
        setCurrentPage(1);
    }, []);

    /**
     * Clear all filters and search query
     */
    const clearAll = useCallback(() => {
        setQuery('');
        setFilters({});
        setCurrentPage(1);
    }, []);

    /**
     * Handle search input change
     */
    const handleQueryChange = useCallback((e) => {
        setQuery(e.target.value);
        setCurrentPage(1); // Reset to page 1 when search changes
    }, []);

    /**
     * Filter items based on search query and dropdown filters
     * Uses useMemo to avoid unnecessary recalculations
     */
    const filteredItems = useMemo(() => {
        let result = [...items];

        // Apply text search (case-insensitive)
        if (deferredQuery.trim()) {
            const searchLower = deferredQuery.toLowerCase().trim();
            result = result.filter((item) =>
                searchFields.some((field) => {
                    // Handle nested fields like 'roles.0.name'
                    const value = getNestedValue(item, field);
                    if (value === null || value === undefined) return false;
                    return String(value).toLowerCase().includes(searchLower);
                })
            );
        }

        // Apply dropdown filters
        Object.entries(filters).forEach(([key, filterValue]) => {
            // Skip empty/null/"all" filter values
            if (!filterValue || filterValue === '' || filterValue === 'all') {
                return;
            }

            result = result.filter((item) => {
                const itemValue = getNestedValue(item, key);

                // Special handling for array fields (e.g., roles)
                if (Array.isArray(itemValue)) {
                    return itemValue.some(
                        (v) =>
                            String(v?.name || v).toLowerCase() ===
                            String(filterValue).toLowerCase()
                    );
                }

                // Standard comparison (case-insensitive for strings)
                if (typeof itemValue === 'string') {
                    return itemValue.toLowerCase() === String(filterValue).toLowerCase();
                }

                return itemValue === filterValue;
            });
        });

        return result;
    }, [items, deferredQuery, filters, searchFields]);

    /**
     * Calculate paginated items
     */
    const paginatedItems = useMemo(() => {
        const startIndex = (currentPage - 1) * itemsPerPage;
        return filteredItems.slice(startIndex, startIndex + itemsPerPage);
    }, [filteredItems, currentPage, itemsPerPage]);

    /**
     * Calculate total pages
     */
    const totalPages = useMemo(() => {
        return Math.ceil(filteredItems.length / itemsPerPage);
    }, [filteredItems.length, itemsPerPage]);

    /**
     * Go to specific page
     */
    const goToPage = useCallback(
        (page) => {
            const validPage = Math.max(1, Math.min(page, totalPages || 1));
            setCurrentPage(validPage);
        },
        [totalPages]
    );

    return {
        // Search
        query,
        setQuery: handleQueryChange,
        clearQuery: () => {
            setQuery('');
            setCurrentPage(1);
        },

        // Filters
        filters,
        setFilter,
        clearFilters: () => {
            setFilters({});
            setCurrentPage(1);
        },

        // Combined clear
        clearAll,

        // Results
        filteredItems,
        paginatedItems,
        totalCount: filteredItems.length,

        // Pagination
        currentPage,
        setCurrentPage: goToPage,
        totalPages,
        hasNextPage: currentPage < totalPages,
        hasPrevPage: currentPage > 1,
        nextPage: () => goToPage(currentPage + 1),
        prevPage: () => goToPage(currentPage - 1),

        // Utility - check if any filter is active
        isFiltered: query.trim() !== '' || Object.values(filters).some((v) => v && v !== 'all'),
    };
}

/**
 * Helper: Get nested value from object using dot notation
 * e.g., getNestedValue(user, 'roles.0.name') => 'admin'
 */
function getNestedValue(obj, path) {
    if (!path) return obj;

    const keys = path.split('.');
    let value = obj;

    for (const key of keys) {
        if (value === null || value === undefined) return undefined;
        value = value[key];
    }

    return value;
}

export default useClientFilter;
