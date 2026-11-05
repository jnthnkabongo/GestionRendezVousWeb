@extends('layouts.entete-dash')

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header avec bouton Créer -->
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-blue-50">
            <div>
                <h2 class="text-2xl font-bold text-blue-600">Liste des Utilisateurs</h2>
                <p class="text-blue-600 text-sm mt-1">Gérez tous vos utilisateurs</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openModal('createModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>Créer</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-red-50 border-b border-blue-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($liste_users) && $liste_users->count() > 0)
                        @foreach($liste_users as $user)
                        <tr class="@if($loop->even) bg-blue-50 @else bg-white @endif hover:bg-blue-100 transition-colors duration-150">
                            <td class="px-6 py-4 text-blue-600">{{ ($liste_users->currentPage() - 1) * $liste_users->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('images/logo_app_64x64.png') }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                    <span class="font-medium text-blue-600">{{ $user->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-blue-600">{{ $user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span @class([
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    'bg-red-100 text-red-700' => $user->role?->name === 'administrateur',
                                    'bg-green-100 text-green-700' => $user->role?->name === 'secretaire',
                                    'bg-blue-100 text-blue-700' => $user->role?->name === 'responsable',
                                    'bg-gray-100 text-gray-700' => !$user->role || !in_array($user->role->name, [
                                        'administrateur',
                                        'secretaire',
                                        'responsable',
                                    ]),
                                ])>
                                    {{ $user->role ? ucfirst($user->role->name) : 'N/A' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($user->actif) bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    @if($user->actif) Actif
                                    @else Inactif
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openViewModal({{ $user->id }}, '{{ $user->name ?? '' }}', '{{ $user->email ?? '' }}', '{{ $user->role ? $user->role->name : '' }}', {{ $user->actif ? 'true' : 'false' }})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="openEditModal({{ $user->id }}, '{{ $user->name ?? '' }}', '{{ $user->email ?? '' }}', {{ $user->role_id ?? 'null' }}, {{ $user->actif ? 'true' : 'false' }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->name ?? 'cet utilisateur' }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Aucun utilisateur trouvé</p>
                                <p class="text-sm mt-1">Commencez par créer un nouvel utilisateur</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($liste_users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                Affichage de {{ $liste_users->firstItem() }} à {{ $liste_users->lastItem() }} sur {{ $liste_users->total() }} utilisateurs
            </div>
            {{ $liste_users->links('pagination.default') }}
        </div>
        @endif
    </div>

    <!-- Modal Créer -->
    <div id="createModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Créer un Utilisateur</h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('store-personnel') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: Kabongo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: jonathan@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                    <div class="relative">
                        <input type="password" name="password" id="createPassword" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300 pr-12" placeholder="********">
                        <button type="button" onclick="togglePassword('createPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                    <div class="relative">
                        <select name="role_id" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                            <option value="">Sélectionner un rôle</option>
                            @if(isset($roles))
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="actif" id="actif" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-sm"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Actif</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('createModal')" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit" id="createSubmitBtn" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier -->
    <div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Modifier l'Utilisateur</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="" method="POST" id="editForm" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" name="name" id="editName" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="editEmail" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                    <div class="relative">
                        <select name="role_id" id="editRoleId" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                            <option value="">Sélectionner un rôle</option>
                            @if(isset($roles))
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="actif" id="editActif" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-sm"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Actif</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit" id="editSubmitBtn" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Voir -->
    <div id="viewModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Détails de l'Utilisateur</h3>
                <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-2xl" id="viewAvatar">
                        U
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800" id="viewName">Nom</h4>
                        <p class="text-gray-500 text-sm" id="viewRole">Rôle</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-gray-800 font-medium" id="viewEmail">N/A</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Statut</label>
                    <p class="text-gray-800 font-medium" id="viewStatut">N/A</p>
                </div>
                <div class="flex gap-3 pt-4">
                    <button onclick="closeModal('viewModal')" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Disable submit buttons on form submission
        document.addEventListener('DOMContentLoaded', function() {
            const createForm = document.querySelector('#createModal form');
            const editForm = document.querySelector('#editModal form');

            if (createForm) {
                createForm.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('createSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        submitBtn.textContent = 'Création en cours...';
                    }
                });
            }

            if (editForm) {
                editForm.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('editSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        submitBtn.textContent = 'Modification en cours...';
                    }
                });
            }
        });

        function openViewModal(id, name, email, role, actif) {
            document.getElementById('viewAvatar').textContent = name ? name.charAt(0).toUpperCase() : 'U';
            document.getElementById('viewName').textContent = name || 'N/A';
            document.getElementById('viewEmail').textContent = email || 'N/A';
            document.getElementById('viewRole').textContent = role || 'N/A';
            document.getElementById('viewStatut').textContent = actif ? 'Actif' : 'Inactif';
            openModal('viewModal');
        }

        function openEditModal(id, name, email, roleId, actif) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRoleId').value = roleId || '';
            document.getElementById('editActif').checked = actif;
            document.getElementById('editForm').action = '/dashboard/personnels/' + id;
            openModal('editModal');
        }

        function deleteUser(id, name) {
            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Êtes-vous sûr de vouloir supprimer ${name} ?`,
                icon: 'warning',
                iconColor: '#dc2626',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'swal2-confirm-btn',
                    cancelButton: 'swal2-cancel-btn',
                    actions: 'swal2-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/personnels/' + id;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Afficher les alertes SweetAlert après les opérations
        @if(session('success'))
            Swal.fire({
                title: 'Succès',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Erreur',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection

@push('styles')
<style>
    .swal2-confirm-btn {
        background-color: #dc2626 !important;
        color: white !important;
        padding: 0.625rem 1.5rem !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .swal2-confirm-btn:hover {
        background-color: #b91c1c !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    .swal2-cancel-btn {
        background-color: white !important;
        color: #374151 !important;
        border: 1px solid #d1d5db !important;
        padding: 0.625rem 1.5rem !important;
        border-radius: 0.5rem !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
    }
    .swal2-cancel-btn:hover {
        background-color: #f9fafb !important;
    }
    .swal2-actions {
        display: flex !important;
        gap: 0.75rem !important;
    }
    .swal2-actions button {
        flex: 1 !important;
    }
</style>
@endpush