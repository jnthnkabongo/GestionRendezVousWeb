@extends('layouts.entete-dash')

@section('content')
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header avec bouton Créer -->
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-blue-50">
            <div>
                <h2 class="text-2xl font-bold text-blue-600">Liste des Rôles</h2>
                <p class="text-blue-600 text-sm mt-1">Gérez tous vos rôles et permissions</p>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Utilisateurs</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($liste_roles) && $liste_roles->count() > 0)
                        @foreach($liste_roles as $role)
                        <tr class="@if($loop->even) bg-blue-50 @else bg-white @endif hover:bg-blue-100 transition-colors duration-150">
                            <td class="px-6 py-4 text-blue-600">{{ ($liste_roles->currentPage() - 1) * $liste_roles->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('images/logo_app_64x64.png') }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                    <span class="font-medium text-blue-600">{{ ucfirst($role->name ?? 'N/A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-blue-600">{{ $role->description ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $role->users_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openViewModal({{ $role->id }}, '{{ $role->name ?? '' }}', '{{ $role->description ?? '' }}', {{ $role->users_count ?? 0 }})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="openEditModal({{ $role->id }}, '{{ $role->name ?? '' }}', '{{ $role->description ?? '' }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRole({{ $role->id }}, '{{ ucfirst($role->name ?? 'ce rôle') }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-user-tag text-4xl mb-3 text-gray-300"></i>
                                <p class="font-medium">Aucun rôle trouvé</p>
                                <p class="text-sm mt-1">Commencez par créer un nouveau rôle</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($liste_roles->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                Affichage de {{ $liste_roles->firstItem() }} à {{ $liste_roles->lastItem() }} sur {{ $liste_roles->total() }} rôles
            </div>
            {{ $liste_roles->links('pagination.default') }}
        </div>
        @endif
    </div>

    <!-- Modal Créer -->
    <div id="createModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Créer un Rôle</h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('create-role') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: administrateur">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300 resize-none" placeholder="Ex: Accès complet au système"></textarea>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('createModal')" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
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
                <h3 class="text-xl font-bold text-gray-800">Modifier le Rôle</h3>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="editDescription" rows="3" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
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
                <h3 class="text-xl font-bold text-gray-800">Détails du Rôle</h3>
                <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold text-2xl" id="viewAvatar">
                        R
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800" id="viewName">Nom</h4>
                        <p class="text-gray-500 text-sm">Rôle</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm text-gray-500 mb-1">Description</p>
                    <p class="text-gray-800" id="viewDescription">Description</p>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-sm text-gray-500 mb-1">Nombre d'utilisateurs</p>
                    <p class="text-gray-800 font-semibold" id="viewUsersCount">0</p>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100">
                <button onclick="closeModal('viewModal')" class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                    Fermer
                </button>
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

        function openViewModal(id, name, description, usersCount) {
            document.getElementById('viewAvatar').textContent = name ? name.charAt(0).toUpperCase() : 'R';
            document.getElementById('viewName').textContent = name ? name.charAt(0).toUpperCase() + name.slice(1) : 'N/A';
            document.getElementById('viewDescription').textContent = description || 'Aucune description';
            document.getElementById('viewUsersCount').textContent = usersCount || 0;
            openModal('viewModal');
        }

        function openEditModal(id, name, description) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name || '';
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editForm').action = '/dashboard/roles/' + id;
            openModal('editModal');
        }

        function deleteRole(id, name) {
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
                    form.action = '/dashboard/roles/' + id;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Fermer les modals en cliquant à l'extérieur
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

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