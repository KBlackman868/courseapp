export function Card({ children, className = '', padding = true }) {
    return (
        <div className={`bg-white rounded-lg shadow ${padding ? 'p-6' : ''} ${className}`}>
            {children}
        </div>
    );
}

export function CardHeader({ title, description, actions, className = '' }) {
    return (
        <div className={`border-b border-gray-200 pb-4 mb-4 ${className}`}>
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-base font-semibold leading-6 text-gray-900">{title}</h3>
                    {description && (
                        <p className="mt-1 text-sm text-gray-500">{description}</p>
                    )}
                </div>
                {actions && <div className="flex gap-2">{actions}</div>}
            </div>
        </div>
    );
}

export function CardBody({ children, className = '' }) {
    return <div className={className}>{children}</div>;
}

export function CardFooter({ children, className = '' }) {
    return (
        <div className={`border-t border-gray-200 pt-4 mt-4 ${className}`}>
            {children}
        </div>
    );
}

export default Card;
