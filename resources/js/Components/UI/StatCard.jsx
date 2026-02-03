import { ArrowTrendingUpIcon, ArrowTrendingDownIcon } from '@heroicons/react/24/outline';

const colorClasses = {
    indigo: {
        bg: 'bg-indigo-500',
        light: 'bg-indigo-50',
        text: 'text-indigo-600',
    },
    green: {
        bg: 'bg-green-500',
        light: 'bg-green-50',
        text: 'text-green-600',
    },
    yellow: {
        bg: 'bg-yellow-500',
        light: 'bg-yellow-50',
        text: 'text-yellow-600',
    },
    red: {
        bg: 'bg-red-500',
        light: 'bg-red-50',
        text: 'text-red-600',
    },
    blue: {
        bg: 'bg-blue-500',
        light: 'bg-blue-50',
        text: 'text-blue-600',
    },
    purple: {
        bg: 'bg-purple-500',
        light: 'bg-purple-50',
        text: 'text-purple-600',
    },
};

export function StatCard({
    title,
    value,
    change,
    changeType = 'increase',
    changeLabel,
    icon: Icon,
    color = 'indigo',
    href,
    variant = 'default',
}) {
    const colors = colorClasses[color] || colorClasses.indigo;

    const Content = () => (
        <>
            {variant === 'default' && (
                <>
                    <dt>
                        <div className={`absolute rounded-md ${colors.bg} p-3`}>
                            <Icon className="h-6 w-6 text-white" aria-hidden="true" />
                        </div>
                        <p className="ml-16 truncate text-sm font-medium text-gray-500">{title}</p>
                    </dt>
                    <dd className="ml-16 flex items-baseline">
                        <p className="text-2xl font-semibold text-gray-900">{value}</p>
                        {change && (
                            <p
                                className={`ml-2 flex items-baseline text-sm font-semibold ${
                                    changeType === 'increase' ? 'text-green-600' : 'text-red-600'
                                }`}
                            >
                                {changeType === 'increase' ? (
                                    <ArrowTrendingUpIcon
                                        className="h-5 w-5 flex-shrink-0 self-center text-green-500"
                                        aria-hidden="true"
                                    />
                                ) : (
                                    <ArrowTrendingDownIcon
                                        className="h-5 w-5 flex-shrink-0 self-center text-red-500"
                                        aria-hidden="true"
                                    />
                                )}
                                <span className="ml-1">{change}</span>
                            </p>
                        )}
                        {changeLabel && (
                            <span className="ml-2 text-sm text-gray-500">{changeLabel}</span>
                        )}
                    </dd>
                </>
            )}

            {variant === 'compact' && (
                <div className="flex items-center">
                    <div className={`flex-shrink-0 rounded-md ${colors.light} p-3`}>
                        <Icon className={`h-6 w-6 ${colors.text}`} aria-hidden="true" />
                    </div>
                    <div className="ml-4">
                        <p className="text-sm font-medium text-gray-500">{title}</p>
                        <p className="text-lg font-semibold text-gray-900">{value}</p>
                    </div>
                </div>
            )}

            {variant === 'centered' && (
                <div className="text-center">
                    <div className={`mx-auto w-12 h-12 rounded-full ${colors.light} flex items-center justify-center`}>
                        <Icon className={`h-6 w-6 ${colors.text}`} aria-hidden="true" />
                    </div>
                    <p className="mt-3 text-3xl font-bold text-gray-900">{value}</p>
                    <p className="mt-1 text-sm font-medium text-gray-500">{title}</p>
                    {change && (
                        <p
                            className={`mt-1 text-sm font-medium ${
                                changeType === 'increase' ? 'text-green-600' : 'text-red-600'
                            }`}
                        >
                            {changeType === 'increase' ? '+' : ''}{change}
                        </p>
                    )}
                </div>
            )}
        </>
    );

    if (href) {
        return (
            <a
                href={href}
                className="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow hover:shadow-md transition-shadow sm:px-6 sm:py-6"
            >
                <Content />
            </a>
        );
    }

    return (
        <div className="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <Content />
        </div>
    );
}

export default StatCard;
