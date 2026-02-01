<?php

declare(strict_types=1);

namespace MonarchMoney\Concerns;

use MonarchMoney\GraphQL\Queries\InstitutionQueries;

trait ManagesInstitutions
{
    /**
     * Gets institution data from the account.
     *
     * @return array<string, mixed>
     */
    public function getInstitutions(): array
    {
        return $this->client->query('Web_GetInstitutionSettings', InstitutionQueries::GET_INSTITUTIONS);
    }

    /**
     * The type of subscription for the Monarch Money account.
     *
     * @return array<string, mixed>
     */
    public function getSubscriptionDetails(): array
    {
        return $this->client->query('GetSubscriptionDetails', InstitutionQueries::GET_SUBSCRIPTION_DETAILS);
    }
}
