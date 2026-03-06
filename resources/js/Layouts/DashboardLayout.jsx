import { Fragment, useState } from 'react';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems, Transition } from '@headlessui/react';
import { Bars3Icon, XMarkIcon, ChevronDownIcon } from '@heroicons/react/24/outline';
import { Link, usePage } from '@inertiajs/react';
import Footer from '@/Components/Footer';

function classNames(...classes) {
    return classes.filter(Boolean).join(' ');
}

function getUserRole(user) {
    if (!user?.roles?.length) return 'moh_staff';
    const roleNames = user.roles.map(r => r.name);
    if (roleNames.includes('superadmin')) return 'superadmin';
    if (roleNames.includes('admin')) return 'admin';
    if (roleNames.includes('course_admin')) return 'course_admin';
    if (roleNames.includes('external_staff')) return 'external_staff';
    return 'moh_staff';
}

const learnerNav = [
    { name: 'My Courses', href: '/dashboard/learner' },
    { name: 'Course Catalog', href: '/catalog' },
];

const courseAdminNav = [
    { name: 'Users', href: '/admin/users' },
    { name: 'Course Management', href: '/admin/courses' },
    { name: 'Create Course', href: '/courses/create' },
    { name: 'Account Requests', href: '/admin/account-requests' },
    { name: 'Open Moodle', href: '/moodle/sso', external: true },
];

const adminManagementItems = [
    { name: 'Users', href: '/admin/users' },
    { name: 'Roles', href: '/admin/roles' },
    { name: 'Course Management', href: '/admin/courses' },
    { name: 'Create Course', href: '/courses/create' },
    { name: 'Account Requests', href: '/admin/account-requests' },
    { name: 'Course Access', href: '/admin/course-access-requests' },
    { name: 'Moodle Status', href: '/admin/moodle/status' },
    { name: 'Open Moodle', href: '/moodle/sso', external: true },
    { name: 'Activity Logs', href: '/admin/activity-logs' },
];

function getNavItems(role) {
    if (role === 'course_admin') return courseAdminNav;
    if (role === 'admin' || role === 'superadmin') return [];
    return learnerNav;
}

function isActive(href, currentUrl) {
    if (href === '/dashboard/learner') return currentUrl.startsWith('/dashboard/learner');
    if (href === '/catalog') return currentUrl.startsWith('/catalog');
    if (href === '/admin/dashboard') return currentUrl === '/admin/dashboard';
    return currentUrl.startsWith(href);
}

const avatarFallback = (name) => `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&background=6366f1&color=fff`;

