@extends('layouts.entete-dash')

@section('content')
    <div class="rounded-xl shadow-lg overflow-hidden max-w-7xl mx-auto">
        <!-- Header avec bouton Créer -->
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-blue-50 backdrop-blur-sm">
            <div>
                <h2 class="text-2xl font-bold text-blue-600">Liste des Rendez-vous</h2>
                <p class="text-blue-600 text-sm mt-1">Gérez tous vos rendez-vous</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openModal('createModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus"></i>
                    <span>Créer</span>
                </button>
                <button onclick="deleteAllRendezVous()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-trash-alt"></i>
                    <span>Supprimer tous</span>
                </button>
            </div>
        </div>

        <!-- Contenu principal avec tableau et carte photo -->
        <div class="flex flex-col lg:flex-row gap-6 p-6 bg-white/20 backdrop-blur-sm">
            <!-- Tableau -->
            <div class="flex-1 overflow-x-auto bg-white rounded-xl shadow-md">
                <table class="w-full">
                    <thead class="bg-red-50 border-b border-blue-100">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">#</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Titre</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Heure</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Invités</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-blue-600 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($liste_rendez_vous) && $liste_rendez_vous->count() > 0)
                            @foreach($liste_rendez_vous as $rdv)
                            <tr class="@if($loop->even) bg-blue-50 @else bg-white @endif hover:bg-blue-100 transition-colors duration-150">
                                <td class="px-4 py-4 text-blue-600">{{ ($liste_rendez_vous->currentPage() - 1) * $liste_rendez_vous->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-blue-600">{{ $rdv->objet ?? 'N/A' }}</div>
                                    <div class="text-sm text-blue-600 truncate max-w-xs">{{ $rdv->description ?? '' }}</div>
                                </td>
                                <td class="px-4 py-4 text-blue-600">{{ $rdv->date_rendez_vous ? \Carbon\Carbon::parse($rdv->date_rendez_vous)->format('d/m/Y') : 'N/A' }}</td>
                                <td class="px-4 py-4 text-blue-600">{{ $rdv->heure_debut ?? 'N/A' }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('images/logo_app_64x64.png') }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                        <span class="text-blue-600">{{ $rdv->client->nom . ' ' . $rdv->client->prenom ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($rdv->statut == 'confirme') bg-green-100 text-green-700
                                        @elseif($rdv->statut == 'planifie') bg-blue-100 text-blue-700
                                        @elseif($rdv->statut == 'termine') bg-purple-100 text-purple-700
                                        @elseif($rdv->statut == 'annule') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        @if($rdv->statut == 'confirme') Confirmé
                                        @elseif($rdv->statut == 'planifie') Planifié
                                        @elseif($rdv->statut == 'termine') Terminé
                                        @elseif($rdv->statut == 'annule') Annulé
                                        @else {{ ucfirst($rdv->statut ?? 'N/A') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openViewModal({{ $rdv->id }}, '{{ $rdv->objet ?? '' }}', '{{ $rdv->date_rendez_vous ? \Carbon\Carbon::parse($rdv->date_rendez_vous)->format('d/m/Y') : '' }}', '{{ $rdv->heure_debut ?? '' }}', '{{ $rdv->heure_fin ?? '' }}', '{{ $rdv->statut ?? '' }}', '{{ $rdv->description ?? '' }}', '{{ $rdv->client->nom ?? '' }} {{ $rdv->client->prenom ?? '' }}')" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-150" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="openEditModal({{ $rdv->id }}, '{{ $rdv->objet ?? '' }}', '{{ $rdv->date_rendez_vous ?? '' }}', '{{ $rdv->heure_debut ?? '' }}', '{{ $rdv->heure_fin ?? '' }}', '{{ $rdv->statut ?? '' }}', {{ $rdv->chef_id ?? 'null' }}, {{ $rdv->rappel_active ? 'true' : 'false' }}, {{ $rdv->rappel_minutes ?? 30 }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRendezVous({{ $rdv->id }}, '{{ $rdv->objet ?? 'ce rendez-vous' }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                                    <p class="font-medium">Aucun rendez-vous trouvé</p>
                                    <p class="text-sm mt-1">Commencez par créer un nouveau rendez-vous</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Carte photo à côté -->
            <div class="lg:w-1/3 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold text-blue-600 mb-4 text-center">PCA KALLY TSHISEKEDI</h3>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 flex items-center justify-center">
                        <img src="{{ asset('images/background.jpeg') }}" alt="Avatar" class="w-92 h-92 rounded-full object-cover border-4 border-white shadow-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($liste_rendez_vous->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white/90 backdrop-blur-sm">
            <div class="text-sm text-gray-600">
                Affichage de {{ $liste_rendez_vous->firstItem() }} à {{ $liste_rendez_vous->lastItem() }} sur {{ $liste_rendez_vous->total() }} rendez-vous
            </div>
            {{ $liste_rendez_vous->links('pagination.default') }}
        </div>
        @endif
    </div>

    <!-- Modal Créer -->
    <div id="createModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Créer un Rendez-vous</h3>
                <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('rendez-vous.store') }}" method="POST" class="p-4 space-y-2">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Objet</label>
                        <input type="text" name="objet" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: Réunion importante">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date_rendez_vous" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Heure début</label>
                        <input type="time" name="heure_debut" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Heure fin</label>
                        <input type="time" name="heure_fin" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Personne à rencontrer</label>
                        <div class="relative">
                            <select name="client_id" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="">Sélectionner la personne</option>
                                @if(isset($clients))
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->nom }} {{ $client->prenom }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="relative">
                            <select name="statut" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="planifie">Planifié</option>
                                <option value="confirme">Confirmé</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chef</label>
                        <div class="relative">
                            <select name="chef_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="">Sélectionner un chef</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="rappel_active" id="rappelActive" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-sm"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Activer le rappel</span>
                        </label>
                    </div>
                </div>
                <div id="rappelMinutesContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temps de rappel (minutes)</label>
                    <input type="number" name="rappel_minutes" min="1" value="30" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: 30">
                </div>
                <div class="grid grid-cols-2 gap-1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de rendez-vous</label>
                        <div class="relative">
                            <select name="type_rendez_vous" id="type_rendez_vous" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="interieur">Interieur</option>
                                <option value="exterieur">Exterieur</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div> 
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300 resize-none" placeholder="Description du rendez-vous..."></textarea>
                    </div>
                </div>
                <div class="flex gap-1 pt-2">
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
                <h3 class="text-xl font-bold text-gray-800">Modifier le Rendez-vous</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="" method="POST" id="editForm" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Objet</label>
                        <input type="text" name="objet" id="editTitre" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date_rendez_vous" id="editDate" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Heure début</label>
                        <input type="time" name="heure_debut" id="editHeure" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Heure fin</label>
                        <input type="time" name="heure_fin" id="editHeureFin" required class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <div class="relative">
                            <select name="statut" id="editStatut" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="planifie">Planifié</option>
                                <option value="confirme">Confirmé</option>
                                <option value="termine">Terminé</option>
                                <option value="annule">Annulé</option>
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chef</label>
                        <div class="relative">
                            <select name="chef_id" id="editChefId" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none bg-white cursor-pointer hover:border-gray-300">
                                <option value="">Sélectionner un chef</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="rappel_active" id="editRappelActive" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-sm"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Activer le rappel</span>
                    </label>
                </div>
                <div id="editRappelMinutesContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temps de rappel (minutes)</label>
                    <input type="number" name="rappel_minutes" id="editRappelMinutes" min="1" value="30" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 hover:border-gray-300" placeholder="Ex: 30">
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

    <!-- Modal Voir détails -->
    <div id="viewModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Détails du Rendez-vous</h3>
                <button onclick="closeModal('viewModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Objet</label>
                    <p id="viewObjet" class="text-gray-800 font-medium mt-1"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</label>
                        <p id="viewDate" class="text-gray-800 font-medium mt-1"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Heure</label>
                        <p id="viewHeure" class="text-gray-800 font-medium mt-1"></p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</label>
                    <p id="viewClient" class="text-gray-800 font-medium mt-1"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</label>
                    <span id="viewStatut" class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium"></span>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</label>
                    <p id="viewDescription" class="text-gray-800 mt-1"></p>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('viewModal')" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
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

        function openEditModal(id, titre, date, heureDebut, heureFin, statut, chefId, rappelActive, rappelMinutes) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitre').value = titre;
            document.getElementById('editDate').value = date;
            document.getElementById('editHeure').value = heureDebut;
            document.getElementById('editHeureFin').value = heureFin;
            document.getElementById('editStatut').value = statut;
            document.getElementById('editChefId').value = chefId || '';
            document.getElementById('editRappelActive').checked = rappelActive === true || rappelActive === '1';
            document.getElementById('editRappelMinutes').value = rappelMinutes || 30;
            document.getElementById('editRappelMinutesContainer').classList.toggle('hidden', !(rappelActive === true || rappelActive === '1'));
            document.getElementById('editForm').action = '/rendez-vous/' + id;
            openModal('editModal');
        }

        function deleteRendezVous(id, titre) {
            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Êtes-vous sûr de vouloir supprimer ${titre} ?`,
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
                    form.action = '/rendez-vous/' + id;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function deleteAllRendezVous() {
            Swal.fire({
                title: 'Confirmer la suppression',
                text: 'Êtes-vous sûr de vouloir supprimer tous les rendez-vous ? Cette action est irréversible.',
                icon: 'warning',
                iconColor: '#dc2626',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Supprimer tous',
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
                    form.action = '/rendez-vous';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function openViewModal(id, objet, date, heureDebut, heureFin, statut, description, client) {
            document.getElementById('viewObjet').textContent = objet;
            document.getElementById('viewDate').textContent = date;
            document.getElementById('viewHeure').textContent = heureDebut + ' - ' + heureFin;
            document.getElementById('viewClient').textContent = client;
            document.getElementById('viewDescription').textContent = description || 'Aucune description';

            const statutEl = document.getElementById('viewStatut');
            statutEl.textContent = statut === 'confirme' ? 'Confirmé' :
                                 statut === 'planifie' ? 'Planifié' :
                                 statut === 'termine' ? 'Terminé' :
                                 statut === 'annule' ? 'Annulé' : statut;
            statutEl.className = 'inline-block mt-1 px-3 py-1 rounded-full text-xs font-medium ' +
                                 (statut === 'confirme' ? 'bg-green-100 text-green-700' :
                                  statut === 'planifie' ? 'bg-blue-100 text-blue-700' :
                                  statut === 'termine' ? 'bg-purple-100 text-purple-700' :
                                  statut === 'annule' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700');

            openModal('viewModal');
        }

        // Fermer les modals en cliquant à l'extérieur
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        // Fermer avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });

        // Gestion de l'affichage du champ temps de rappel
        document.getElementById('rappelActive').addEventListener('change', function() {
            document.getElementById('rappelMinutesContainer').classList.toggle('hidden', !this.checked);
        });

        document.getElementById('editRappelActive').addEventListener('change', function() {
            document.getElementById('editRappelMinutesContainer').classList.toggle('hidden', !this.checked);
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
    @endpush
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