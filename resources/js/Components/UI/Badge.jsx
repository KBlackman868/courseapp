const variants = {
    gray: 'bg-gray-100 text-gray-800',
    red: 'bg-red-100 text-red-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    green: 'bg-green-100 text-green-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    pink: 'bg-pink-100 text-pink-800',
};

const sizes = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-0.5 text-sm',
    lg: 'px-3 py-1 text-sm',
};

const dotVariants = {
    gray: 'bg-gray-400',
    red: 'bg-red-400',
    yellow: 'bg-yellow-400',
    green: 'bg-green-400',
    blue: 'bg-blue-400',
    indigo: 'bg-indigo-400',
    purple: 'bg-purple-400',
    pink: 'bg-pink-400',
};

export function Badge({
    children,
    variant = 'gray',
    size = 'md',
    dot = false,
    removable = false,
    onRemove,
    className = '',
}) {
    return (
        <span
            className={`inline-flex items-center rounded-full font-medium ${variants[variant]} ${sizes[size]} ${className}`}
        >
            {dot && (
                <span
                    className={`-ml-0.5 mr-1.5 h-2 w-2 rounded-full ${dotVariants[variant]}`}
                    aria-hidden="true"
                />
            )}
            {children}
            {removable && (
                <button
                    type="button"
                    onClick={onRemove}
                    className="ml-1 -mr-1 inline-flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full text-current hover:bg-black/10 focus:bg-black/10 focus:outline-none"
                >
                    <span className="sr-only">Remove</span>
                    <svg className="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                        <path strokeLinecap="round" d="M1 1l6 6m0-6L1 7" />
                    </svg>
                </button>
            )}
        </span>
    );
}

// Preset status badges
export function StatusBadge({ status }) {
    const statusConfig = {
        pending: { variant: 'yellow', label: 'Pending' },
        approved: { variant: 'green', label: 'Approved' },
        rejected: { variant: 'red', label: 'Rejected' },
        active: { variant: 'green', label: 'Active' },
        inactive: { variant: 'gray', label: 'Inactive' },
        completed: { variant: 'blue', label: 'Completed' },
        failed: { variant: 'red', label: 'Failed' },
        syncing: { variant: 'indigo', label: 'Syncing' },
    };

    const config = statusConfig[status] || { variant: 'gray', label: status };

    return (
        <Badge variant={config.variant} dot>
            {config.label}
        </Badge>
    );
}

export default Badge;