export default function DashboardLayout({ children, header }) {
    const { auth, flash } = usePage().props;
    const currentUrl = usePage().url.split('?')[0];
    const user = auth?.user;
    const role = getUserRole(user);
    const isAdminOrSuperAdmin = role === 'admin' || role === 'superadmin';
    const navItems = getNavItems(role);
    const [managementOpen, setManagementOpen] = useState(false);

    const userDropdownItems = [
        { name: 'Your Profile', href: '/profile' },
        { name: 'Change Password', href: '/profile/settings' },
    ];

    return (
        <div className="flex min-h-screen flex-col bg-gray-50">
            <Disclosure as="nav" className="bg-white border-b border-gray-200">
                {({ open, close }) => (
                    <>
                        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                            <div className="flex h-16 justify-between">
                                <div className="flex">
                                    <div className="flex flex-shrink-0 items-center">
                                        <Link href="/" className="flex items-center gap-2">
                                            <img
                                                src="/images/moh_logo.jpg"
                                                alt="MOH"
                                                className="h-8 w-8 rounded-full object-cover"
                                                onError={(e) => { e.target.onerror = null; e.target.src = avatarFallback('MOH'); }}
                                            />
                                            <span className="hidden sm:block text-lg font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                                MOH Learning
                                            </span>
                                        </Link>
                                    </div>

                                    <div className="hidden sm:ml-8 sm:flex sm:items-center sm:space-x-1">
                                        {isAdminOrSuperAdmin && (
                                            <>
                                                <Link
                                                    href="/admin/dashboard"
                                                    className={classNames(
                                                        isActive('/admin/dashboard', currentUrl)
                                                            ? 'bg-indigo-50 text-indigo-700 border-indigo-500'
                                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                                                        'inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-150'
                                                    )}
                                                >
                                                    Dashboard
                                                </Link>
                                                <div className="relative">
                                                    <button
                                                        onClick={() => setManagementOpen(!managementOpen)}
                                                        onBlur={() => setTimeout(() => setManagementOpen(false), 200)}
                                                        className={classNames(
                                                            adminManagementItems.some(item => !item.external && isActive(item.href, currentUrl))
                                                                ? 'bg-indigo-50 text-indigo-700 border-indigo-500'
                                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                                                            'inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-150 gap-1'
                                                        )}
                                                    >
                                                        Management
                                                        <ChevronDownIcon className="h-4 w-4" />
                                                    </button>
                                                    {managementOpen && (
                                                        <div className="absolute left-0 z-50 mt-1 w-56 origin-top-left rounded-lg bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                                                            {adminManagementItems.map((item) => (
                                                                item.external ? (
                                                                    <a
                                                                        key={item.name}
                                                                        href={item.href}
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        className="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-50"
                                                                        onClick={() => setManagementOpen(false)}
                                                                    >
                                                                        {item.name} &#8599;
                                                                    </a>
                                                                ) : (
                                                                    <Link
                                                                        key={item.name}
                                                                        href={item.href}
                                                                        className={classNames(
                                                                            isActive(item.href, currentUrl) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700',
                                                                            'block px-4 py-2 text-sm hover:bg-gray-50'
                                                                        )}
                                                                        onClick={() => setManagementOpen(false)}
                                                                    >
                                                                        {item.name}
                                                                    </Link>
                                                                )
                                                            ))}
                                                        </div>
                                                    )}
                                                </div>
                                            </>
                                        )}

                                        {!isAdminOrSuperAdmin && navItems.map((item) => (
                                            item.external ? (
                                                <a
                                                    key={item.name}
                                                    href={item.href}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-150"
                                                >
                                                    {item.name} &#8599;
                                                </a>
                                            ) : (
                                                <Link
                                                    key={item.name}
                                                    href={item.href}
                                                    className={classNames(
                                                        isActive(item.href, currentUrl)
                                                            ? 'bg-indigo-50 text-indigo-700 border-indigo-500'
                                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                                                        'inline-flex items-center px-3 py-2 text-sm font-medium border-b-2 transition-colors duration-150'
                                                    )}
                                                >
                                                    {item.name}
                                                </Link>
                                            )
                                        ))}
                                    </div>
                                </div>

                                <div className="hidden sm:ml-6 sm:flex sm:items-center sm:gap-3">
                                    <Menu as="div" className="relative">
                                        <MenuButton className="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                            <img
                                                className="h-8 w-8 rounded-full object-cover ring-2 ring-gray-100"
                                                src={user?.profile_photo_url || avatarFallback(user?.first_name)}
                                                alt=""
                                                onError={(e) => { e.target.onerror = null; e.target.src = avatarFallback(user?.first_name); }}
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
                                                <div className="px-4 py-3 border-b border-gray-100">
                                                    <p className="text-sm font-medium text-gray-900">
                                                        {user?.first_name} {user?.last_name}
                                                    </p>
                                                    <p className="text-xs text-gray-500 truncate">{user?.email}</p>
                                                </div>
                                                {userDropdownItems.map((item) => (
                                                    <MenuItem key={item.name}>
                                                        {({ active }) => (
                                                            <Link
                                                                href={item.href}
                                                                className={classNames(active ? 'bg-gray-50' : '', 'block px-4 py-2 text-sm text-gray-700')}
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
                                                                href="/logout"
                                                                method="post"
                                                                as="button"
                                                                className={classNames(active ? 'bg-gray-50' : '', 'block w-full text-left px-4 py-2 text-sm text-red-600')}
                                                            >
                                                                Sign Out
                                                            </Link>
                                                        )}
                                                    </MenuItem>
                                                </div>
                                            </MenuItems>
                                        </Transition>
                                    </Menu>
                                </div>

                                <div className="flex items-center sm:hidden">
                                    <DisclosureButton className="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-colors">
                                        <span className="sr-only">Open main menu</span>
                                        {open ? <XMarkIcon className="block h-6 w-6" /> : <Bars3Icon className="block h-6 w-6" />}
                                    </DisclosureButton>
                                </div>
                            </div>
                        </div>

                        <DisclosurePanel className="sm:hidden">
                            <div className="space-y-1 pb-3 pt-2 px-2">
                                {isAdminOrSuperAdmin && (
                                    <>
                                        <Link href="/admin/dashboard" onClick={() => close()} className={classNames(isActive('/admin/dashboard', currentUrl) ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50', 'block border-l-4 py-2 pl-3 pr-4 text-base font-medium')}>
                                            Dashboard
                                        </Link>
                                        <div className="pl-3 py-1"><p className="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3">Management</p></div>
                                        {adminManagementItems.map((item) => (
                                            item.external ? (
                                                <a key={item.name} href={item.href} target="_blank" rel="noopener noreferrer" onClick={() => close()} className="border-transparent text-gray-600 hover:bg-gray-50 block border-l-4 py-2 pl-6 pr-4 text-base font-medium">
                                                    {item.name} &#8599;
                                                </a>
                                            ) : (
                                                <Link key={item.name} href={item.href} onClick={() => close()} className={classNames(isActive(item.href, currentUrl) ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50', 'block border-l-4 py-2 pl-6 pr-4 text-base font-medium')}>
                                                    {item.name}
                                                </Link>
                                            )
                                        ))}
                                    </>
                                )}
                                {!isAdminOrSuperAdmin && navItems.map((item) => (
                                    item.external ? (
                                        <a key={item.name} href={item.href} target="_blank" rel="noopener noreferrer" onClick={() => close()} className="border-transparent text-gray-600 hover:bg-gray-50 block border-l-4 py-2 pl-3 pr-4 text-base font-medium">
                                            {item.name} &#8599;
                                        </a>
                                    ) : (
                                        <Link key={item.name} href={item.href} onClick={() => close()} className={classNames(isActive(item.href, currentUrl) ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50', 'block border-l-4 py-2 pl-3 pr-4 text-base font-medium')}>
                                            {item.name}
                                        </Link>
                                    )
                                ))}
                            </div>
                            <div className="border-t border-gray-200 pb-3 pt-4">
                                <div className="flex items-center px-4">
                                    <img className="h-10 w-10 rounded-full object-cover" src={user?.profile_photo_url || avatarFallback(user?.first_name)} alt="" onError={(e) => { e.target.onerror = null; e.target.src = avatarFallback(user?.first_name); }} />
                                    <div className="ml-3">
                                        <div className="text-base font-medium text-gray-800">{user?.first_name} {user?.last_name}</div>
                                        <div className="text-sm font-medium text-gray-500">{user?.email}</div>
                                    </div>
                                </div>
                                <div className="mt-3 space-y-1 px-2">
                                    {userDropdownItems.map((item) => (
                                        <Link key={item.name} href={item.href} onClick={() => close()} className="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">
                                            {item.name}
                                        </Link>
                                    ))}
                                    <Link href="/logout" method="post" as="button" onClick={() => close()} className="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50">
                                        Sign Out
                                    </Link>
                                </div>
                            </div>
                        </DisclosurePanel>
                    </>
                )}
            </Disclosure>

            {header && (
                <header className="bg-white shadow-sm">
                    <div className="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">{header}</div>
                </header>
            )}

            {flash?.success && (
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div className="rounded-md bg-green-50 p-4"><p className="text-sm font-medium text-green-800">{flash.success}</p></div>
                </div>
            )}
            {flash?.error && (
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                    <div className="rounded-md bg-red-50 p-4"><p className="text-sm font-medium text-red-800">{flash.error}</p></div>
                </div>
            )}

            <main className="flex-1 mx-auto max-w-7xl w-full px-4 py-6 sm:px-6 lg:px-8">
                {children}
            </main>

            <Footer />
        </div>
    );
}
