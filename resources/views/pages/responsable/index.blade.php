@extends('layouts.entete-dash')

@section('title', 'Dashboard')

@section('header-title', 'Dashboard')

<style>
    body {
        background-image: url('images/background.jpeg');
    }
</style>

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6 mb-8">
        <!-- Total Rendez-vous -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-lg p-4 md:p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-xl border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-xs md:text-sm font-medium">Total Rendez-vous</p>
                    <p class="text-2xl md:text-4xl font-bold text-white mt-1 md:mt-2">{{ $totalRendezVous }}</p>
                    <p class="text-gray-300 text-xs mt-1"><i class="fas fa-arrow-up mr-1"></i>+12% ce mois</p>
                </div>
                <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white text-xl md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-lg p-4 md:p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-xl border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-xs md:text-sm font-medium">Total Personnes <br/> Organisation </p>
                    <p class="text-2xl md:text-4xl font-bold text-white mt-1 md:mt-2">{{ $totalClients }}</p>
                    <p class="text-gray-300 text-xs mt-1"><i class="fas fa-arrow-up mr-1"></i>+8% ce mois</p>
                </div>
                <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Rendez-vous Aujourd'hui -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-lg p-4 md:p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-xl border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-xs md:text-sm font-medium">Rendez-vous Aujourd'hui</p>
                    <p class="text-2xl md:text-4xl font-bold text-white mt-1 md:mt-2">{{ $rendezVousAujourdhui }}</p>
                    <p class="text-gray-300 text-xs mt-1"><i class="fas fa-clock mr-1"></i>{{ date('d/m/Y') }}</p>
                </div>
                <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl md:text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- Rendez-vous par statut -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6 hover:shadow-lg transition-all duration-300 border-t-4 border-yellow-400 border border-white/20">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <span class="text-gray-100 text-xs md:text-sm font-medium">Planifiés</span>
                <span class="w-3 h-3 md:w-4 md:h-4 bg-yellow-400 rounded-full animate-pulse"></span>
            </div>
            <p class="text-xl md:text-3xl font-bold text-white">{{ $rendezVousPlanifies }}</p>
            <div class="mt-2 md:mt-3 h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-400 rounded-full" style="width: {{ ($rendezVousPlanifies / max($totalRendezVous, 1)) * 100 }}%"></div>
            </div>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6 hover:shadow-lg transition-all duration-300 border-t-4 border-green-500 border border-white/20">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <span class="text-gray-100 text-xs md:text-sm font-medium">Confirmés</span>
                <span class="w-3 h-3 md:w-4 md:h-4 bg-green-500 rounded-full animate-pulse"></span>
            </div>
            <p class="text-xl md:text-3xl font-bold text-white">{{ $rendezVousConfirmes }}</p>
            <div class="mt-2 md:mt-3 h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width: {{ ($rendezVousConfirmes / max($totalRendezVous, 1)) * 100 }}%"></div>
            </div>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6 hover:shadow-lg transition-all duration-300 border-t-4 border-blue-500 border border-white/20">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <span class="text-gray-100 text-xs md:text-sm font-medium">Terminés</span>
                <span class="w-3 h-3 md:w-4 md:h-4 bg-blue-500 rounded-full animate-pulse"></span>
            </div>
            <p class="text-xl md:text-3xl font-bold text-white">{{ $rendezVousTermines }}</p>
            <div class="mt-2 md:mt-3 h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($rendezVousTermines / max($totalRendezVous, 1)) * 100 }}%"></div>
            </div>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6 hover:shadow-lg transition-all duration-300 border-t-4 border-red-500 border border-white/20">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <span class="text-gray-100 text-xs md:text-sm font-medium">Annulés</span>
                <span class="w-3 h-3 md:w-4 md:h-4 bg-red-500 rounded-full animate-pulse"></span>
            </div>
            <p class="text-xl md:text-3xl font-bold text-white">{{ $rendezVousAnnules }}</p>
            <div class="mt-2 md:mt-3 h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-red-500 rounded-full" style="width: {{ ($rendezVousAnnules / max($totalRendezVous, 1)) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Rendez-vous dans le bureau -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6
            hover:shadow-lg transition-all duration-300 border border-white/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 md:mb-6 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-day text-white text-sm md:text-base"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-white">Rendez-vous interne (Collaborateurs)</h2>
                </div>
                <span class="text-xs md:text-sm text-gray-200 bg-white/20 px-3 py-1 rounded-full text-center sm:text-left">{{ date('d/m/Y') }}</span>
            </div>

            @if($liste_rendez_vous_interieur->count() > 0)
                <div class="space-y-3">
                    @foreach($liste_rendez_vous_interieur as $rdv)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 md:p-4 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 gap-2">
                            <div class="flex items-center space-x-3 md:space-x-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-red-500 to-red-700 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <i class="fas fa-user text-white text-sm md:text-base"></i>
                                </div>
                                <div class="min-w-0 max-w-[200px] md:max-w-[250px]">
                                    <p class="font-semibold text-white text-sm md:text-base truncate">{{ $rdv->client->nom }} {{ $rdv->client->prenom }}</p>
                                    <p class="text-xs md:text-sm text-gray-200 truncate" title="{{ $rdv->objet }}">{{ Str::limit($rdv->objet, 30) }}</p>
                                </div>
                            </div>
                            <div class="flex sm:flex-row sm:items-center justify-between gap-2 w-full sm:w-auto">
                                <p class="font-bold text-white text-sm md:text-base">{{ date('H:i', strtotime($rdv->heure_debut)) }}</p>
                                <span class="inline-block px-2 md:px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    @if($rdv->statut == 'planifie') bg-yellow-100 text-yellow-700
                                    @elseif($rdv->statut == 'confirme') bg-green-100 text-green-700
                                    @elseif($rdv->statut == 'termine') bg-blue-100 text-blue-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($rdv->statut) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($liste_rendez_vous_interieur->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-xs md:text-sm text-gray-600">
                        Affichage de {{ $liste_rendez_vous_interieur->firstItem() }} à {{ $liste_rendez_vous_interieur->lastItem() }} sur {{ $liste_rendez_vous_interieur->total() }}
                    </div>
                    {{ $liste_rendez_vous_interieur->links('pagination.default') }}
                </div>
                @endif
            @else
                <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                        <i class="fas fa-calendar-times text-2xl md:text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm md:text-base">Aucun rendez-vous aujourd'hui</p>
                </div>
            @endif
        </div>

        <!-- Rendez-vous en dehors du bureau -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-md p-4 md:p-6
            hover:shadow-lg transition-all duration-300 border border-white/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 md:mb-6 gap-2">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-white text-sm md:text-base"></i>
                    </div>
                    <h2 class="text-lg md:text-xl font-bold text-white">Rendez-vous externe (Partenaire)</h2>
                </div>
                <a href="#" class="text-white text-xs md:text-sm font-medium hover:underline bg-white/20 px-3 py-1 rounded-full hover:bg-white/30 transition text-center sm:text-left">Voir tout</a>
            </div>

            @if($liste_rendez_vous_exterieur->count() > 0)
                <div class="space-y-3">
                    @foreach($liste_rendez_vous_exterieur as $rdv)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 md:p-4 bg-white/10 backdrop-blur-sm rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 gap-2">
                            <div class="flex items-center space-x-3 md:space-x-4">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <i class="fas fa-user text-white text-sm md:text-base"></i>
                                </div>
                                <div class="min-w-0 max-w-[200px] md:max-w-[250px]">
                                    <p class="font-semibold text-white text-sm md:text-base truncate">{{ $rdv->client->nom }} {{ $rdv->client->prenom }}</p>
                                    <p class="text-xs md:text-sm text-gray-200 truncate" title="{{ $rdv->objet }}">{{ Str::limit($rdv->objet, 30) }}</p>
                                </div>
                            </div>
                            <div class="flex sm:flex-row sm:items-center justify-between gap-2 w-full sm:w-auto">
                                <p class="font-bold text-white text-sm md:text-base">{{ date('H:i', strtotime($rdv->heure_debut)) }}</p>
                                <span class="inline-block px-2 md:px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    @if($rdv->statut == 'planifie') bg-yellow-100 text-yellow-700
                                    @elseif($rdv->statut == 'confirme') bg-green-100 text-green-700
                                    @elseif($rdv->statut == 'termine') bg-blue-100 text-blue-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($rdv->statut) }}
                                </span>
                            </div>
                
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($liste_rendez_vous_exterieur->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-xs md:text-sm text-gray-600">
                        Affichage de {{ $liste_rendez_vous_exterieur->firstItem() }} à {{ $liste_rendez_vous_exterieur->lastItem() }} sur {{ $liste_rendez_vous_exterieur->total() }}
                    </div>
                    {{ $liste_rendez_vous_exterieur->links('pagination.default') }}
                </div>
                @endif
            @else
                <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                        <i class="fas fa-inbox text-2xl md:text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm md:text-base">Aucun rendez-vous récent</p>
                </div>
            @endif
        </div>
    </div>
@endsection
