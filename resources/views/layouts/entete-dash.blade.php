<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Gestion de Rendez-vous</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_app_32x32.png')}}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        /* body {
            font-family: 'Inter', sans-serif;
            background-image: url('images/background.jpeg');
        } */
        .sidebar-open {
            transform: translateX(0);
        }
        .sidebar-closed {
            transform: translateX(-100%);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header Full Width -->
    <header class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center px-4 md:px-8 py-4">
            <div class="flex items-center space-x-4">
                <!-- Hamburger Menu Button (Mobile) -->
                <button id="menuToggle" class="md:hidden p-2 text-gray-600 hover:text-blue-700 transition rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <img src="{{ asset('images/logo_web_128x48.png') }}" alt="">
            </div>
            <div class="flex items-center space-x-2 md:space-x-4">
                <!-- Heure actuelle -->
                <div class="hidden md:flex items-center space-x-2 bg-gray-50 rounded-full px-4 py-2">
                    <i class="fas fa-clock text-gray-600"></i>
                    <span id="currentTime" class="text-gray-700 font-medium text-sm"></span>
                </div>

                <!-- Notifications Dropdown -->
                <div class="relative">
                    <button id="notificationToggle" class="relative p-2 text-gray-600 hover:text-blue-700 transition bg-gray-100 rounded-full w-10 h-10">
                        <i class="fas fa-bell text-lg"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown Menu -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800">Rappels</h3>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">
                                        {{ $unreadCount }} non lue(s)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <!-- DEBUG: {{ isset($notifications) ? 'Notifications: ' . $notifications->count() : 'Pas de notifications' }} -->
                            @if(isset($notifications) && $notifications->count() > 0)
                                @foreach($notifications as $notification)
                                    <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                @if($notification->type == 'rappel_rendez_vous') bg-yellow-100
                                                @elseif($notification->type == 'nouveau_rendez_vous') bg-blue-100
                                                @elseif($notification->type == 'modification_rendez_vous') bg-purple-100
                                                @elseif($notification->type == 'annulation_rendez_vous') bg-red-100
                                                @else bg-gray-100
                                                @endif">
                                                <i class="fas
                                                    @if($notification->type == 'rappel_rendez_vous') fa-clock text-yellow-600
                                                    @elseif($notification->type == 'nouveau_rendez_vous') fa-calendar-plus text-blue-600
                                                    @elseif($notification->type == 'modification_rendez_vous') fa-edit text-purple-600
                                                    @elseif($notification->type == 'annulation_rendez_vous') fa-calendar-times text-red-600
                                                    @else fa-bell text-gray-600
                                                    @endif text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $notification->titre }}</p>
                                                <p class="text-xs text-gray-500 truncate mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-3xl mb-3"></i>
                                    <p>Aucune notification</p>
                                </div>
                            @endif
                        </div>

                        @if(isset($notifications) && $notifications->count() > 0)
                            <div class="p-3 border-t border-gray-100">
                                <a href="#" class="block text-center text-sm text-blue-700 hover:text-blue-800 font-medium">
                                    Voir toutes les notifications
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- User Dropdown -->
                <div class="relative">
                    <button id="userDropdownToggle" class="hidden md:flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 hover:bg-gray-100 transition">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                            <p class="text-xs text-gray-500">Responsable</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Mobile Avatar Only -->
                    <button id="userDropdownToggleMobile" class="md:hidden w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="py-2">
                            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-user w-5 text-gray-400"></i>
                                <span>Mon profil</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-cog w-5 text-gray-400"></i>
                                <span>Paramètres</span>
                            </a>
                        </div>

                        <div class="border-t border-gray-100 pt-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Overlay for Mobile Sidebar -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    <div class="flex pt-20">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-closed md:sidebar-open w-64 bg-gradient-to-b from-blue-900 to-blue-800 min-h-screen fixed left-0 top-20 z-40 transition-transform duration-300 md:transform-none">
            <div class="p-6">
                <nav class="space-y-2">
                    <a href="{{ route('dashboard-responsable') }}" class="flex items-center space-x-3 text-white bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->is('dashboard') ? 'bg-blue-700 shadow-lg' : 'hover:bg-blue-700/50' }}">
                        <i class="fas fa-home w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('liste-rendez-vous') }}" class="flex items-center space-x-3 text-blue-100 hover:bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200">
                        <i class="fas fa-calendar w-5"></i>
                        <span>Rendez-vous</span>
                    </a>
                    <a href="{{ route('liste-invites') }}" class="flex items-center space-x-3 text-blue-100 hover:bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200">
                        <i class="fas fa-users w-5"></i>
                        <span>Invités</span>
                    </a>
                    <a href="{{ route('liste-personnels') }}" class="flex items-center space-x-3 text-blue-100 hover:bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200">
                        <i class="fas fa-user-tie w-5"></i>
                        <span>Personnels</span>
                    </a>
                    <a href="{{ route('liste-roles') }}" class="flex items-center space-x-3 text-blue-100 hover:bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200">
                        <i class="fas fa-user-shield w-5"></i>
                        <span>Rôles</span>
                    </a>
                    <a href="{{ route('liste-notifications') }}" class="flex items-center space-x-3 text-blue-100 hover:bg-blue-700/50 px-4 py-3 rounded-lg transition-all duration-200">
                        <i class="fas fa-bell w-5"></i>
                        <span>Notifications</span>
                    </a>
                </nav>
            </div>

            <div class="absolute bottom-0 left-0 right-0 p-6">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center space-x-3 text-red-300 hover:bg-red-900/30 px-4 py-3 rounded-lg transition-all duration-200 w-full">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-0 md:ml-64 flex-1 transition-all duration-300">
            <!-- Content -->
            <div class="p-4 md:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-closed');
            sidebar.classList.toggle('sidebar-open');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('sidebar-closed');
            sidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.add('hidden');
        });

        // Notification Dropdown Toggle
        const notificationToggle = document.getElementById('notificationToggle');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationToggle && notificationDropdown) {
            notificationToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!notificationToggle.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // User Dropdown Toggle
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownToggleMobile = document.getElementById('userDropdownToggleMobile');
        const userDropdown = document.getElementById('userDropdown');

        function toggleUserDropdown(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            // Close notification dropdown if open
            if (notificationDropdown) {
                notificationDropdown.classList.add('hidden');
            }
        }

        if (userDropdownToggle) {
            userDropdownToggle.addEventListener('click', toggleUserDropdown);
        }

        if (userDropdownToggleMobile) {
            userDropdownToggleMobile.addEventListener('click', toggleUserDropdown);
        }

        // Close user dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (userDropdown) {
                if (userDropdownToggle && !userDropdownToggle.contains(e.target) &&
                    userDropdownToggleMobile && !userDropdownToggleMobile.contains(e.target) &&
                    !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            }
        });

        // Mise à jour de l'heure actuelle
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const timeString = now.toLocaleDateString('fr-FR', options);
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString.charAt(0).toUpperCase() + timeString.slice(1);
            }
        }

        // Mettre à jour l'heure immédiatement puis chaque seconde
        updateTime();
        setInterval(updateTime, 1000);

        // Actualiser les notifications toutes les 30 secondes
        function updateNotifications() {
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.unreadCount;
                    const unreadBadge = document.querySelector('#notificationToggle span');
                    if (unreadBadge) {
                        if (unreadCount > 0) {
                            unreadBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                            unreadBadge.classList.remove('hidden');
                        } else {
                            unreadBadge.classList.add('hidden');
                        }
                    }

                    // Mettre à jour la liste des notifications
                    const notificationList = document.querySelector('#notificationDropdown .max-h-96');
                    if (notificationList && data.notifications) {
                        let html = '';
                        if (data.notifications.length > 0) {
                            data.notifications.forEach(notification => {
                                const typeIcon = notification.type === 'rappel_rendez_vous' ? 'fa-clock text-yellow-600' : 'fa-bell text-gray-600';
                                const typeBg = notification.type === 'rappel_rendez_vous' ? 'bg-yellow-100' : 'bg-gray-100';
                                html += `
                                    <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${typeBg}">
                                                <i class="fas ${typeIcon} text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">${notification.titre}</p>
                                                <p class="text-xs text-gray-500 truncate mt-1">${notification.message}</p>
                                                <p class="text-xs text-gray-400 mt-1">${notification.created_at}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html = `
                                <div class="p-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-3xl mb-3"></i>
                                    <p>Aucune notification</p>
                                </div>
                            `;
                        }
                        notificationList.innerHTML = html;
                    }
                })
                .catch(error => console.error('Erreur lors de la récupération des notifications:', error));
        }

        // Actualiser les notifications immédiatement puis toutes les 30 secondes
        updateNotifications();
        setInterval(updateNotifications, 30000);
    </script>

    @stack('scripts')
</body>
</html>
