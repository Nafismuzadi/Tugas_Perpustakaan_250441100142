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

    public function test_student_history_renders_empty_state_when_no_borrowings_exist(): void
    {
        $user = new User;
        $user->name = 'Siswa';
        $user->setRelation('roles', collect());

        $this->withoutVite()
            ->actingAs($user)
            ->view('peminjaman.riwayat', ['peminjamans' => collect()])
            ->assertSee('Anda belum pernah mengajukan peminjaman buku.');
    }
}
