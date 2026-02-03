import { useState } from 'react';
import { MagnifyingGlassIcon, FunnelIcon, ChevronUpDownIcon } from '@heroicons/react/24/outline';

export function DataTable({
    title,
    description,
    columns,
    data,
    emptyMessage = 'No data available',
    emptyIcon: EmptyIcon,
    searchable = false,
    searchPlaceholder = 'Search...',
    onSearch,
    filterable = false,
    filterOptions = [],
    onFilter,
    actions,
    loading = false,
    pagination,
}) {
    const [searchQuery, setSearchQuery] = useState('');
    const [sortColumn, setSortColumn] = useState(null);
    const [sortDirection, setSortDirection] = useState('asc');

    const handleSearch = (value) => {
        setSearchQuery(value);
        onSearch?.(value);
    };

    const handleSort = (columnKey) => {
        if (sortColumn === columnKey) {
            setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
        } else {
            setSortColumn(columnKey);
            setSortDirection('asc');
        }
    };

    return (
        <div className="overflow-hidden rounded-lg bg-white shadow">
            {/* Header */}
            {(title || searchable || filterable || actions) && (
                <div className="border-b border-gray-200 px-4 py-5 sm:px-6">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            {title && (
                                <h3 className="text-base font-semibold leading-6 text-gray-900">
                                    {title}
                                </h3>
                            )}
                            {description && (
                                <p className="mt-1 text-sm text-gray-500">{description}</p>
                            )}
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            {/* Search */}
                            {searchable && (
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon
                                            className="h-5 w-5 text-gray-400"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <input
                                        type="text"
                                        value={searchQuery}
                                        onChange={(e) => handleSearch(e.target.value)}
                                        placeholder={searchPlaceholder}
                                        className="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    />
                                </div>
                            )}

                            {/* Filter */}
                            {filterable && (
                                <button
                                    type="button"
                                    className="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                >
                                    <FunnelIcon className="-ml-0.5 h-5 w-5 text-gray-400" />
                                    Filter
                                </button>
                            )}

                            {/* Actions */}
                            {actions && <div className="flex gap-2">{actions}</div>}
                        </div>
                    </div>
                </div>
            )}

            {/* Table */}
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map((column) => (
                                <th
                                    key={column.key}
                                    scope="col"
                                    className={`px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 ${
                                        column.sortable ? 'cursor-pointer hover:bg-gray-100' : ''
                                    }`}
                                    onClick={() => column.sortable && handleSort(column.key)}
                                >
                                    <div className="flex items-center gap-1">
                                        {column.label}
                                        {column.sortable && (
                                            <ChevronUpDownIcon className="h-4 w-4 text-gray-400" />
                                        )}
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200 bg-white">
                        {loading ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-6 py-12 text-center"
                                >
                                    <div className="flex items-center justify-center">
                                        <svg
                                            className="animate-spin h-8 w-8 text-indigo-600"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                className="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                strokeWidth="4"
                                            />
                                            <path
                                                className="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            />
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        ) : data.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-6 py-12 text-center"
                                >
                                    {EmptyIcon && (
                                        <EmptyIcon className="mx-auto h-12 w-12 text-gray-400" />
                                    )}
                                    <p className="mt-2 text-sm text-gray-500">{emptyMessage}</p>
                                </td>
                            </tr>
                        ) : (
                            data.map((row, rowIndex) => (
                                <tr
                                    key={row.id || rowIndex}
                                    className="hover:bg-gray-50 transition-colors"
                                >
                                    {columns.map((column) => (
                                        <td
                                            key={column.key}
                                            className={`px-6 py-4 text-sm text-gray-900 ${
                                                column.wrap ? '' : 'whitespace-nowrap'
                                            }`}
                                        >
                                            {column.render
                                                ? column.render(row, rowIndex)
                                                : row[column.key]}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {pagination && (
                <div className="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-gray-700">
                            Showing{' '}
                            <span className="font-medium">{pagination.from}</span> to{' '}
                            <span className="font-medium">{pagination.to}</span> of{' '}
                            <span className="font-medium">{pagination.total}</span> results
                        </div>
                        <div className="flex gap-2">
                            <button
                                onClick={pagination.onPrevious}
                                disabled={!pagination.hasPrevious}
                                className="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <button
                                onClick={pagination.onNext}
                                disabled={!pagination.hasNext}
                                className="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default DataTable;
