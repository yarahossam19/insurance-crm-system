<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\InsuranceCompany;
use App\Models\Policy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientDeletionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function soft_deleting_a_client_does_not_destroy_their_policy_history(): void
    {
        $client = Client::factory()->create();
        $policy = Policy::factory()->create(['client_id' => $client->id]);

        $client->delete();

        $this->assertSoftDeleted($client);
        // the policy record must survive — it's financial history, not disposable
        $this->assertDatabaseHas('policies', ['id' => $policy->id]);
    }

    #[Test]
    public function force_deleting_a_client_cascades_to_their_policies(): void
    {
        $client = Client::factory()->create();
        $policy = Policy::factory()->create(['client_id' => $client->id]);

        $client->forceDelete();

        $this->assertDatabaseMissing('policies', ['id' => $policy->id]);
    }

    #[Test]
    public function an_insurance_company_cannot_be_deleted_while_it_still_has_policies(): void
    {
        $company = InsuranceCompany::factory()->create();
        Policy::factory()->create(['insurance_company_id' => $company->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $company->delete();
    }
}
