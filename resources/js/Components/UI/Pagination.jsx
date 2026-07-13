import { Link } from '@inertiajs/react';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/20/solid';

/**
 * Server-side pagination — works with Laravel's paginate() links.
 */
export function ServerPagination({ links, from, to, total }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
            {total != null && (
                <p className="text-sm text-gray-600">
                    Showing <span className="font-semibold text-gray-900">{from}</span> to{' '}
                    <span className="font-semibold text-gray-900">{to}</span> of{' '}
                    <span className="font-semibold text-gray-900">{total}</span> results
                </p>
            )}
            <nav className="inline-flex -space-x-px rounded-lg shadow-sm" aria-label="Pagination">
                {links.map((link, i) => {
                    const isFirst = i === 0;
                    const isLast = i === links.length - 1;

                    const baseClasses = [
                        'relative inline-flex items-center px-3 py-2 text-sm font-medium transition-colors',
                        'border border-gray-300',
                        'focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/30',
                        isFirst && 'rounded-l-lg',
                        isLast && 'rounded-r-lg',
                    ].filter(Boolean).join(' ');

                    if (link.active) {
                        return (
                            <span
                                key={i}
                                className={`${baseClasses} z-10 border-indigo-500 bg-indigo-50 text-indigo-700`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        );
                    }

                    if (!link.url) {
                        return (
                            <span
                                key={i}
                                className={`${baseClasses} cursor-not-allowed bg-white text-gray-300`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        );
                    }

                    return (
                        <Link
                            key={i}
                            href={link.url}
                            className={`${baseClasses} bg-white text-gray-700 hover:bg-gray-50`}
                            preserveState
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                })}
            </nav>
        </div>
    );
}

/**
 * Client-side pagination — works with local arrays.
 *
 * Props:
 *  currentPage, totalPages, onPageChange,
 *  from, to, total  (all optional for the info text)
 */
export function ClientPagination({ currentPage, totalPages, onPageChange, from, to, total }) {
    if (totalPages <= 1) return null;

    const pages = [];
    const delta = 1;
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
            pages.push(i);
        } else if (pages[pages.length - 1] !== '...') {
            pages.push('...');
        }
    }

    const btnBase =
        'relative inline-flex items-center px-3 py-2 text-sm font-medium transition-colors border border-gray-300 focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500/30';

    return (
        <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
            {total != null && (
                <p className="text-sm text-gray-600">
                    Showing <span className="font-semibold text-gray-900">{from}</span> to{' '}
                    <span className="font-semibold text-gray-900">{to}</span> of{' '}
                    <span className="font-semibold text-gray-900">{total}</span> results
                </p>
            )}
            <nav className="inline-flex -space-x-px rounded-lg shadow-sm" aria-label="Pagination">
                {/* Previous */}
                <button
                    onClick={() => onPageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                    className={`${btnBase} rounded-l-lg ${
                        currentPage === 1 ? 'cursor-not-allowed bg-white text-gray-300' : 'bg-white text-gray-700 hover:bg-gray-50'
                    }`}
                >
                    <ChevronLeftIcon className="h-4 w-4" />
                </button>

                {/* Page numbers */}
                {pages.map((page, i) => {
                    if (page === '...') {
                        return (
                            <span key={`ellipsis-${i}`} className={`${btnBase} cursor-default bg-white text-gray-400`}>
                                ...
                            </span>
                        );
                    }

                    return (
                        <button
                            key={page}
                            onClick={() => onPageChange(page)}
                            className={`${btnBase} ${
                                page === currentPage
                                    ? 'z-10 border-indigo-500 bg-indigo-50 text-indigo-700'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            }`}
                        >
                            {page}
                        </button>
                    );
                })}

                {/* Next */}
                <button
                    onClick={() => onPageChange(currentPage + 1)}
                    disabled={currentPage === totalPages}
                    className={`${btnBase} rounded-r-lg ${
                        currentPage === totalPages ? 'cursor-not-allowed bg-white text-gray-300' : 'bg-white text-gray-700 hover:bg-gray-50'
                    }`}
                >
                    <ChevronRightIcon className="h-4 w-4" />
                </button>
            </nav>
        </div>
    );
}
