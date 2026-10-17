<?php

namespace local_chuspasocial\local\persistent;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Clase persistent para la tabla local_chuspasocial_report.
 *
 * Representa un reporte hecho por un usuario sobre una publicacion,
 * un comentario o un anuncio de libro.
 *
 * @package    local_chuspasocial
 */
class report extends persistent {

    /** @var string Nombre de la tabla asociada a esta clase. */
    const TABLE = 'local_chuspasocial_report';

    /**
     * Define las propiedades de la tabla local_chuspasocial_report.
     *
     * @return array Definicion de cada campo con su tipo PARAM_*, valor por defecto y choices.
     */
    protected static function define_properties(): array {
        return [
            'itemtype' => [
                'type' => PARAM_ALPHA,
                'choices' => ['post', 'comment', 'listing'],
            ],
            'itemid' => [
                'type' => PARAM_INT,
            ],
            'reporterid' => [
                'type' => PARAM_INT,
            ],
            'reason' => [
                'type' => PARAM_TEXT,
            ],
            'status' => [
                'type' => PARAM_ALPHA,
                'default' => 'open',
                'choices' => ['open', 'resolved', 'dismissed'],
            ],
            'resolvedby' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'timeresolved' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
        ];
    }
}
