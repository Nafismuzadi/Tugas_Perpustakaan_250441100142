<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_book_with_the_required_catalog_fields(): void
    {
        $category = Kategori::factory()->create();
        $user = $this->userWithBookManagementPermission('Admin');

        $response = $this->actingAs($user)->post(route('buku.store'), [
            'judul' => 'Buku Uji',
            'penulis' => 'Penulis Uji',
            'kategori_id' => (string) $category->id,
            'tahun_terbit' => '2024',
        ]);

        $response->assertRedirect(route('buku.index'));
        $response->assertSessionHas('success', 'Buku berhasil ditambahkan.');
        $this->assertDatabaseHas('bukus', [
            'judul' => 'Buku Uji',
            'penulis' => 'Penulis Uji',
            'kategori_id' => $category->id,
            'tahun_terbit' => 2024,
        ]);
    }

    public function test_petugas_can_create_a_book_with_the_required_catalog_fields(): void
    {
        $category = Kategori::factory()->create();
        $user = $this->userWithBookManagementPermission('Petugas');

        $response = $this->actingAs($user)->post(route('buku.store'), [
            'judul' => 'Buku Petugas',
            'penulis' => 'Penulis Petugas',
            'kategori_id' => (string) $category->id,
            'tahun_terbit' => '2024',
        ]);

        $response->assertRedirect(route('buku.index'));
        $response->assertSessionHas('success', 'Buku berhasil ditambahkan.');
        $this->assertDatabaseHas('bukus', [
            'judul' => 'Buku Petugas',
            'penulis' => 'Penulis Petugas',
            'kategori_id' => $category->id,
            'tahun_terbit' => 2024,
        ]);
    }

    public function test_book_creation_shows_an_error_when_publication_year_is_missing(): void
    {
        $category = Kategori::factory()->create();
        $user = $this->userWithBookManagementPermission('Admin');

        $response = $this->actingAs($user)
            ->from(route('buku.create'))
            ->post(route('buku.store'), [
                'judul' => 'Buku Uji',
                'penulis' => 'Penulis Uji',
                'kategori_id' => $category->id,
            ]);

        $response->assertRedirectBackWithErrors([
            'tahun_terbit' => 'Tahun terbit wajib diisi.',
        ]);
        $this->assertDatabaseMissing('bukus', ['judul' => 'Buku Uji']);
    }

    public function test_admin_can_update_a_book_with_the_required_catalog_fields(): void
    {
        $category = Kategori::factory()->create();
        $book = Buku::factory()->for($category, 'kategori')->create();
        $user = $this->userWithBookManagementPermission('Petugas');

        $response = $this->actingAs($user)->put(route('buku.update', $book), [
            'judul' => 'Judul Baru',
            'penulis' => 'Penulis Baru',
            'kategori_id' => (string) $category->id,
            'tahun_terbit' => '2025',
        ]);

        $response->assertRedirect(route('buku.index'));
        $response->assertSessionHas('success', 'Data buku berhasil diperbarui.');
        $this->assertDatabaseHas('bukus', [
            'id' => $book->id,
            'judul' => 'Judul Baru',
            'penulis' => 'Penulis Baru',
            'kategori_id' => $category->id,
            'tahun_terbit' => 2025,
        ]);
    }

    private function userWithBookManagementPermission(string $roleName): User
    {
        $permission = Permission::create([
            'name' => 'kelola data buku',
            'guard_name' => 'web',
        ]);
        $role = Role::create([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
