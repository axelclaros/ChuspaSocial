<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_chuspasocial\local;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;

/**
 * Pruebas unitarias del guardado de etiquetas (tags) de las publicaciones.
 *
 * @package    local_chuspasocial
 * @category   test
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_chuspasocial\local\post_service
 */
final class post_tags_test extends advanced_testcase {

    /** @var string Componente de las etiquetas. */
    private const COMPONENT = 'local_chuspasocial';

    /** @var string Tipo de item de las etiquetas (ver db/tag.php). */
    private const ITEMTYPE = 'local_chuspasocial_post';

    /**
     * Configuracion previa a cada test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Crea un usuario autor, inicia sesion con el y crea una publicacion en el muro general.
     *
     * @return \stdClass Registro de la publicacion creada.
     */
    private function create_post(): \stdClass {
        global $DB;

        $author = $this->getDataGenerator()->create_user();
        $this->setUser($author);

        $now = time();
        $post = (object) [
            'userid' => $author->id,
            'courseid' => 0,
            'type' => 'post',
            'content' => 'Vendo mi libro de Calculo I.',
            'contentformat' => FORMAT_HTML,
            'hidden' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $post->id = $DB->insert_record('local_chuspasocial_post', $post);

        return $post;
    }

    /**
     * Guarda las etiquetas de una publicacion mediante el servicio.
     *
     * Unico punto de contacto con la implementacion: si el metodo del servicio tiene otro
     * nombre o firma, solo hay que ajustar esta llamada.
     *
     * @param int $postid Id de la publicacion.
     * @param string[] $tags Nombres de las etiquetas.
     */
    private function save_tags(int $postid, array $tags): void {
        post_service::save_tags($postid, $tags);
    }

    /**
     * Devuelve los nombres de las etiquetas de una publicacion, ordenados.
     *
     * @param int $postid Id de la publicacion.
     * @return string[] Nombres de las etiquetas.
     */
    private function get_tags(int $postid): array {
        $tags = array_values(\core_tag_tag::get_item_tags_array(self::COMPONENT, self::ITEMTYPE, $postid));
        sort($tags);
        return $tags;
    }

    /**
     * Test: al crear una publicacion con etiquetas, estas quedan guardadas.
     */
    public function test_create_post_with_tags(): void {
        $post = $this->create_post();

        $this->save_tags($post->id, ['calculo', 'venta']);

        $this->assertSame(['calculo', 'venta'], $this->get_tags($post->id));
    }

    /**
     * Test: al editar las etiquetas, se reemplazan las anteriores.
     */
    public function test_edit_post_tags(): void {
        $post = $this->create_post();
        $this->save_tags($post->id, ['calculo', 'venta']);

        $this->save_tags($post->id, ['venta', 'fisica']);

        $this->assertSame(['fisica', 'venta'], $this->get_tags($post->id));
    }

    /**
     * Test: al eliminar la publicacion, se eliminan sus etiquetas.
     */
    public function test_delete_post_removes_tags(): void {
        global $DB;

        $post = $this->create_post();
        $this->save_tags($post->id, ['calculo', 'venta']);
        $this->assertCount(2, $this->get_tags($post->id));

        post_service::delete_post($post->id);

        $this->assertSame([], $this->get_tags($post->id));
        $this->assertSame(0, $DB->count_records('tag_instance', [
            'component' => self::COMPONENT,
            'itemtype' => self::ITEMTYPE,
            'itemid' => $post->id,
        ]));
    }
}
