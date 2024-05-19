<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
        ]);

        $users = User::all();
        $this->admin = $users->where('is_admin', true)->first();
        $this->user = $users->where('is_admin', false)->first();
    }

    /**
     * A basic feature test example.
     */
    public function test_admin_can_get_list_of_users(): void
    {
        $userCount = User::count();

        $response = $this->actingAs($this->admin)
            ->getJson(route('users.index'));

        $response->assertOk();
        $response->assertJsonCount(min($userCount, 15), 'data');
        $response->assertJsonPath('meta.total', $userCount);
    }

    public function test_user_cant_get_list_of_users(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('users.index'));

        $response->assertNotFound();
    }

    public function test_guest_cant_get_list_of_users(): void
    {
        $response = $this->getJson(route('users.index'));

        $response->assertUnauthorized();
    }

    public function test_admin_can_filter_users_by_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('users.index', [
                'email' => $this->user->email,
            ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.email', $this->user->email);
    }

    public function test_admin_can_get_only_admins(): void
    {
        $adminsCount = User::where('is_admin', true)->count();

        $response = $this->actingAs($this->admin)
            ->getJson(route('users.index', [
                'admins' => true,
            ]));

        $response->assertOk();
        $response->assertJsonCount(min($adminsCount, 15), 'data');
        $response->assertJsonPath('meta.total', $adminsCount);
    }
}
