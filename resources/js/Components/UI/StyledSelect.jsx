import { ChevronUpDownIcon } from '@heroicons/react/24/outline';

const sizeClasses = {
    sm: 'py-1.5 pl-3 pr-8 text-xs',
    md: 'py-2 pl-3.5 pr-9 text-sm',
    lg: 'py-2.5 pl-4 pr-10 text-sm',
};

const iconSizeClasses = {
    sm: 'h-4 w-4 right-2',
    md: 'h-4 w-4 right-2.5',
    lg: 'h-5 w-5 right-3',
};

export default function StyledSelect({
    value,
    onChange,
    children,
    disabled = false,
    size = 'md',
    className = '',
    ...props
}) {
    return (
        <div className="relative inline-flex">
            <select
                value={value}
                onChange={onChange}
                disabled={disabled}
                className={[
                    'appearance-none rounded-lg border border-gray-300 bg-white font-medium text-gray-700',
                    'shadow-sm transition-all duration-150',
                    'hover:border-gray-400 hover:bg-gray-50',
                    'focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none',
                    'disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-100',
                    sizeClasses[size],
                    className,
                ].join(' ')}
                {...props}
            >
                {children}
            </select>
            <ChevronUpDownIcon
                className={[
                    'pointer-events-none absolute top-1/2 -translate-y-1/2 text-gray-400',
                    iconSizeClasses[size],
                ].join(' ')}
            />
        </div>
    );
}
