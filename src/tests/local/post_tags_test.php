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
     * Inicia sesion con un usuario autor y crea una publicacion con las etiquetas indicadas.
     *
     * @param string[] $tags Nombres de las etiquetas.
     * @return int Id de la publicacion creada.
     */
    private function create_post_with_tags(array $tags): int {
        $author = $this->getDataGenerator()->create_user();
        $this->setUser($author);

        return post_service::create_post('Vendo mi libro de Calculo I.', FORMAT_HTML, 'post', $tags);
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
        $postid = $this->create_post_with_tags(['calculo', 'venta']);

        $this->assertSame(['calculo', 'venta'], $this->get_tags($postid));
    }

    /**
     * Test: al editar las etiquetas, se reemplazan las anteriores.
     */
    public function test_edit_post_tags(): void {
        $postid = $this->create_post_with_tags(['calculo', 'venta']);

        post_service::update_post($postid, 'Vendo mi libro de Calculo I.', FORMAT_HTML, ['venta', 'fisica']);

        $this->assertSame(['fisica', 'venta'], $this->get_tags($postid));
    }

    /**
     * Test: al eliminar la publicacion, se eliminan sus etiquetas.
     */
    public function test_delete_post_removes_tags(): void {
        global $DB;

        $postid = $this->create_post_with_tags(['calculo', 'venta']);
        $this->assertCount(2, $this->get_tags($postid));

        post_service::delete_post($postid);

        $this->assertSame([], $this->get_tags($postid));
        $this->assertSame(0, $DB->count_records('tag_instance', [
            'component' => self::COMPONENT,
            'itemtype' => self::ITEMTYPE,
            'itemid' => $postid,
        ]));
    }
}
