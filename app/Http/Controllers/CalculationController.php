<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalculationRequest;
use App\Http\Resources\CalculationResource;
use App\Models\Calculation;
use App\Services\BadgeAwarderService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CalculationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Facteurs d'émission (kg CO₂ / unité) — Base Carbone® ADEME / GIEC AR6
    |--------------------------------------------------------------------------
    */

    private const TRANSPORT_FACTORS = [
        'voiture' => 0.218,
        'train'   => 0.009,
        'bus'     => 0.029,
        'avion'   => 0.258,
        'velo'    => 0.000,
        'moto'    => 0.191,
    ];

    private const REGIME_BASE_ANNUAL_KG = [
        'omnivore'    => 1200,
        'flexitarien' => 1000,
        'pescetarien' => 900,
        'vegetarien'  => 800,
        'vegetalien'  => 550,
    ];

    private const VIANDE_FACTOR  = 27.0;
    private const POULET_FACTOR  = 5.7;
    private const POISSON_FACTOR = 6.1;

    private const ELECTRICITE_FACTOR = 0.052;
    private const GAZ_FACTOR         = 0.227;
    private const RENOUVELABLE_RATIO = 0.3;

    private const CONSOMMATION_ANNUAL_KG = [
        'tres_peu' => 500,
        'peu'      => 900,
        'moyen'    => 1400,
        'beaucoup' => 2200,
    ];

    public function __construct(private readonly BadgeAwarderService $badgeAwarder) {}

    /**
     * Calcule les émissions côté serveur et persiste le résultat.
     */
    public function store(StoreCalculationRequest $request): JsonResponse
    {
        $data      = $request->validated();
        $emissions = $this->computeEmissions($data);

        $calculation = Calculation::create([
            'user_id'                 => Auth::id(),
            'transport_voiture'       => $data['transport']['voiture'],
            'transport_train'         => $data['transport']['train'],
            'transport_bus'           => $data['transport']['bus'],
            'transport_avion'         => $data['transport']['avion'],
            'transport_velo'          => $data['transport']['velo'],
            'transport_moto'          => $data['transport']['moto'],
            'alimentation_regime'     => $data['alimentation']['regime'],
            'alimentation_kg_viande'  => $data['alimentation']['kg_viande'],
            'alimentation_kg_poulet'  => $data['alimentation']['kg_poulet'],
            'alimentation_kg_poisson' => $data['alimentation']['kg_poisson'],
            'energie_electricite'     => $data['energie']['electricite'],
            'energie_gaz'             => $data['energie']['gaz'],
            'energie_renouvelable'    => $data['energie']['renouvelable'],
            'consommation_niveau'     => $data['consommation']['niveau'],
            'emissions_transport'     => $emissions['transport'],
            'emissions_alimentation'  => $emissions['alimentation'],
            'emissions_energie'       => $emissions['energie'],
            'emissions_consommation'  => $emissions['consommation'],
            'total_emissions'         => $emissions['total'],
        ]);

        $newBadges = $this->badgeAwarder->checkAndAward(Auth::user()->fresh());

        return response()->json([
            'success'    => true,
            'message'    => 'Calcul sauvegardé avec succès.',
            'data'       => new CalculationResource($calculation),
            'emissions'  => $emissions,
            'new_badges' => $newBadges,
        ], 201);
    }

    /**
     * Historique paginé des calculs de l'utilisateur connecté.
     */
    public function index(): AnonymousResourceCollection
    {
        $calculations = Calculation::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return CalculationResource::collection($calculations);
    }

    /**
     * Dernier calcul de l'utilisateur.
     */
    public function latest(): JsonResponse
    {
        $calculation = Calculation::where('user_id', Auth::id())->latest()->first();

        return response()->json([
            'success' => true,
            'data'    => $calculation ? new CalculationResource($calculation) : null,
        ]);
    }

    /**
     * Tendances mensuelles sur 12 mois pour le graphique du profil.
     */
    public function trends(): JsonResponse
    {
        $monthly = Calculation::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subMonths(12))
            ->orderBy('created_at')
            ->get(['total_emissions', 'created_at'])
            ->groupBy(fn ($c) => $c->created_at->format('Y-m'))
            ->map(fn ($group, $yearMonth) => [
                'month'     => Carbon::createFromFormat('Y-m', $yearMonth)
                                   ->locale('fr')
                                   ->isoFormat('MMM YY'),
                'emissions' => round($group->avg('total_emissions'), 1),
                'count'     => $group->count(),
            ])
            ->values();

        return response()->json(['success' => true, 'data' => $monthly]);
    }

    /**
     * Supprime un calcul appartenant à l'utilisateur connecté.
     */
    public function destroy(int $id): JsonResponse
    {
        Calculation::where('user_id', Auth::id())->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Calcul supprimé.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Calcul des émissions (logique privée)
    |--------------------------------------------------------------------------
    */

    private function computeEmissions(array $data): array
    {
        $transport    = $this->computeTransport($data['transport']);
        $alimentation = $this->computeAlimentation($data['alimentation']);
        $energie      = $this->computeEnergie($data['energie']);
        $consommation = $this->computeConsommation($data['consommation']);

        return [
            'transport'    => round($transport, 2),
            'alimentation' => round($alimentation, 2),
            'energie'      => round($energie, 2),
            'consommation' => round($consommation, 2),
            'total'        => round($transport + $alimentation + $energie + $consommation, 2),
        ];
    }

    private function computeTransport(array $transport): float
    {
        return array_sum(
            array_map(
                fn ($mode) => ($transport[$mode] ?? 0) * (self::TRANSPORT_FACTORS[$mode] ?? 0),
                array_keys(self::TRANSPORT_FACTORS)
            )
        );
    }

    private function computeAlimentation(array $alimentation): float
    {
        $base = (self::REGIME_BASE_ANNUAL_KG[$alimentation['regime']] ?? 1200) / 12;

        return $base
            + $alimentation['kg_viande']  * self::VIANDE_FACTOR
            + $alimentation['kg_poulet']  * self::POULET_FACTOR
            + $alimentation['kg_poisson'] * self::POISSON_FACTOR;
    }

    private function computeEnergie(array $energie): float
    {
        $brut = ($energie['electricite'] * self::ELECTRICITE_FACTOR)
              + ($energie['gaz']         * self::GAZ_FACTOR);

        return $energie['renouvelable'] ? $brut * self::RENOUVELABLE_RATIO : $brut;
    }

    private function computeConsommation(array $consommation): float
    {
        return (self::CONSOMMATION_ANNUAL_KG[$consommation['niveau']] ?? 1400) / 12;
    }
}
