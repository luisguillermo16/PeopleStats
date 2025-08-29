<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Votante;
use App\Models\LugarVotacion;
use App\Models\Barrio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VotanteDuplicadoTest extends TestCase
{
    use RefreshDatabase;

    protected $alcalde;
    protected $concejal1;
    protected $concejal2;
    protected $lider1;
    protected $lider2;
    protected $lugarVotacion;
    protected $barrio;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear alcalde
        $this->alcalde = User::factory()->create();
        $this->alcalde->assignRole('aspirante-alcaldia');

        // Crear concejales
        $this->concejal1 = User::factory()->create(['alcalde_id' => $this->alcalde->id]);
        $this->concejal1->assignRole('aspirante-concejo');

        $this->concejal2 = User::factory()->create(['alcalde_id' => $this->alcalde->id]);
        $this->concejal2->assignRole('aspirante-concejo');

        // Crear líderes
        $this->lider1 = User::factory()->create(['concejal_id' => $this->concejal1->id]);
        $this->lider1->assignRole('lider');

        $this->lider2 = User::factory()->create(['concejal_id' => $this->concejal2->id]);
        $this->lider2->assignRole('lider');

        // Crear lugar de votación
        $this->lugarVotacion = LugarVotacion::factory()->create([
            'alcalde_id' => $this->alcalde->id
        ]);

        // Crear barrio
        $this->barrio = Barrio::factory()->create([
            'alcalde_id' => $this->alcalde->id
        ]);
    }

    /** @test */
    public function no_puede_registrar_votante_duplicado_globalmente()
    {
        // Líder 1 registra un votante
        $this->actingAs($this->lider1);
        
        $votanteData = [
            'nombre' => 'Juan Pérez',
            'cedula' => '123456789',
            'telefono' => '3001234567',
            'mesa_id' => 1,
            'lugar_votacion_id' => $this->lugarVotacion->id,
            'barrio_id' => $this->barrio->id,
        ];

        $response = $this->post('/votantes', $votanteData);
        $response->assertRedirect();
        $this->assertDatabaseHas('votantes', [
            'cedula' => '123456789',
            'lider_id' => $this->lider1->id,
            'alcalde_id' => $this->alcalde->id
        ]);

        // Líder 2 (concejal diferente) intenta registrar el mismo votante
        $this->actingAs($this->lider2);
        
        $response = $this->post('/votantes', $votanteData);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['cedula']);
        
        // Verificar que el mensaje de error sea específico
        $this->assertStringContainsString(
            'Ya fue registrada por el líder',
            session('errors')->first('cedula')
        );
        
        // Verificar que el mensaje mencione "en ninguna campaña"
        $this->assertStringContainsString(
            'en ninguna campaña',
            session('errors')->first('cedula')
        );
    }

    /** @test */
    public function no_puede_registrar_votante_con_misma_cedula_en_concejal_diferente()
    {
        // Líder 1 registra votante en concejal 1
        $this->actingAs($this->lider1);
        
        $votanteData1 = [
            'nombre' => 'Juan Pérez',
            'cedula' => '123456789',
            'telefono' => '3001234567',
            'mesa_id' => 1,
            'lugar_votacion_id' => $this->lugarVotacion->id,
            'barrio_id' => $this->barrio->id,
        ];

        $response = $this->post('/votantes', $votanteData1);
        $response->assertRedirect();

        // Líder 2 (concejal 2) intenta registrar votante con misma cédula
        $this->actingAs($this->lider2);
        
        $votanteData2 = [
            'nombre' => 'Juan Pérez',
            'cedula' => '123456789',
            'telefono' => '3001234567',
            'mesa_id' => 1,
            'lugar_votacion_id' => $this->lugarVotacion->id,
            'barrio_id' => $this->barrio->id,
        ];

        $response = $this->post('/votantes', $votanteData2);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['cedula']);

        // Verificar que solo existe el primer votante
        $this->assertDatabaseHas('votantes', [
            'cedula' => '123456789',
            'alcalde_id' => $this->alcalde->id,
            'concejal_id' => $this->concejal1->id
        ]);

        $this->assertDatabaseMissing('votantes', [
            'cedula' => '123456789',
            'alcalde_id' => $this->alcalde->id,
            'concejal_id' => $this->concejal2->id
        ]);
    }

    /** @test */
    public function no_puede_registrar_votante_con_misma_cedula_en_campaña_diferente()
    {
        // Crear otro alcalde
        $alcalde2 = User::factory()->create();
        $alcalde2->assignRole('aspirante-alcaldia');

        $concejal3 = User::factory()->create(['alcalde_id' => $alcalde2->id]);
        $concejal3->assignRole('aspirante-concejo');

        $lider3 = User::factory()->create(['concejal_id' => $concejal3->id]);
        $lider3->assignRole('lider');

        $lugarVotacion2 = LugarVotacion::factory()->create([
            'alcalde_id' => $alcalde2->id
        ]);

        $barrio2 = Barrio::factory()->create([
            'alcalde_id' => $alcalde2->id
        ]);

        // Líder 1 registra votante en campaña 1
        $this->actingAs($this->lider1);
        
        $votanteData1 = [
            'nombre' => 'Juan Pérez',
            'cedula' => '123456789',
            'telefono' => '3001234567',
            'mesa_id' => 1,
            'lugar_votacion_id' => $this->lugarVotacion->id,
            'barrio_id' => $this->barrio->id,
        ];

        $response = $this->post('/votantes', $votanteData1);
        $response->assertRedirect();

        // Líder 3 intenta registrar votante con misma cédula en campaña 2
        $this->actingAs($lider3);
        
        $votanteData2 = [
            'nombre' => 'Juan Pérez',
            'cedula' => '123456789',
            'telefono' => '3001234567',
            'mesa_id' => 1,
            'lugar_votacion_id' => $lugarVotacion2->id,
            'barrio_id' => $barrio2->id,
        ];

        $response = $this->post('/votantes', $votanteData2);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['cedula']);

        // Verificar que solo existe el primer votante
        $this->assertDatabaseHas('votantes', [
            'cedula' => '123456789',
            'alcalde_id' => $this->alcalde->id
        ]);

        $this->assertDatabaseMissing('votantes', [
            'cedula' => '123456789',
            'alcalde_id' => $alcalde2->id
        ]);
    }
}
