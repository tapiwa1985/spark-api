<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserDiscoveryPreference;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use App\Models\DiscoveryPrefLanguage;
use App\Models\DiscoveryPrefInterest;
use App\Models\DiscoveryPrefIndustry;
use App\Repositories\Contracts\UserDiscoveryPreferenceRepositoryInterface;

/**
 * Repository for managing user discovery preferences, including related languages, interests, and industries.
 *
 * @package App\Repositories
 */
class UserDiscoveryPreferenceRepository extends BaseRepository implements UserDiscoveryPreferenceRepositoryInterface
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Constructor.
     *
     * @param UserDiscoveryPreference $model The discovery preference model.
     */
    public function __construct(UserDiscoveryPreference $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new user discovery preference with related pivot data.
     *
     * @param array $data The data array containing preference attributes and ID lists.
     * @return Model The created preference model.
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $userDiscoveryPreference = parent::create([
                'min_age' => $data['min_age'],
                'max_age' => $data['max_age'],
                'max_distance_radius_km' => $data['max_distance_radius_km'],
                'gender' => $data['gender'],
                'user_id' => $data['user_id'],
                'verified_only' => $data['verified_only']
            ]);

            $this->addLanguages($data['languageIds']->toArray(), $userDiscoveryPreference->id);
            $this->addInterests($data['interestIds']->toArray(), $userDiscoveryPreference->id);
            $this->addIndustries($data['industryIds']->toArray(), $userDiscoveryPreference->id);

            return $userDiscoveryPreference;
        });
    }

    /**
     * Attach languages to the discovery preference.
     *
     * @param array $languageIds List of language IDs.
     * @param int $userDiscoveryPreferenceId The preference ID.
     * @return void
     */
    private function addLanguages(array $languageIds, int $userDiscoveryPreferenceId)
    {
        foreach ($languageIds as $languageId) {
            DiscoveryPrefLanguage::create([
                'language_id' => $languageId,
                'user_discovery_preference_id' => $userDiscoveryPreferenceId
            ]);
        }
    }

    /**
     * Attach interests to the discovery preference.
     *
     * @param array $interestIds List of interest IDs.
     * @param int $userDiscoveryPreferenceId The preference ID.
     * @return void
     */
    private function addInterests(array $interestIds, $userDiscoveryPreferenceId): void
    {
        foreach ($interestIds as $interestId) {
            DiscoveryPrefInterest::create([
                'interest_id' => $interestId,
                'user_discovery_preference_id' => $userDiscoveryPreferenceId
            ]);
        }
    }

    /**
     * Attach industries to the discovery preference.
     *
     * @param array $industryIds List of industry IDs.
     * @param int $userDiscoveryPreferenceId The preference ID.
     * @return void
     */
    private function addIndustries(array $industryIds, $userDiscoveryPreferenceId)
    {
        foreach ($industryIds as $industryId) {
            DiscoveryPrefIndustry::create([
                'industry_id' => $industryId,
                'user_discovery_preference_id' => $userDiscoveryPreferenceId
            ]);
        }
    }

    /**
     * Update an existing user discovery preference with its related pivot data.
     *
     * This method updates the core preference fields, removes all existing pivot associations
     * (languages, interests, industries), and recreates them from the provided arrays.
     *
     * @param int $userDiscoveryPreferenceId The ID of the preference to update.
     * @param array $data The data array containing updated attributes and ID lists.
     * @return Model|null The updated preference model, or null if not found.
     */
    public function update(int $userDiscoveryPreferenceId, array $data): ?Model
    {
        return DB::transaction(function () use ($userDiscoveryPreferenceId, $data) {
            $userDiscoveryPreference = parent::update($userDiscoveryPreferenceId, [
                'min_age' => $data['min_age'],
                'max_age' => $data['max_age'],
                'max_distance_radius_km' => $data['max_distance_radius_km'],
                'gender' => $data['gender'],
                'user_id' => $data['user_id'],
                'verified_only' => $data['verified_only']
            ]);

            DiscoveryPrefLanguage::where('user_discovery_preference_id', $userDiscoveryPreferenceId)->delete();
            DiscoveryPrefInterest::where('user_discovery_preference_id', $userDiscoveryPreferenceId)->delete();
            DiscoveryPrefIndustry::where('user_discovery_preference_id', $userDiscoveryPreferenceId)->delete();

            $this->addLanguages($data['languageIds']->toArray(), $userDiscoveryPreferenceId);
            $this->addInterests($data['interestIds']->toArray(), $userDiscoveryPreferenceId);
            $this->addIndustries($data['industryIds']->toArray(), $userDiscoveryPreferenceId);

            return $userDiscoveryPreference;
        });
    }
}
