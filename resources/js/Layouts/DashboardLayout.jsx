import { Fragment } from 'react';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems, Transition } from '@headlessui/react';
import { Bars3Icon, BellIcon, XMarkIcon, ChevronDownIcon } from '@heroicons/react/24/outline';
import { Link, usePage } from '@inertiajs/react';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', routeName: 'dashboard' },
    { name: 'My Courses', href: '/mycourses', routeName: 'mycourses' },
    { name: 'Course Catalog', href: '/catalog', routeName: 'catalog' },
];

const adminNavigation = [
    { name: 'Dashboard', href: '/admin/dashboard', routeName: 'dashboard.superadmin' },
    { name: 'Users', href: '/admin/users', routeName: 'admin.users.*' },
    { name: 'Courses', href: '/courses', routeName: 'courses.index' },
    { name: 'Access Requests', href: '/admin/course-access-requests', routeName: 'admin.course-access-requests.*' },
    { name: 'Enrollment Requests', href: '/admin/enrollment-requests', routeName: 'admin.enrollment-requests.*' },
    { name: 'Activity Logs', href: '/admin/activity-logs', routeName: 'admin.activity-logs.*' },
];

function classNames(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function DashboardLayout({ children, header }) {
    const { auth, url } = usePage().props;
    const user = auth?.user;

    // Determine if user is admin
    const isAdmin = user?.roles?.some(role =>
        ['admin', 'superadmin', 'course_admin'].includes(role.name)
    );

    const navItems = isAdmin ? adminNavigation : navigation;

    // Check if current route matches
    const isCurrentRoute = (routeName) => {
        if (!routeName) return false;
        try {
            if (routeName.includes('*')) {
                const base = routeName.replace('.*', '');
                return route().current(base) || route().current(routeName);
            }
            return route().current(routeName);
        } catch {
            return false;
        }
    };

    const userNavigation = [
        { name: 'Your Profile', href: route('profile.show') },
        { name: 'Settings', href: route('profile.settings') },
    ];

    return (
        <div className="min-h-screen bg-gray-50">
            <Disclosure as="nav" className="bg-white border-b border-gray-200">
                {({ open, close }) => (
                    <>
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="flex h-16 justify-between">
                                {/* Logo and Desktop Nav */}
                                <div className="flex">
                                    <div className="flex flex-shrink-0 items-center">
                                        <Link href="/" className="flex items-center gap-2">
                                            <div className="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                                <span className="text-white font-bold text-sm">MOH</span>
                                            </div>
                                            <span className="hidden sm:block text-lg font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                                Learning
                                            </span>
                                        </Link>
                                    </div>

                                    {/* Desktop Navigation */}
                                    <div className="hidden sm:ml-8 sm:flex sm:space-x-1">
                                        {navItems.map((item) => (
                                            <Link
                                                key={item.name}
                                                href={item.href}
                                                className={classNames(
                                                    isCurrentRoute(item.routeName)
                                                        ? 'bg-indigo-50 text-indigo-700 border-indigo-500'
                                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                                                    'inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-150'
                                                )}
                                                aria-current={isCurrentRoute(item.routeName) ? 'page' : undefined}
                                            >
                                                {item.name}
                                            </Link>
                                        ))}
                                    </div>
                                </div>

                                {/* Right side - Notifications + User Menu */}
                                <div className="hidden sm:ml-6 sm:flex sm:items-center sm:gap-3">
                                    {/* Notifications */}
                                    <button
                                        type="button"
                                        className="relative rounded-full bg-white p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                    >
                                        <span className="sr-only">View notifications</span>
                                        <BellIcon className="h-5 w-5" aria-hidden="true" />
                                        {/* Notification badge */}
                                        <span className="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white" />
                                    </button>

                                    {/* User Dropdown */}
                                    <Menu as="div" className="relative">
                                        <MenuButton className="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                            <img
                                                className="h-8 w-8 rounded-full object-cover ring-2 ring-gray-100"
                                                src={user?.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.first_name || 'U')}&background=6366f1&color=fff`}
                                                alt=""
                                            />
                                            <div className="hidden lg:block text-left">
                                                <p className="text-sm font-medium text-gray-700">
                                                    {user?.first_name} {user?.last_name}
                                                </p>
                                                <p className="text-xs text-gray-500 truncate max-w-[150px]">
                                                    {user?.email}
                                                </p>
                                            </div>
                                            <ChevronDownIcon className="h-4 w-4 text-gray-400" aria-hidden="true" />
                                        </MenuButton>

                                        <Transition
                                            as={Fragment}
                                            enter="transition ease-out duration-100"
                                            enterFrom="transform opacity-0 scale-95"
                                            enterTo="transform opacity-100 scale-100"
                                            leave="transition ease-in duration-75"
                                            leaveFrom="transform opacity-100 scale-100"
                                            leaveTo="transform opacity-0 scale-95"
                                        >
                                            <MenuItems className="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                {/* User info header */}
                                                <div className="px-4 py-3 border-b border-gray-100">
                                                    <p className="text-sm font-medium text-gray-900">
                                                        {user?.first_name} {user?.last_name}
                                                    </p>
                                                    <p className="text-xs text-gray-500 truncate">
                                                        {user?.email}
                                                    </p>
                                                    {isAdmin && (
                                                        <span className="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                            Admin
                                                        </span>
                                                    )}
                                                </div>

                                                {userNavigation.map((item) => (
                                                    <MenuItem key={item.name}>
                                                        {({ active }) => (
                                                            <Link
                                                                href={item.href}
                                                                className={classNames(
                                                                    active ? 'bg-gray-50' : '',
                                                                    'block px-4 py-2 text-sm text-gray-700'
                                                                )}
                                                            >
                                                                {item.name}
                                                            </Link>
                                                        )}
                                                    </MenuItem>
                                                ))}

                                                <div className="border-t border-gray-100 mt-1 pt-1">
                                                    <MenuItem>
                                                        {({ active }) => (
                                                            <Link
                                                                href={route('logout')}
                                                                method="post"
                                                                as="button"
                                                                className={classNames(
                                                                    active ? 'bg-gray-50' : '',
                                                                    'block w-full text-left px-4 py-2 text-sm text-red-600'
                                                                )}
                                                            >
                                                                Sign out
                                                            </Link>
                                                        )}
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </Transition>
                                    </Menu>
                                </div>

                                {/* Mobile menu button */}
                                <div className="flex items-center sm:hidden">
                                    <DisclosureButton className="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-colors">
                                        <span className="sr-only">Open main menu</span>
                                        {open ? (
                                            <XMarkIcon className="block h-6 w-6" aria-hidden="true" />
                                        ) : (
                                            <Bars3Icon className="block h-6 w-6" aria-hidden="true" />
                                        )}
                                    </DisclosureButton>
                                </div>
                            </div>
                        </div>

                        {/* Mobile menu panel */}
                        <DisclosurePanel className="sm:hidden">
                            <div className="space-y-1 pb-3 pt-2 px-2">
                                {navItems.map((item) => (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        onClick={() => close()}
                                        className={classNames(
                                            isCurrentRoute(item.routeName)
                                                ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                                : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                                            'block border-l-4 py-2 pl-3 pr-4 text-base font-medium transition-colors'
                                        )}
                                        aria-current={isCurrentRoute(item.routeName) ? 'page' : undefined}
                                    >
                                        {item.name}
                                    </Link>
                                ))}
                            </div>

                            {/* Mobile user info */}
                            <div className="border-t border-gray-200 pb-3 pt-4">
                                <div className="flex items-center px-4">
                                    <div className="flex-shrink-0">
                                        <img
                                            className="h-10 w-10 rounded-full object-cover"
                                            src={user?.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.first_name || 'U')}&background=6366f1&color=fff`}
                                            alt=""
                                        />
                                    </div>
                                    <div className="ml-3">
                                        <div className="text-base font-medium text-gray-800">
                                            {user?.first_name} {user?.last_name}
                                        </div>
                                        <div className="text-sm font-medium text-gray-500">
                                            {user?.email}
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        className="ml-auto flex-shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        <span className="sr-only">View notifications</span>
                                        <BellIcon className="h-6 w-6" aria-hidden="true" />
                                    </button>
                                </div>
                                <div className="mt-3 space-y-1 px-2">
                                    {userNavigation.map((item) => (
                                        <Link
                                            key={item.name}
                                            href={item.href}
                                            onClick={() => close()}
                                            className="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition-colors"
                                        >
                                            {item.name}
                                        </Link>
                                    ))}
                                    <Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                        onClick={() => close()}
                                        className="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 transition-colors"
                                    >
                                        Sign out
                                    </Link>
                                </div>
                            </div>
                        </DisclosurePanel>
                    </>
                )}
            </Disclosure>

            {/* Page Header */}
            {header && (
                <header className="bg-white shadow-sm">
                    <div className="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            {/* Main Content */}
            <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {children}
            </main>
        </div>
    );
}
