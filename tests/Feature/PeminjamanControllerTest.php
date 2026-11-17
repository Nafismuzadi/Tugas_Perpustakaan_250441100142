<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PeminjamanControllerTest extends TestCase
{
    public function test_management_page_renders_empty_state_when_no_borrowings_exist(): void
    {
        $user = new User;
        $user->name = 'Petugas';
        $user->setRelation('roles', collect());

        $this->withoutVite()
            ->actingAs($user)
            ->view('peminjaman.index', ['peminjamans' => collect()])
            ->assertSee('Belum ada data pengajuan atau peminjaman.');
    }
}
