import { Fragment, useState } from 'react';
import { Dialog, DialogPanel, Transition, TransitionChild, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import {
    Bars3Icon,
    XMarkIcon,
    HomeIcon,
    UsersIcon,
    FolderIcon,
    ClipboardDocumentListIcon,
    Cog6ToothIcon,
    ChartBarIcon,
    BellIcon,
    ArrowRightOnRectangleIcon,
    AcademicCapIcon,
    ShieldCheckIcon,
    DocumentTextIcon,
    ChevronDownIcon,
} from '@heroicons/react/24/outline';
import { Link, usePage } from '@inertiajs/react';

const navigation = [
    { name: 'Dashboard', href: '/admin/dashboard', icon: HomeIcon, routeName: 'dashboard.superadmin' },
    { name: 'Users', href: '/admin/users', icon: UsersIcon, routeName: 'admin.users.*' },
    { name: 'Roles & Permissions', href: '/admin/roles', icon: ShieldCheckIcon, routeName: 'admin.roles.*' },
    { name: 'Courses', href: '/courses', icon: AcademicCapIcon, routeName: 'courses.*' },
    { name: 'Access Requests', href: '/admin/course-access-requests', icon: ClipboardDocumentListIcon, routeName: 'admin.course-access-requests.*', badge: true },
    { name: 'Account Requests', href: '/admin/account-requests', icon: DocumentTextIcon, routeName: 'admin.account-requests.*', badge: true },
    { name: 'Activity Logs', href: '/admin/activity-logs', icon: ChartBarIcon, routeName: 'admin.activity-logs.*' },
];

const secondaryNavigation = [
    { name: 'Moodle Status', href: '/admin/moodle/status', icon: Cog6ToothIcon },
    { name: 'Open Moodle', href: '/moodle/sso', icon: ArrowRightOnRectangleIcon, external: true },
];

function classNames(...classes) {
    return classes.filter(Boolean).join(' ');
}

export default function AdminLayout({ children, header, title }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { auth, pendingCounts = {} } = usePage().props;
    const user = auth?.user;

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

    const NavItem = ({ item, mobile = false }) => {
        const current = isCurrentRoute(item.routeName);
        const baseClasses = mobile
            ? 'group flex items-center gap-x-3 rounded-md p-2 text-sm font-medium'
            : 'group flex items-center gap-x-3 rounded-md p-2 text-sm font-medium leading-6';

        return (
            <Link
                href={item.href}
                onClick={mobile ? () => setSidebarOpen(false) : undefined}
                target={item.external ? '_blank' : undefined}
                className={classNames(
                    current
                        ? 'bg-indigo-700 text-white'
                        : 'text-indigo-100 hover:bg-indigo-700 hover:text-white',
                    baseClasses
                )}
            >
                <item.icon
                    className={classNames(
                        current ? 'text-white' : 'text-indigo-200 group-hover:text-white',
                        'h-5 w-5 shrink-0'
                    )}
                    aria-hidden="true"
                />
                {item.name}
                {item.badge && pendingCounts[item.routeName] > 0 && (
                    <span className="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium bg-indigo-500 text-white rounded-full">
                        {pendingCounts[item.routeName]}
                    </span>
                )}
            </Link>
        );
    };

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Mobile sidebar */}
            <Transition show={sidebarOpen} as={Fragment}>
                <Dialog as="div" className="relative z-50 lg:hidden" onClose={setSidebarOpen}>
                    <TransitionChild
                        as={Fragment}
                        enter="transition-opacity ease-linear duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="transition-opacity ease-linear duration-300"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="fixed inset-0 bg-gray-900/80" />
                    </TransitionChild>

                    <div className="fixed inset-0 flex">
                        <TransitionChild
                            as={Fragment}
                            enter="transition ease-in-out duration-300 transform"
                            enterFrom="-translate-x-full"
                            enterTo="translate-x-0"
                            leave="transition ease-in-out duration-300 transform"
                            leaveFrom="translate-x-0"
                            leaveTo="-translate-x-full"
                        >
                            <DialogPanel className="relative mr-16 flex w-full max-w-xs flex-1">
                                <TransitionChild
                                    as={Fragment}
                                    enter="ease-in-out duration-300"
                                    enterFrom="opacity-0"
                                    enterTo="opacity-100"
                                    leave="ease-in-out duration-300"
                                    leaveFrom="opacity-100"
                                    leaveTo="opacity-0"
                                >
                                    <div className="absolute left-full top-0 flex w-16 justify-center pt-5">
                                        <button
                                            type="button"
                                            className="-m-2.5 p-2.5"
                                            onClick={() => setSidebarOpen(false)}
                                        >
                                            <span className="sr-only">Close sidebar</span>
                                            <XMarkIcon className="h-6 w-6 text-white" aria-hidden="true" />
                                        </button>
                                    </div>
                                </TransitionChild>

                                {/* Mobile Sidebar content */}
                                <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4">
                                    <div className="flex h-16 shrink-0 items-center">
                                        <Link href="/" className="flex items-center gap-2">
                                            <div className="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                                                <span className="text-white font-bold text-sm">MOH</span>
                                            </div>
                                            <span className="text-white font-semibold">Admin Panel</span>
                                        </Link>
                                    </div>
                                    <nav className="flex flex-1 flex-col">
                                        <ul role="list" className="flex flex-1 flex-col gap-y-7">
                                            <li>
                                                <ul role="list" className="-mx-2 space-y-1">
                                                    {navigation.map((item) => (
                                                        <li key={item.name}>
                                                            <NavItem item={item} mobile />
                                                        </li>
                                                    ))}
                                                </ul>
                                            </li>
                                            <li>
                                                <div className="text-xs font-semibold leading-6 text-indigo-200">
                                                    Moodle
                                                </div>
                                                <ul role="list" className="-mx-2 mt-2 space-y-1">
                                                    {secondaryNavigation.map((item) => (
                                                        <li key={item.name}>
                                                            <NavItem item={item} mobile />
                                                        </li>
                                                    ))}
                                                </ul>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </Dialog>
            </Transition>

            {/* Desktop sidebar */}
            <div className="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
                <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-indigo-600 px-6 pb-4">
                    <div className="flex h-16 shrink-0 items-center">
                        <Link href="/" className="flex items-center gap-2">
                            <div className="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                                <span className="text-white font-bold text-sm">MOH</span>
                            </div>
                            <span className="text-white font-semibold">Admin Panel</span>
                        </Link>
                    </div>
                    <nav className="flex flex-1 flex-col">
                        <ul role="list" className="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" className="-mx-2 space-y-1">
                                    {navigation.map((item) => (
                                        <li key={item.name}>
                                            <NavItem item={item} />
                                        </li>
                                    ))}
                                </ul>
                            </li>
                            <li>
                                <div className="text-xs font-semibold leading-6 text-indigo-200 uppercase tracking-wider">
                                    Moodle
                                </div>
                                <ul role="list" className="-mx-2 mt-2 space-y-1">
                                    {secondaryNavigation.map((item) => (
                                        <li key={item.name}>
                                            <NavItem item={item} />
                                        </li>
                                    ))}
                                </ul>
                            </li>
                            <li className="mt-auto">
                                <Link
                                    href={route('profile.show')}
                                    className="group -mx-2 flex items-center gap-x-3 rounded-md p-2 text-sm font-medium leading-6 text-indigo-100 hover:bg-indigo-700 hover:text-white"
                                >
                                    <Cog6ToothIcon className="h-5 w-5 shrink-0 text-indigo-200 group-hover:text-white" />
                                    Settings
                                </Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            {/* Main content area */}
            <div className="lg:pl-64">
                {/* Top bar */}
                <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        className="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <span className="sr-only">Open sidebar</span>
                        <Bars3Icon className="h-6 w-6" aria-hidden="true" />
                    </button>

                    {/* Separator */}
                    <div className="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true" />

                    <div className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        {/* Page title / breadcrumb area */}
                        <div className="flex flex-1 items-center">
                            {title && (
                                <h1 className="text-lg font-semibold text-gray-900">{title}</h1>
                            )}
                        </div>

                        {/* Right side actions */}
                        <div className="flex items-center gap-x-4 lg:gap-x-6">
                            {/* Notifications */}
                            <button
                                type="button"
                                className="relative -m-2.5 p-2.5 text-gray-400 hover:text-gray-500"
                            >
                                <span className="sr-only">View notifications</span>
                                <BellIcon className="h-6 w-6" aria-hidden="true" />
                                <span className="absolute top-2 right-2 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white" />
                            </button>

                            {/* Separator */}
                            <div className="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true" />

                            {/* Profile dropdown */}
                            <Menu as="div" className="relative">
                                <MenuButton className="-m-1.5 flex items-center p-1.5">
                                    <span className="sr-only">Open user menu</span>
                                    <img
                                        className="h-8 w-8 rounded-full bg-gray-50 object-cover"
                                        src={user?.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.first_name || 'A')}&background=6366f1&color=fff`}
                                        alt=""
                                    />
                                    <span className="hidden lg:flex lg:items-center">
                                        <span className="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">
                                            {user?.first_name} {user?.last_name}
                                        </span>
                                        <ChevronDownIcon className="ml-2 h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </span>
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
                                    <MenuItems className="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                        <MenuItem>
                                            {({ active }) => (
                                                <Link
                                                    href={route('profile.show')}
                                                    className={classNames(
                                                        active ? 'bg-gray-50' : '',
                                                        'block px-3 py-1 text-sm leading-6 text-gray-900'
                                                    )}
                                                >
                                                    Your profile
                                                </Link>
                                            )}
                                        </MenuItem>
                                        <MenuItem>
                                            {({ active }) => (
                                                <Link
                                                    href="/"
                                                    className={classNames(
                                                        active ? 'bg-gray-50' : '',
                                                        'block px-3 py-1 text-sm leading-6 text-gray-900'
                                                    )}
                                                >
                                                    Back to site
                                                </Link>
                                            )}
                                        </MenuItem>
                                        <MenuItem>
                                            {({ active }) => (
                                                <Link
                                                    href={route('logout')}
                                                    method="post"
                                                    as="button"
                                                    className={classNames(
                                                        active ? 'bg-gray-50' : '',
                                                        'block w-full text-left px-3 py-1 text-sm leading-6 text-red-600'
                                                    )}
                                                >
                                                    Sign out
                                                </Link>
                                            )}
                                        </MenuItem>
                                    </MenuItems>
                                </Transition>
                            </Menu>
                        </div>
                    </div>
                </div>

                {/* Page header */}
                {header && (
                    <header className="bg-white border-b border-gray-200">
                        <div className="px-4 py-4 sm:px-6 lg:px-8">
                            {header}
                        </div>
                    </header>
                )}

                {/* Main content */}
                <main className="py-6">
                    <div className="px-4 sm:px-6 lg:px-8">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
