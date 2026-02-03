import { Link } from '@inertiajs/react';

const variants = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 border-transparent',
    secondary: 'bg-white text-gray-700 hover:bg-gray-50 focus:ring-indigo-500 border-gray-300',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border-transparent',
    success: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 border-transparent',
    warning: 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 border-transparent',
    ghost: 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500 border-transparent',
};

const sizes = {
    xs: 'px-2.5 py-1.5 text-xs',
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-2.5 text-base',
    xl: 'px-6 py-3 text-base',
};

export function Button({
    children,
    variant = 'primary',
    size = 'md',
    disabled = false,
    loading = false,
    icon: Icon,
    iconPosition = 'left',
    className = '',
    ...props
}) {
    const baseClasses = 'inline-flex items-center justify-center font-medium rounded-md border shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed';

    return (
        <button
            className={`${baseClasses} ${variants[variant]} ${sizes[size]} ${className}`}
            disabled={disabled || loading}
            {...props}
        >
            {loading && (
                <svg
                    className="animate-spin -ml-1 mr-2 h-4 w-4"
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
            )}
            {Icon && iconPosition === 'left' && !loading && (
                <Icon className="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
            )}
            {children}
            {Icon && iconPosition === 'right' && !loading && (
                <Icon className="ml-2 -mr-0.5 h-5 w-5" aria-hidden="true" />
            )}
        </button>
    );
}

export function LinkButton({
    children,
    href,
    variant = 'primary',
    size = 'md',
    icon: Icon,
    iconPosition = 'left',
    className = '',
    ...props
}) {
    const baseClasses = 'inline-flex items-center justify-center font-medium rounded-md border shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150';

    return (
        <Link
            href={href}
            className={`${baseClasses} ${variants[variant]} ${sizes[size]} ${className}`}
            {...props}
        >
            {Icon && iconPosition === 'left' && (
                <Icon className="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
            )}
            {children}
            {Icon && iconPosition === 'right' && (
                <Icon className="ml-2 -mr-0.5 h-5 w-5" aria-hidden="true" />
            )}
        </Link>
    );
}

export function IconButton({
    icon: Icon,
    variant = 'ghost',
    size = 'md',
    label,
    className = '',
    ...props
}) {
    const iconSizes = {
        xs: 'p-1',
        sm: 'p-1.5',
        md: 'p-2',
        lg: 'p-2.5',
        xl: 'p-3',
    };

    const iconDimensions = {
        xs: 'h-4 w-4',
        sm: 'h-4 w-4',
        md: 'h-5 w-5',
        lg: 'h-6 w-6',
        xl: 'h-6 w-6',
    };

    const baseClasses = 'inline-flex items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150';

    return (
        <button
            className={`${baseClasses} ${variants[variant]} ${iconSizes[size]} ${className}`}
            {...props}
        >
            <span className="sr-only">{label}</span>
            <Icon className={iconDimensions[size]} aria-hidden="true" />
        </button>
    );
}

export default Button;
