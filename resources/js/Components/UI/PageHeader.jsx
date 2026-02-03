import { ChevronRightIcon, HomeIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';

export function PageHeader({
    title,
    description,
    breadcrumbs = [],
    actions,
    children,
}) {
    return (
        <div className="border-b border-gray-200 pb-5">
            {/* Breadcrumbs */}
            {breadcrumbs.length > 0 && (
                <nav className="flex mb-4" aria-label="Breadcrumb">
                    <ol role="list" className="flex items-center space-x-2">
                        <li>
                            <Link href="/" className="text-gray-400 hover:text-gray-500">
                                <HomeIcon className="h-5 w-5 flex-shrink-0" aria-hidden="true" />
                                <span className="sr-only">Home</span>
                            </Link>
                        </li>
                        {breadcrumbs.map((item, index) => (
                            <li key={item.name} className="flex items-center">
                                <ChevronRightIcon
                                    className="h-5 w-5 flex-shrink-0 text-gray-400"
                                    aria-hidden="true"
                                />
                                {index === breadcrumbs.length - 1 ? (
                                    <span className="ml-2 text-sm font-medium text-gray-500">
                                        {item.name}
                                    </span>
                                ) : (
                                    <Link
                                        href={item.href}
                                        className="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700"
                                    >
                                        {item.name}
                                    </Link>
                                )}
                            </li>
                        ))}
                    </ol>
                </nav>
            )}

            {/* Header content */}
            <div className="sm:flex sm:items-center sm:justify-between">
                <div className="min-w-0 flex-1">
                    <h1 className="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        {title}
                    </h1>
                    {description && (
                        <p className="mt-1 text-sm text-gray-500">{description}</p>
                    )}
                </div>
                {actions && (
                    <div className="mt-4 flex flex-shrink-0 gap-3 sm:ml-4 sm:mt-0">
                        {actions}
                    </div>
                )}
            </div>

            {/* Optional additional content */}
            {children && <div className="mt-4">{children}</div>}
        </div>
    );
}

export default PageHeader;
