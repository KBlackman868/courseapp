import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { Settings, KeyRound, User } from 'lucide-react';

const tabs = [
    { name: 'Settings', href: '/profile/settings', icon: Settings },
    { name: 'Change Password', href: '/profile/change-password', icon: KeyRound },
];

export function ProfileNav() {
    const currentUrl = usePage().url.split('?')[0];

    return (
        <nav className="mb-6 border-b border-gray-200">
            <div className="flex space-x-1">
                {tabs.map((tab) => {
                    const isActive = currentUrl === tab.href;
                    return (
                        <Link
                            key={tab.name}
                            href={tab.href}
                            className={cn(
                                'group inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition-colors',
                                isActive
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                            )}
                        >
                            <tab.icon
                                className={cn(
                                    'h-4 w-4',
                                    isActive
                                        ? 'text-indigo-500'
                                        : 'text-gray-400 group-hover:text-gray-500'
                                )}
                            />
                            {tab.name}
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
